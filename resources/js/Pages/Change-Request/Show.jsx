import { useTranslation } from "react-i18next";
import AppLayout from "../../Layouts/AppLayout";
import Breadcrumb from "../../src/components/ui/Breadcrumb";
import { useEffect, useState } from "react";
import { Link, router, useForm, usePage } from "@inertiajs/react";
import { Modal, Tab, Tabs } from "react-bootstrap";
import { notifyError, notifySuccess } from "../../src/components/ui/Toastify";
import ChangeInitiationDetail from "./Partials/ShowComponent/ChangeInititaionDetail";
import Button from "../../src/components/ui/Button";
import { Icon } from "@iconify/react/dist/iconify.js";
import ImpactRiskAssessmentDetail from "./Partials/ShowComponent/ImpactRiskAsesmentDetail";
import { route } from "ziggy-js";
import QaRiskAssessmentDetail from "./Partials/ShowComponent/QaRiskAsesmentDetail";
import { confirmAlert } from "../../src/components/ui/SweetAlert";
import TextInput from "../../src/components/ui/TextInput";
import AffectedProduct from "./Partials/ShowComponent/AffectedProduct";
import RelatedDepartmentAssessment from "./Partials/ShowComponent/RelatedDepartmentAssessment";
import RegulatoryAssessmentDetail from "../RegulatoryAssessment/RegulatoryAssessmentDetail";
import Select from "react-select"


