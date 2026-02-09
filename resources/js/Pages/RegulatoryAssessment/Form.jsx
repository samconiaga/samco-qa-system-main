import { useTranslation } from "react-i18next";
import CheckBoxInput from "../../src/components/ui/CheckBoxInput";
import Button from "../../src/components/ui/Button";
import TextInput from "../../src/components/ui/TextInput";
import { Link, useForm, usePage } from "@inertiajs/react";
import AppLayout from "../../Layouts/AppLayout";
import Breadcrumb from "../../src/components/ui/Breadcrumb";
import { Icon } from "@iconify/react/dist/iconify.js";
import { useEffect, useState } from "react";
import { notifyError } from "../../src/components/ui/Toastify";

export default function Form({ changeRequestId, impact_of_change_assessments, regulatory_assessments, status }) {
    const { t } = useTranslation();
    const { auth, flash } = usePage().props;
    const { data, setData, processing, post, transform, errors, setError, clearErrors, reset } = useForm({
        change_request_id: changeRequestId,
        facility_affected: impact_of_change_assessments?.facility_affected || "",
        regulatory_related: impact_of_change_assessments?.regulatory_related || "",
        halal_status: impact_of_change_assessments?.halal_status || "",
        regulatory_change_type: regulatory_assessments?.regulatory_change_type || "",
        regulatory_variation: regulatory_assessments?.regulatory_variation || "",
        reported_by: regulatory_assessments?.reported_by || "",
        notification_date: regulatory_assessments?.notification_date || "",
    });
    const handleSubmit = () => {
        post(route('change-requests.regulatory-assessments.store'), {
            onSuccess: (response) => {

            }
        });
    };
    useEffect(() => {

        if (flash.error) {
            notifyError(flash.error, 'bottom-center');
        }
    }, [flash.error]);
    return (
        <AppLayout>
            <Breadcrumb title={t('regulatory_assessments')} subtitle={`${t("change_request")} / ${t("add")}`} />

            <div className="container">
                <div className="card">
                    <div className="card-body">
                        <div className="d-flex justify-content-end">
                            <Link
                                href={route("change-requests.show", changeRequestId)}
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

                        <div className="row gy-3">
                            <span className="fw-bold d-block">
                                {t("assessment_related_to_registration")}
                            </span>
                            <div className='col-sm-12'>
                                <label className='form-label'>{t('facility_question')}</label>
                                {errors.facility_affected && <small className="text-danger d-block mb-10">{errors.facility_affected}</small>}
                                <div className="form-check style-check d-flex align-items-center">
                                    <CheckBoxInput
                                        type="radio"
                                        id="facility_yes_after_bpom_notification"
                                        label={t('yes_after_bpom_notification')}
                                        errorInput={false}
                                        className="checked-danger border border-neutral-300"
                                        onChange={(e) => {
                                            setData('facility_affected', e.target.value)
                                        }}
                                        checked={data.facility_affected == 'Yes After BPOM Notification'}
                                        value={"Yes After BPOM Notification"}
                                    />
                                </div>
                                <div className="form-check style-check d-flex align-items-center">
                                    <CheckBoxInput
                                        type="radio"
                                        id="facility_yes_bpom_notification_required"
                                        label={t('yes_bpom_notification_required')}
                                        errorInput={false}
                                        className="checked-danger border border-neutral-300"
                                        onChange={(e) => {
                                            setData('facility_affected', e.target.value)
                                        }}
                                        checked={data.facility_affected == 'Yes, BPOM Notification Required'}
                                        value={"Yes, BPOM Notification Required"}
                                    />
                                </div>
                                <div className="form-check style-check d-flex align-items-center">
                                    <CheckBoxInput
                                        type="radio"
                                        id="facility_no"
                                        label={t('facility_no')}
                                        errorInput={false}
                                        className="checked-danger border border-neutral-300"
                                        onChange={(e) => {
                                            setData('facility_affected', e.target.value)
                                        }}
                                        checked={data.facility_affected == 'No'}
                                        value={"No"}
                                    />
                                </div>
                            </div>
                            <div className='col-sm-12'>
                                <label className='form-label'>{t('halal_question')}</label>
                                {errors.halal_status && <small className="text-danger d-block mb-10">{errors.halal_status}</small>}
                                <div className="form-check style-check d-flex align-items-center">
                                    <CheckBoxInput
                                        type="radio"
                                        id="halal_yes_bpjh"
                                        label={t('halal_yes_bpjh')}
                                        errorInput={false}
                                        className="checked-danger border border-neutral-300"
                                        onChange={(e) => {
                                            setData('halal_status', e.target.value)
                                        }}
                                        checked={data.halal_status == 'Yes BPJPH Required'}
                                        value={"Yes BPJPH Required"}
                                    />
                                </div>
                                <div className="form-check style-check d-flex align-items-center">
                                    <CheckBoxInput
                                        type="radio"
                                        id="halal_yes_no_bpjh"
                                        label={t('halal_yes_no_bpjh')}
                                        errorInput={false}
                                        className="checked-danger border border-neutral-300"
                                        onChange={(e) => {
                                            setData('halal_status', e.target.value)
                                        }}
                                        checked={data.halal_status == 'Yes No BPJH'}
                                        value={"Yes No BPJH"}
                                    />
                                </div>
                                <div className="form-check style-check d-flex align-items-center">
                                    <CheckBoxInput
                                        type="radio"
                                        id="halal_no"
                                        label={t('halal_no')}
                                        errorInput={false}
                                        className="checked-danger border border-neutral-300"
                                        onChange={(e) => {
                                            setData('halal_status', e.target.value)
                                        }}
                                        checked={data.halal_status == 'No'}
                                        value={"No"}
                                    />
                                </div>
                            </div>
                            {/* <div className='col-sm-12'>
                                <label className='form-label'>{t('regulatory_assessment')}</label>
                                {errors.regulatory_related && <small className="text-danger d-block mb-10">{errors.regulatory_related}</small>}
                                <div className="form-check style-check d-flex align-items-center">
                                    <CheckBoxInput
                                        type="radio"
                                        id="regulatory_yes"
                                        label={t('regulation_yes')}
                                        errorInput={false}
                                        className="checked-danger border border-neutral-300"
                                        onChange={(e) => {
                                            setData('regulatory_related', e.target.value)
                                        }}
                                        checked={data.regulatory_related == 'Yes'}
                                        value={"Yes"}
                                    />
                                </div>
                                <div className="form-check style-check d-flex align-items-center">
                                    <CheckBoxInput
                                        type="radio"
                                        id="regulatory_no"
                                        label={t('regulation_no')}
                                        errorInput={false}
                                        className="checked-danger border border-neutral-300"
                                        onChange={(e) => {
                                            setData('regulatory_related', e.target.value)
                                        }}
                                        checked={data.regulatory_related == 'No'}
                                        value={"No"}
                                    />
                                </div>
                            </div> */}
                            <div className="col-sm-12">

                                <label className="form-label">
                                    {t("regulatory_change_type")}
                                </label>
                                {errors.regulatory_change_type && (
                                    <small className="text-danger d-block mb-10">{errors.regulatory_change_type}</small>
                                )}
                                {["regulatory_change_type_1", "regulatory_change_type_2", "regulatory_change_type_3"].map((opt) => (
                                    <div className="form-check style-check d-flex align-items-center" key={opt}>
                                        <CheckBoxInput
                                            type="radio"
                                            id={opt}
                                            label={t(opt)}
                                            errorInput={false}

                                            className="checked-danger border border-neutral-300"
                                            onChange={(e) => {
                                                const value = e.target.value;
                                                setData("regulatory_change_type", value);

                                                if (value !== "regulatory_change_type_3") {
                                                    setData("reported_by", "");
                                                    setData("notification_date", "");
                                                }
                                            }}
                                            checked={data.regulatory_change_type === opt}
                                            value={opt}
                                        />
                                    </div>
                                ))}

                                {/* Extra fields kalau pilih type 3 */}
                                {data.regulatory_change_type ===
                                    "regulatory_change_type_3" && (
                                        <>
                                            <div className="col-sm-12">
                                                <label className="form-label">
                                                    {t("reported_by")}
                                                </label>
                                                <TextInput
                                                    value={data.reported_by}
                                                    onChange={(e) =>
                                                        setData("reported_by", e.target.value)
                                                    }
                                                    errorMessage={errors.reported_by}
                                                />
                                            </div>

                                            <div className="col-sm-12">
                                                <label className="form-label">
                                                    {t("notification_date")}
                                                </label>
                                                <TextInput
                                                    type="date"
                                                    value={data.notification_date}
                                                    onChange={(e) =>
                                                        setData("notification_date", e.target.value)
                                                    }
                                                    errorMessage={errors.notification_date}
                                                />
                                            </div>
                                        </>
                                    )}

                            </div>
                            <div className="col-sm-12">
                                <label className="form-label">
                                    {t("regulatory_variation")}
                                </label>
                                {errors.regulatory_variation && <small className="text-danger d-block mb-10">{errors.regulatory_variation}</small>}
                                {["Major", "Minor", "Notification"].map((option) => (
                                    <div className="form-check style-check d-flex align-items-center" key={option}>
                                        <CheckBoxInput
                                            type="radio"
                                            id={`regulatory_variation_${option}`}
                                            label={t(option)}
                                            errorInput={false}
                                            className="checked-danger border border-neutral-300"
                                            onChange={(e) =>
                                                setData("regulatory_variation", e.target.value)
                                            }
                                            checked={data.regulatory_variation == option}
                                            value={option}
                                        />
                                    </div>
                                ))}
                            </div>
                            {status != 'Closed' && (
                                <div className="col-12 text-end mt-4">
                                    <Button
                                        type="submit"
                                        isLoading={processing}
                                        className="btn btn-danger btn-sm"
                                        loadingType={1}
                                        onClick={handleSubmit}
                                    >
                                        {t('save')}
                                    </Button>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>


        </AppLayout >
    );
}
