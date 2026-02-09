import { useTranslation } from "react-i18next";
import AppLayout from "../../Layouts/AppLayout";
import Breadcrumb from "../../src/components/ui/Breadcrumb";
import { useEffect, useRef, useState } from "react";
import { useForm, usePage } from "@inertiajs/react";
import ImpactRiskAssesmentForm from "./Partials/FormComponent/ImpactRiskAssesmentForm";
import ChangeInitiationForm from "./Partials/FormComponent/ChangeInitiationForm";
import SelectDepartmentForm from "./Partials/FormComponent/SelectDepartmentForm";
import { notifyError } from "../../src/components/ui/Toastify";
import ImpactOfChangeAssesmentForm from "./Partials/FormComponent/ImpactOfChangeAssesmentForm";
import Button from "../../src/components/ui/Button";
import { Modal } from "react-bootstrap";
import TextInput from "../../src/components/ui/TextInput";
import { confirmAlert } from "../../src/components/ui/SweetAlert";

export default function Form({ title, scopes, departments, products, ...props }) {
    const { t } = useTranslation();
    const { employee } = usePage().props.auth.user;
    const { permissions, user } = usePage().props.auth;
    const { flash } = usePage().props;
    const [modalShow, setModalShow] = useState(false);
    const [isLoading, setIsLoading] = useState(null);
    let stepNumber = 0;

    const { data, setData, post, errors, setError, clearErrors, processing, transform } = useForm({
        id: props?.changeRequest?.id ?? null,
        // Change Initiation
        title: props?.changeRequest?.title ?? '',
        email: props?.changeRequest?.employee?.user?.email ?? user?.email,
        employee_id: props?.changeRequest?.employee_id ?? employee?.id,
        department_id: props?.changeRequest?.department_id ?? employee?.department_id,
        initiator_name: props?.changeRequest?.initiator_name ?? '',
        scopes: props?.changeRequest?.scope_of_change
            ? props.changeRequest.scope_of_change.map(s => s.name)
            : [],
        type_of_change: props?.changeRequest?.type_of_change ? props.changeRequest.type_of_change.map(t => t.type_name) : [],
        current_status: props?.changeRequest?.current_status ?? '',
        current_status_file: [],
        current_status_files_meta: props?.changeRequest?.current_status_files_meta ?? [],

        proposed_change: props?.changeRequest?.proposed_change ?? '',
        proposed_change_file: [],
        proposed_change_files_meta: props?.changeRequest?.proposed_change_files_meta ?? [],
        reason: props?.changeRequest?.reason ?? '',
        expected_completion_date: props?.changeRequest?.expected_completion_date ?? '',
        overall_status: props?.changeRequest?.overall_status ?? null,
        // Supporting Attachments
        supporting_attachment: [],
        supporting_attachments_meta: props?.changeRequest?.supporting_attachments_meta ?? [],

        // Impact Risk Assessment
        source_of_risk: props?.changeRequest?.impact_risk_assesment?.source_of_risk ?? '',
        impact_of_risk: props?.changeRequest?.impact_risk_assesment?.impact_of_risk ?? '',
        cause_of_risk: props?.changeRequest?.impact_risk_assesment?.cause_of_risk ?? '',
        severity: props?.changeRequest?.impact_risk_assesment?.severity ?? 0,
        probability: props?.changeRequest?.impact_risk_assesment?.probability ?? 0,
        detectability: props?.changeRequest?.impact_risk_assesment?.detectability ?? 0,
        rpn: props?.changeRequest?.impact_risk_assesment?.rpn ?? 0,
        control_implemented: props?.changeRequest?.impact_risk_assesment?.control_implemented ?? "",
        risk_category: props?.changeRequest?.impact_risk_assesment?.risk_category ?? "",
        isLastStep: false,

        // Impact of Change Assesment
        facility_affected: props?.changeRequest?.impact_of_change_assesment?.facility_affected ?? null,
        product_affected: props?.changeRequest?.impact_of_change_assesment?.product_affected ?? null,
        affected_products: props?.changeRequest?.affected_products.length ? props?.changeRequest?.affected_products.map(ap => ap.id) : [],
        regulatory_related: props?.changeRequest?.impact_of_change_assesment?.regulatory_related ?? "",
        halal_status: props?.changeRequest?.impact_of_change_assesment?.halal_status ?? "",
        third_party_involved: props?.changeRequest?.impact_of_change_assesment?.third_party_involved ?? null,
        third_party_name: props?.changeRequest?.impact_of_change_assesment?.third_party_name ?? "",


        // Action Plan
        related_departments: props?.changeRequest?.related_departments?.length
            ? props.changeRequest.related_departments.map(rd => ({
                id: rd.id,
                department_id: rd.department_id,
            }))
            : [],

        // Additional
        isDraft: false,

        decision: '',
        comments: '',

    });

    const buildPayload = (data, extra = {}) => ({
        ...data,
        ...extra,

        // ===== CURRENT STATUS =====
        current_status_file: data.current_status_file,
        current_status_file_names: data.current_status_file_names,
        current_status_file_keep_ids: data.current_status_file_keep_ids,

        // ===== PROPOSED CHANGE =====
        proposed_change_file: data.proposed_change_file,
        proposed_change_file_names: data.proposed_change_file_names,
        proposed_change_file_keep_ids: data.proposed_change_file_keep_ids,

        // ===== SUPPORTING =====
        supporting_attachment: data.supporting_attachment,
        supporting_attachment_names: data.supporting_attachment_names,
        supporting_attachment_keep_ids: data.supporting_attachment_keep_ids,
    });


    const steps = [
        { id: 1, label: t('change_initiation'), permission: ['Create Change Control', 'Approve Manager', 'Approve QA SPV'] },
        { id: 2, label: t('impact_risk_assesment'), permission: ['Create Change Control', 'Approve Manager', 'Approve QA SPV'] },
        { id: 3, label: t('impact_of_change_assessment'), permission: ['Create Change Control', 'Approve Manager', 'Approve QA SPV'] },
        { id: 4, label: t('related_departments'), permission: ['Create Change Control', 'Approve Manager', 'Approve QA SPV'] },
    ];

    const [currentStep, setCurrentStep] = useState(1);
    const fieldsetRefs = useRef([]);
    const setFieldsetRef = (el, index) => {
        fieldsetRefs.current[index] = el;
    };
    const stepRoutes = {
        1: "change-requests.change-initiation",
        2: "change-requests.impact-risk-assessment",
        3: "change-requests.impact-of-change-assessment",
        4: "change-requests.store", // Related Departments
    };
    const handleNext = () => {
        transform((data) => buildPayload(data));
        const routeName = stepRoutes[currentStep];
        post(route(routeName), {
            onSuccess: () => {
                if (currentStep < steps.length) {
                    setCurrentStep(currentStep + 1);
                } else {
                    console.log("Form selesai ✅");
                }
            },
            onError: (errors) => {
                console.log("Error:", errors);
            }
        });
    };
    const handleApproveManager = () => {
        setIsLoading("Approved");
        setData('decision', 'Approved');
        transform((data) => buildPayload(data));
        post(route("change-requests.approve.validate", data.id), {
            onSuccess: () => {
                confirmAlert(t('are_you_sure'), t('approve_description'), 'warning', () => {
                    try {
                        post(route("change-requests.approve", data.id), {
                            onSuccess: () => setIsLoading(false),
                            onError: () => setIsLoading(false),
                        });
                    } catch (error) {
                        setIsLoading(false)
                    }
                });
            },
            onError: () => setIsLoading(null),
        });
    };

    const handleSaveDraft = () => {
        transform(data => buildPayload(data, { isDraft: true }));
        post(route("change-requests.save-as-draft"), {
            onSuccess: () => {

            },
            onError: (errors) => {
                console.log("Error:", errors);
            }
        });
    };
    const handleClose = () => {
        setModalShow(false);
        setIsLoading(false)
        clearErrors();
        setData(prev => ({
            ...prev,
            comments: "",
            decision: "",
        }));
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
                        <div className="form-wizard">
                            <form>
                                <div className='form-wizard-header overflow-x-auto scroll-sm pb-8 my-32'>
                                    {/* Step */}
                                    <ul className="list-unstyled form-wizard-list style-two">
                                        {steps.map((step, index) => {
                                            const stepNumber = index + 1;
                                            const isClickable = stepNumber <= currentStep;
                                            return (
                                                permissions.some((perm) => step.permission.includes(perm)) && (
                                                    <li key={step.id}
                                                        className={`form-wizard-list__item ${stepNumber < currentStep ? "activated" : ""} ${currentStep === stepNumber ? "active" : ""}`}
                                                        onClick={() => isClickable && setCurrentStep(stepNumber)}
                                                    >
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
                                            {currentStep === 1 && <ChangeInitiationForm data={data} setData={setData} errors={errors} setError={setError} scopes={scopes} />}
                                            {currentStep === 2 && <ImpactRiskAssesmentForm data={data} setData={setData} errors={errors} setError={setError} />}
                                            {currentStep === 3 && <ImpactOfChangeAssesmentForm data={data} setData={setData} errors={errors} setError={setError} products={products} />}
                                            {currentStep === 4 && <SelectDepartmentForm data={data} setData={setData} errors={errors} setError={setError} departments={departments} />}
                                        </fieldset>
                                    )

                                })}
                                <div className="form-group text-end mt-20">
                                    {currentStep > 1 && (
                                        <Button
                                            type="button"
                                            onClick={() => {
                                                setCurrentStep(currentStep - 1)
                                                console.log(data.product_affected);
                                            }}
                                            className="btn btn-secondary me-2"
                                            disabled={processing}
                                        >
                                            {t('previous')}
                                        </Button>
                                    )}

                                    <Button
                                        type="button"
                                        onClick={handleSaveDraft}
                                        className="btn btn-warning me-2"
                                        isLoading={processing}
                                    >
                                        {t('save_as_draft')}
                                    </Button>


                                    <Button
                                        type="button"
                                        onClick={() => {
                                            if (
                                                currentStep === steps.length &&
                                                permissions.some(p =>
                                                    p === 'Approve Manager' || p === 'Approve QA SPV'
                                                )
                                            ) {
                                                setData('decision', 'Approved');
                                                setModalShow(true);
                                            } else {
                                                handleNext();
                                            }

                                        }}
                                        className="btn btn-danger"
                                        isLoading={processing || isLoading === 'Approved'}
                                    >
                                        {currentStep === steps.length
                                            ? (permissions.includes('Approve Manager') || permissions.includes('Approve QA SPV'))
                                                ? t("approve_and_send_to_next_step")
                                                : t('submit')
                                            : t('next')}
                                    </Button>

                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div >

            {/* Comment Modal */}
            <Modal show={modalShow} onHide={handleClose}>
                <Modal.Header closeButton>
                    <Modal.Title>
                        <h6>{t('enter_attribute', { 'attribute': t('comment') })}</h6>
                    </Modal.Title>
                </Modal.Header>
                <form onSubmit={(e) => {
                    e.preventDefault();
                    handleApproveManager();
                }}>
                    <Modal.Body>
                        <div className="mb-3">
                            <label htmlFor="comments" className="form-label">{t('comment')}</label>
                            <TextInput
                                className="form-control"
                                autoComplete="off"
                                onChange={(e) => setData('comments', e.target.value)}
                                placeholder={t('enter_attribute', { 'attribute': t('comment') })}
                                value={data.comments}
                                errorMessage={errors.comments} />
                        </div>
                    </Modal.Body>
                    <Modal.Footer>
                        <Button type="button" isLoading={false} className="btn btn-sm btn-secondary" onClick={handleClose}>
                            {t('cancel')}
                        </Button>
                        <Button type="submit" isLoading={processing} className="btn btn-sm btn-danger">
                            {t('save')}
                        </Button>
                    </Modal.Footer>
                </form>
            </Modal>
        </AppLayout >
    );
}