export default function Show({ title, changeRequest, ...props }) {
    const { t } = useTranslation();
    const { auth, flash } = usePage().props;
    const [modalShow, setModalShow] = useState(false);
    const { permissions, user } = auth;
    const [isLoading, setIsLoading] = useState(null);
    const [activeTab, setActiveTab] = useState("change-initiation");
    const [refreshTable, setRefreshTable] = useState(false);
    const { data, setData, processing, post, transform, errors, setError, clearErrors } = useForm({
        comments: "",
        decision: "",
        conclusion: ""

    });
    const status = changeRequest.overall_status;
    const canViewDepartmentReviewButton =
        permissions.includes('Approve Manager') &&
        changeRequest?.related_departments?.some(
            rd => rd.department_id === user?.employee?.department_id
        ) && changeRequest.overall_status == "Reviewed" &&
        !changeRequest?.follow_up_implementations?.some(
            f => f.department_id === user?.employee?.department_id
        );
    const approvalPairs = {
        "Pending": "Approve Manager",
        "Waiting QA Manager Approval": "Approve QA Manager",
        "Waiting Plant Manager Approval": "Approve Plant Manager",
        "Waiting SPV Approval": "Approve QA SPV",
    };

    const requiredPermission = approvalPairs[status];

    const canViewApproveRejectButton =
        !!requiredPermission &&
        permissions.includes(requiredPermission) &&
        changeRequest.approvals?.some(
            ({ decision, approver_id }) =>
                decision === "Pending" &&
                approver_id === user?.employee?.id
        );

    const canCloseButton =
        status === "Waiting Close" &&
        (
            (permissions.includes("Approve QA Manager") && changeRequest?.closing?.qa_manager_sign == null) ||
            (permissions.includes("Approve QA SPV") && changeRequest?.closing?.qa_spv_sign == null)
        );
    const handleClose = () => {
        setModalShow(false);
        clearErrors();
        setData({
            comments: "",
            decision: "",
        });
    };

    const handleApproveClick = () => {
        const goToEdit =
            (changeRequest.overall_status === "Waiting SPV Approval" &&
                permissions.includes("Approve QA SPV")) ||
            (changeRequest.overall_status === "Pending" &&
                permissions.includes("Approve Manager"));

        if (goToEdit) {
            router.visit(route('change-requests.edit', changeRequest.id));
            return;
        }

        setData('decision', 'Approved');
        setModalShow(true);
    };

    const handleApprove = (decision) => {
        post(route("change-requests.approve.validate", changeRequest.id), {
            onSuccess: () => {
                confirmAlert(t('are_you_sure'), t('approve_description'), 'warning', () => {
                    try {
                        const targetRoute = route(
                            ["Approved", "Rejected"].includes(decision)
                                ? "change-requests.approve"
                                : "change-requests.review.store",
                            changeRequest.id
                        );
                        transform((data) => ({ ...data, decision }));
                        setIsLoading(decision);
                        post(targetRoute, {
                            onSuccess: () => {
                                setIsLoading(false);
                                setRefreshTable(prev => !prev);
                                handleClose();
                            },
                            onError: () => setIsLoading(false),
                        });
                    } catch (error) {
                        setIsLoading(false);
                    }
                });
            }
        });
    };


    const handleCloseRequest = () => {
        if (permissions.includes('Approve QA SPV')) {
            confirmAlert(t('are_you_sure'), t('close_request_description'), 'warning', () => {
                return router.visit(route("change-requests.qa-approval", changeRequest.id));
            });
        } else {
            setModalShow(true)
        }
    };



    const options = [
        { value: "Can be implemented", label: t("Can be implemented") },
        { value: "Cannot be implemented", label: t("Cannot be implemented") }
    ];

    useEffect(() => {
        if (flash.error) {
            notifyError(flash.error, 'bottom-center');
        } else if (flash.success) {
            notifySuccess(flash.success, 'bottom-center');
        }
    }, [flash.error, flash.success]);
    const STATUS_BADGE_CONFIG = {
        'Pending': {
            className: 'bg-warning',
            label: 'waiting_initiator_manager_approval',
        },
        'In Progress': {
            className: 'bg-warning text-dark',
            label: 'in_progress',
        },
        'Approved': {
            className: 'bg-success',
            label: 'approved',
        },
        'Rejected': {
            className: 'bg-danger',
            label: 'rejected',
        },
        'Reviewed': {
            className: 'bg-info',
            label: 'review_by_relevant_departments_and_prodev',
        },
        'Waiting SPV Approval': {
            className: 'bg-warning',
            label: 'waiting_spv_approval',
        },
        'Waiting QA Manager Approval': {
            className: 'bg-warning',
            label: 'waiting_qa_manager_approval',
        },
        'Waiting Plant Manager Approval': {
            className: 'bg-warning',
            label: 'waiting_plant_manager_approval',
        },
        'Closed': {
            className: 'bg-danger',
            label: 'closed',
        },
    };

    const renderStatusBadge = (status, t) => {
        const config = STATUS_BADGE_CONFIG[status];

        if (!config) {
            return (
                <span className="badge bg-secondary">
                    {status ?? '-'}
                </span>
            );
        }

        return (
            <span className={`badge rounded-pill ${config.className}`}>
                {t(config.label)}
            </span>
        );
    };
    const handlePrint = (id) => {
        window.open(route('change-requests.print', id),
            "_blank"
        );
    };
    return (
        <AppLayout>
            <Breadcrumb title={t('change_request_detail')} subtitle={`${t('change_request')} / ${t('add')}`} />

            <div className="row gy-4">
                <div className="col-lg-12">
                    <div className="user-grid-card position-relative border radius-16 overflow-hidden bg-base h-100">

                        <div className="pb-24 ms-16 mb-24 me-16  mt--100">
                            <div className="text-center border-top-0 border-start-0 border-end-0 mt-50">
                                <img
                                    src="/assets/images/favicon.png"
                                    alt=""
                                    className="border br-white border-width-2-px w-200-px h-200-px rounded-circle object-fit-cover"
                                />
                                <h6 className="mb-0 mt-16">{t('change_request_detail')}</h6>
                                <span className="text-secondary-light mb-8 d-block">{changeRequest.request_number ?? '-'}</span>
                                {renderStatusBadge(changeRequest.overall_status, t)}
                                <div className="mt-20">
                                    {permissions.includes('Review Prodev Manager') && !changeRequest.regulatory && (
                                        <Link
                                            href={route('change-requests.regulatory-assessments.index', { id: changeRequest.id })}
                                            className="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 me-3"
                                        >
                                            <Icon icon="mdi:print-preview" className="menu-icon" />
                                            <span>{t('review')}</span>
                                        </Link>
                                    )}
                                    {changeRequest.overall_status == 'Reviewed' || changeRequest.overall_status == 'In Progress' && (
                                        <Button
                                            className="btn btn-danger btn-sm d-inline-flex align-items-center gap-1"
                                            onClick={() => setActiveTab("related-department-assessment")}
                                        >
                                            <Icon icon="mdi:print-preview" />
                                            {t('related_department_assessment')}
                                        </Button>
                                    )}
                                    {changeRequest.overall_status === 'Closed' && (
                                        <Button
                                            key="print"
                                            type="button"
                                            className="btn btn-sm btn-secondary me-2"
                                            loadingType={2}
                                            onClick={() => handlePrint(changeRequest.id)}
                                        >
                                            <Icon icon="mdi:printer" className="me-2" width="20" height="20" />
                                            {t('print')}
                                        </Button>
                                    )}
                                </div>
                            </div>
                            <div className="d-flex justify-content-end gap-2 mt-40">

                                {canViewDepartmentReviewButton && (
                                    <>
                                        <Button
                                            className="btn btn-success"
                                            onClick={() => {
                                                setData('decision', "Agree")
                                                setModalShow(true)
                                            }}
                                            isLoading={isLoading === 'Agree'}
                                        >
                                            <Icon
                                                icon="mdi:check"
                                                className="me-2 mb-2"
                                                width="20"
                                                height="20"
                                            />
                                            {t('approve')}
                                        </Button>
                                        <Button
                                            className="btn btn-info"
                                            onClick={() => {
                                                setData('decision', "Agree Not Impacted")
                                                setModalShow(true)
                                            }}
                                            isLoading={isLoading === 'Agree Not Impacted'}
                                        >
                                            <Icon
                                                icon="mdi:warning-circle"
                                                className="me-2 mb-2"
                                                width="20"
                                                height="20"
                                            />
                                            {t('agree_not_impacted')}
                                        </Button>
                                        <Button
                                            className="btn btn-danger"
                                            onClick={() => {
                                                setData('decision', "Disagree")
                                                setModalShow(true)
                                            }}
                                            isLoading={isLoading === "Disagree"}
                                        >
                                            <Icon
                                                icon="mdi:warning-circle"
                                                className="me-2 mb-2"
                                                width="20"
                                                height="20"
                                            />
                                            {t('disagree')}
                                        </Button>
                                    </>
                                )}
                                {canViewApproveRejectButton && (
                                    <>
                                        <Button
                                            className="btn btn-success"
                                            onClick={handleApproveClick}
                                            isLoading={isLoading === 'Approved'}
                                        >
                                            <Icon
                                                icon="mdi:check"
                                                className="me-2 mb-2"
                                                width="20"
                                                height="20"
                                            />
                                            {t('approve')}
                                        </Button>
                                        <Button
                                            className="btn btn-danger"
                                            onClick={() => {
                                                setData('decision', "Rejected")
                                                setModalShow(true)
                                            }}
                                            isLoading={isLoading === "Rejected"}
                                        >
                                            <Icon
                                                icon="mdi:warning-circle"
                                                className="me-2 mb-2"
                                                width="20"
                                                height="20"
                                            />
                                            {t('disagree')}
                                        </Button>
                                    </>
                                )}
                                {canCloseButton && (
                                    <Button
                                        className="btn btn-danger"
                                        onClick={handleCloseRequest}
                                        isLoading={isLoading === 'Closed'}
                                    >
                                        <Icon
                                            icon="mdi:stop-remove"
                                            className="me-2 mb-2"
                                            width="20"
                                            height="20"
                                        />
                                        {t('close_request')}
                                    </Button>
                                )}
                            </div>
                            <div className="mt-24">
                                <Tabs
                                    activeKey={activeTab}
                                    onSelect={(k) => setActiveTab(k)}
                                    className="mb-3"
                                >

                                    <Tab eventKey="change-initiation" title={t('change_initiation')}>
                                        <ChangeInitiationDetail changeRequest={changeRequest} />
                                    </Tab>

                                    <Tab eventKey="impact-risk-assesment" title={t('impact_risk_assesment')}>
                                        <ImpactRiskAssessmentDetail
                                            impactRiskAssessment={changeRequest.impact_risk_assesment}
                                            data={data}
                                            setData={setData}
                                            errors={errors}
                                            setError={setError}
                                        />
                                    </Tab>

                                    <Tab eventKey="affected-products" title={t('affected_products')}>
                                        <AffectedProduct changeRequest={changeRequest} />
                                    </Tab>

                                    <Tab eventKey="regulatory-assessments" title={t('regulatory_assessments')}>
                                        <RegulatoryAssessmentDetail changeRequest={changeRequest} />
                                    </Tab>

                                    <Tab
                                        eventKey="related-department-assessment"
                                        title={t('related_department_assessment')}
                                    >
                                        <RelatedDepartmentAssessment
                                            changeRequest={changeRequest}
                                            refreshTable={refreshTable}
                                        />
                                    </Tab>

                                    <Tab eventKey="qa-risk-assesment" title={t('risk_mitigation')}>
                                        <QaRiskAssessmentDetail
                                            riskAssessment={changeRequest.qa_risk_assesment}
                                        />
                                    </Tab>

                                </Tabs>

                            </div>

                        </div>
                    </div>

                </div>
            </div>

            {/* Comment Modal */}
            <Modal show={modalShow} onHide={handleClose}>
                <Modal.Header closeButton>
                    <Modal.Title>
                        <h6>{t('enter_attribute', { 'attribute': t('comment_or_evaluation') })}</h6>
                    </Modal.Title>
                </Modal.Header>
                <form onSubmit={(e) => {
                    e.preventDefault();
                    if (changeRequest?.overall_status === "Waiting Close") {
                        post(route("change-requests.close-request", changeRequest.id), {
                            onSuccess: () => setIsLoading(false),
                            onError: () => setIsLoading(false),
                        });
                    } else {
                        handleApprove(data.decision);
                    }
                }}>
                    <Modal.Body>
                        <div className="mb-3">
                            <label htmlFor="comments" className="form-label">{t('comment_or_evaluation')}</label>
                            {changeRequest?.overall_status === "Waiting Close" ? (
                                <Select
                                    options={options}
                                    value={options.find(option => option.value === data.conclusion) || null}
                                    onChange={(selected) => setData('conclusion', selected?.value || "")}
                                    placeholder={t('Select Conclusion')}
                                    isClearable
                                    styles={{
                                        control: (provided) => ({ ...provided, minHeight: '38px' }),
                                        menu: (provided) => ({ ...provided, zIndex: 9999 })
                                    }}
                                />
                            ) : (
                                <TextInput
                                    className="form-control"
                                    autoComplete="off"
                                    onChange={(e) => setData('comments', e.target.value)}
                                    placeholder={t('enter_attribute', { 'attribute': t('comment_or_evaluation') })}
                                    value={data.comments}
                                    errorMessage={errors.comments}
                                />
                            )}
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
        </AppLayout>
    );
}
