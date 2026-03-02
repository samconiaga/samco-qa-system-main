import { useTranslation } from "react-i18next";
import CheckBoxInput from "../../src/components/ui/CheckBoxInput";
import Button from "../../src/components/ui/Button";
import { Link, useForm, usePage } from "@inertiajs/react";
import AppLayout from "../../Layouts/AppLayout";
import Breadcrumb from "../../src/components/ui/Breadcrumb";
import { Icon } from "@iconify/react/dist/iconify.js";
import { useEffect } from "react";
import { notifyError } from "../../src/components/ui/Toastify";

export default function Form({
    changeRequestId,
    impact_of_change_assessments,
}) {
    const { t } = useTranslation();
    const { flash } = usePage().props;
    const { data, setData, processing, post, errors } = useForm({
        change_request_id: changeRequestId,
        is_informed_to_toll_manufacturing:
            impact_of_change_assessments?.is_informed_to_toll_manufacturing,
        is_approval_required_from_toll_manufacturing:
            impact_of_change_assessments?.is_approval_required_from_toll_manufacturing,
    });

    const handleSubmit = () => {
        post(route("change-requests.ppic-assessments.store"), {
            onSuccess: () => {},
        });
    };

    useEffect(() => {
        if (flash.error) {
            notifyError(flash.error, "bottom-center");
        }
    }, [flash.error]);

    return (
        <AppLayout>
            <Breadcrumb
                title={t("ppic_assessment")}
                subtitle={`${t("change_request")} / ${t("add")}`}
            />

            <div className="container">
                <div className="card">
                    <div className="card-body">
                        <div className="d-flex justify-content-end">
                            <Link
                                href={route(
                                    "change-requests.show",
                                    changeRequestId,
                                )}
                                className="btn btn-secondary float-end"
                            >
                                <Icon
                                    icon="mdi:arrow-left"
                                    className="me-2 mb-2"
                                    width="20"
                                    height="20"
                                />
                                {t("back")}
                            </Link>
                        </div>

                        <div className="row mt-4 gy-4">
                            <span className="fw-bold d-block">
                                {t("ppic_assessment")}
                            </span>

                            <div className="col-sm-12">
                                <label className="form-label">
                                    {t("is_informed_to_toll_manufacturing")}
                                </label>
                                {errors.is_informed_to_toll_manufacturing && (
                                    <small className="text-danger d-block mb-10">
                                        {
                                            errors.is_informed_to_toll_manufacturing
                                        }
                                    </small>
                                )}
                                <div className="form-check style-check d-flex align-items-center">
                                    <CheckBoxInput
                                        type="radio"
                                        id="is_informed_yes"
                                        label={t("yes")}
                                        errorInput={false}
                                        className="checked-danger border border-neutral-300"
                                        onChange={() =>
                                            setData(
                                                "is_informed_to_toll_manufacturing",
                                                true,
                                            )
                                        }
                                        checked={
                                            data.is_informed_to_toll_manufacturing ===
                                            true
                                        }
                                        value={"1"}
                                    />
                                </div>
                                <div className="form-check style-check d-flex align-items-center mt-2">
                                    <CheckBoxInput
                                        type="radio"
                                        id="is_informed_no"
                                        label={t("no")}
                                        errorInput={false}
                                        className="checked-danger border border-neutral-300"
                                        onChange={() =>
                                            setData(
                                                "is_informed_to_toll_manufacturing",
                                                false,
                                            )
                                        }
                                        checked={
                                            data.is_informed_to_toll_manufacturing ===
                                            false
                                        }
                                        value={"0"}
                                    />
                                </div>
                            </div>

                            <div className="col-sm-12">
                                <label className="form-label">
                                    {t(
                                        "is_approval_required_from_toll_manufacturing",
                                    )}
                                </label>
                                {errors.is_approval_required_from_toll_manufacturing && (
                                    <small className="text-danger d-block mb-10">
                                        {
                                            errors.is_approval_required_from_toll_manufacturing
                                        }
                                    </small>
                                )}
                                <div className="form-check style-check d-flex align-items-center">
                                    <CheckBoxInput
                                        type="radio"
                                        id="is_approval_yes"
                                        label={t("yes")}
                                        errorInput={false}
                                        className="checked-danger border border-neutral-300"
                                        onChange={() =>
                                            setData(
                                                "is_approval_required_from_toll_manufacturing",
                                                true,
                                            )
                                        }
                                        checked={
                                            data.is_approval_required_from_toll_manufacturing ===
                                            true
                                        }
                                        value={"1"}
                                    />
                                </div>
                                <div className="form-check style-check d-flex align-items-center mt-2">
                                    <CheckBoxInput
                                        type="radio"
                                        id="is_approval_no"
                                        label={t("no")}
                                        errorInput={false}
                                        className="checked-danger border border-neutral-300"
                                        onChange={() =>
                                            setData(
                                                "is_approval_required_from_toll_manufacturing",
                                                false,
                                            )
                                        }
                                        checked={
                                            data.is_approval_required_from_toll_manufacturing ===
                                            false
                                        }
                                        value={"0"}
                                    />
                                </div>
                            </div>

                            <div className="col-12 text-end mt-4">
                                <Button
                                    type="submit"
                                    isLoading={processing}
                                    className="btn btn-danger btn-sm"
                                    loadingType={1}
                                    onClick={handleSubmit}
                                >
                                    {t("save")}
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
