import { useState } from "react";
import AppLayout from "../../Layouts/AppLayout";
import Breadcrumb from "../../src/components/ui/Breadcrumb";
import { useTranslation } from "react-i18next";
import { usePage, useForm, Link } from "@inertiajs/react";
import Button from "../../src/components/ui/Button";
import RelatedDepartmentForm from "../Change-Request/Partials/FormComponent/RelatedDepartmentForm";
import { Icon } from "@iconify/react";
import { notifyError, notifySuccess } from "../../src/components/ui/Toastify";
import FollowUpImplementationTable from "./FollowUpImplementationTable";
import { error } from "jquery";

export default function Index({ impactOfChangeCategories, followUpImplementation }) {
    const { t } = useTranslation();
    const { auth } = usePage().props;
    const permissions = auth.permissions;

    const [formKey, setFormKey] = useState(0);

    const { data, setData, post, reset, errors, clearErrors } = useForm({
        change_request_id: followUpImplementation?.change_request_id || "",
        id: followUpImplementation?.id || "",
        impact_of_change_category: null,
        custom_category: "",
        deadline: "",
        impact_of_change_description: "",
        // realization: "",
    });

    const handleSave = (e) => {
        e.preventDefault();
        post(route("change-requests.follow-up-implementations.store"), {
            onSuccess: (page) => {
                reset();
                const success = page.props?.flash?.success;
                const error = page.props?.flash?.error;
                if (success) {
                    notifySuccess(success, "bottom-center");
                    setFormKey((prev) => prev + 1); // reset form & table reload
                } else {
                    notifyError(error, "bottom-center");
                }

            },
            onError: (page) => {
                notifyError(page.props?.flash?.error, "bottom-center");
            },
        });
    };

    return (
        <AppLayout>
            <Breadcrumb title={t("follow_up_implementation")} subtitle={t("change_requests")} />

            <div className="container">
                {/* Header button */}
                <div className="d-flex justify-content-end mb-3">
                    <Link href={route("change-requests.show", followUpImplementation?.change_request?.id)} className="btn btn-sm btn-secondary">
                        <Icon icon="line-md:arrow-left" className="me-2" width="20" height="20" />
                        {t("back")}
                    </Link>
                </div>

                {/* Related Department Form */}
                {followUpImplementation?.department_id === auth?.user?.employee?.department_id && followUpImplementation?.change_request?.overall_status == 'Reviewed' && (
                    <div className="card mb-4">
                        <div className="card-body">
                            <RelatedDepartmentForm
                                key={formKey}
                                impactOfChangeCategories={impactOfChangeCategories}
                                data={data}
                                setData={setData}
                                errors={errors}
                                clearErrors={clearErrors}
                            />
                            <Button className="btn btn-sm btn-danger mt-3" onClick={handleSave}>
                                {t("save")}
                            </Button>
                        </div>
                    </div>
                )}

                {/* Table */}
                <div className="card">
                    <div className="card-body">
                        <FollowUpImplementationTable
                            impactOfChangeCategories={impactOfChangeCategories}
                            data={data}
                            setData={setData}
                            errors={errors}
                            clearErrors={clearErrors}
                            reset={reset}
                            key={formKey}
                            auth={auth}
                            handleSave={handleSave}
                            permissions={permissions}
                            changeRequestId={followUpImplementation?.change_request_id}
                        />
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
