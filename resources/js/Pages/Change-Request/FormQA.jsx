import { useTranslation } from "react-i18next";
import AppLayout from "../../Layouts/AppLayout";
import Breadcrumb from "../../src/components/ui/Breadcrumb";
import { useEffect, useRef, useState } from "react";
import { Link, useForm, usePage } from "@inertiajs/react";
import ImpactRiskAssesmentForm from "./Partials/FormComponent/ImpactRiskAssesmentForm";
import { notifyError } from "../../src/components/ui/Toastify";
import { Icon } from "@iconify/react/dist/iconify.js";
import HalalAndRegulatoryForm from "./Partials/FormComponent/HalalAndRegulatoryForm";
import Button from "../../src/components/ui/Button";

export default function FormQA({ title, changeRequest }) {
    const { t } = useTranslation();
    const { employee } = usePage().props.auth.user?.employee;
    const { data, setData, post, errors, setError,processing } = useForm({
        change_request_id: changeRequest.id,
        source_of_risk: "",
        impact_of_risk: "",
        severity: 0,
        probability: 0,
        detectability: 0,
        control_implemented: "",
        isLastStep: true,
        decision: "Approved",
    });
    const steps = [
        { id: 1, label: t('impact_risk_assesment'), permission: 'Approve QA SPV' },
    ];

    const [currentStep, setCurrentStep] = useState(1);
    const fieldsetRefs = useRef([]);
    const setFieldsetRef = (el, index) => {
        fieldsetRefs.current[index] = el;
    };
    const { permissions, user } = usePage().props.auth;
    const { flash } = usePage().props;
    let stepNumber = 0;
    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('change-requests.impact-risk-assessment'));
    };
    useEffect(() => {
        if (flash.error) {
            notifyError(flash.error, 'bottom-center');
        }
    }, [flash.error]);
    return (
        <AppLayout>
            <Breadcrumb title={title} subtitle={`${t('change_request')} / ${t('add')}`} />
            <div className="container">
                <div className="card">
                    <div className="card-body">
                        <Link href={route('change-requests.show', changeRequest.id)} className="btn btn-secondary float-end">
                            <Icon icon="mdi:arrow-left" className="me-2 mb-2" width="20" height="20" /> {t('back_to_detail')}
                        </Link>
                        <div className="form-wizard">
                            <form>
                                <div className='form-wizard-header overflow-x-auto scroll-sm pb-8 my-32'>
                                    {/* Step */}
                                    <ul className="list-unstyled form-wizard-list style-two" style={{ justifyContent: 'center' }}>
                                        {steps.map((step, index) => {
                                            stepNumber = index + 1;
                                            return (
                                                permissions.includes(step.permission) && (
                                                    <li key={step.id}
                                                        className={`form-wizard-list__item ${stepNumber < currentStep ? "activated" : ""} ${currentStep === stepNumber ? "active" : ""}`}
                                                        style={{ width: "30%" }}>
                                                        <div className="form-wizard-list__line">
                                                            <span className="count">{stepNumber}</span>
                                                        </div>
                                                        <span className="text text-xs fw-semibold">
                                                            {step.label}
                                                        </span>
                                                    </li>
                                                )
                                            );
                                        })}
                                    </ul>
                                </div>
                                {/* Field */}
                                {steps.map((step, index) => {
                                    stepNumber = index + 1;
                                    return (
                                        <fieldset key={step.id} className={`wizard-fieldset ${currentStep === stepNumber ? "show" : ""}`} ref={(el) => setFieldsetRef(el, index)}>
                                            {currentStep === 1 && <ImpactRiskAssesmentForm  data={data} setData={setData} errors={errors} setError={setError} method={post} />}
                                        </fieldset>
                                    );
                                })}
                                <div className="form-group text-end mt-20">
                                    <Button
                                        onClick={handleSubmit}
                                        className="btn btn-danger"
                                        isLoading={processing}
                                    >
                                        {currentStep === steps.length ? t('submit') : t('next')}
                                    </Button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
