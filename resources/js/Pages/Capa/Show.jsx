import { useTranslation } from "react-i18next";
import AppLayout from "../../Layouts/AppLayout";
import Breadcrumb from "../../src/components/ui/Breadcrumb";
import { useEffect, useState } from "react";
import { Link, router, useForm, usePage } from "@inertiajs/react";
import { notifyError, notifySuccess } from "../../src/components/ui/Toastify";
import Button from "../../src/components/ui/Button";
import { Icon } from "@iconify/react/dist/iconify.js";
import { route } from "ziggy-js";
import { confirmAlert } from "../../src/components/ui/SweetAlert";
import IssueDetail from "./Partials/Detail/IssueDetail";
import IssueResolutionDetail from "./Partials/Detail/IssueResolutionDetail";
import IssueResolutionFileDetail from "./Partials/Detail/IssueResolutionFileDetail";
import { Modal } from "react-bootstrap";
import TextInput from "../../src/components/ui/TextInput";


export default function Show({ issue }) {
    const { t } = useTranslation();
    const { auth, flash } = usePage().props;
    const [modalShow, setModalShow] = useState(false);
    const { user, permissions } = auth;
    const { data, setData, processing, post, errors, clearErrors } = useForm({
        comment: '',
    });
    const canAcceptButton = user?.employee?.department_id === issue?.department_id && issue?.status == 'Open'
    const canApprovetButton = permissions.includes("Approve QA Manager") && issue?.status == 'Submitted'
    const handleAccept = () => {
        confirmAlert(t('are_you_sure'), t('accept_capa_description'), 'warning', () => {
            console.log("Route accept:", route("capa.issue.accept", issue.id));
            try {
                post(route("capa.issue.accept", issue.id), {
                    onSuccess: (page) => {
                        const { success, error } = page.props?.flash ?? {};
                        if (success || error)
                            (success ? notifySuccess : notifyError)(success || error, 'bottom-center');
                    },
                });
            } catch (error) {

            }
        });
    }


    const handleApprove = () => {
        confirmAlert(t('are_you_sure'), t('approve_description'), 'warning', () => {
            try {
                post(route("capa.issue.approve", issue.id), {
                    onSuccess: (page) => {
                        const { success, error } = page.props?.flash ?? {};
                        if (success || error)
                            (success ? notifySuccess : notifyError)(success || error, 'bottom-center');
                    },
                });
            } catch (error) {

            }
        });
    }



    const handleReject = () => {
        post(route("capa.issue.reject", issue.id), {
            onSuccess: (page) => {
                setModalShow(false);
                const { success, error } = page.props?.flash ?? {};
                if (success || error)
                    (success ? notifySuccess : notifyError)(success || error, 'bottom-center');
            },
        });
    }

    const handleClose = () => {
        clearErrors();
        setModalShow(false);
        setData('comment', '')
    }
    useEffect(() => {
        const msg = flash.error || flash.success;
        if (msg) (flash.error ? notifyError : notifySuccess)(msg, 'bottom-center');
    }, [flash.error, flash.success]);
    return (
        <AppLayout>
            <Breadcrumb title={t('change_request_detail')} subtitle={`${t('change_request')} / ${t('add')}`} />

            <div className="row gy-4">
                <div className="col-lg-12">
                    <div className="user-grid-card position-relative border radius-16 overflow-hidden bg-base h-100">

                        <div className="pb-24 ms-16 mb-24 me-16 mt--100">
                            <div className="text-center border-top-0 border-start-0 border-end-0 mt-50">
                                <img
                                    src="/assets/images/favicon.png"
                                    alt=""
                                    className="border br-white border-width-2-px w-200-px h-200-px rounded-circle object-fit-cover"
                                />

                                <h6 className="mb-0 mt-16">{t('issue_detail')}</h6>
                                <span className="text-secondary-light mb-8 d-block">
                                    {issue.issue_number ?? '-'}
                                </span>
                                <span
                                    className={`badge rounded-pill ${issue.status === 'Open'
                                        ? 'bg-primary'
                                        : issue.status === 'In Progress'
                                            ? 'bg-warning text-dark'
                                            : issue.status === 'Rejected'
                                                ? 'bg-danger'
                                                : issue.status === 'Submitted'
                                                    ? 'bg-info text-dark'
                                                    : issue.status === 'Resolved'
                                                        ? 'bg-success'
                                                        : 'bg-secondary'
                                        } mb-3`}
                                >
                                    {t(issue.status) ?? 'Unknown'}
                                </span>
                                <div>
                                    {user?.employee?.department_id == issue?.department_id && (issue?.status == 'In Progress' || issue?.status == 'Rejected') && (
                                        <Link
                                            href={route('capa.resolution.index', { id: issue.id })}
                                            className="btn btn-sm btn-primary d-inline-flex align-items-center gap-1"
                                        >
                                            <Icon icon="mdi:tools" className="menu-icon" />
                                            <span>{t('resolve_issue')}</span>
                                        </Link>
                                    )}
                                </div>
                            </div>

                            <div className="mt-24">
                                <ul
                                    className="nav border-gradient-tab nav-pills mb-20 d-inline-flex"
                                    id="pills-tab"
                                    role="tablist"
                                >
                                    <li className="nav-item" role="presentation">
                                        <button
                                            className="nav-link d-flex align-items-center px-24 active"
                                            id="issue-detail-tab"
                                            data-bs-toggle="pill"
                                            data-bs-target="#issue-detail"
                                            type="button"
                                            role="tab"
                                            aria-controls="issue-detail"
                                            aria-selected="true"
                                        >
                                            {t('issue_detail')}
                                        </button>
                                    </li>
                                    <li className="nav-item" role="presentation">
                                        <button
                                            className="nav-link d-flex align-items-center px-24"
                                            id="issue-resolution-detail-tab"
                                            data-bs-toggle="pill"
                                            data-bs-target="#issue-resolution-detail"
                                            type="button"
                                            role="tab"
                                            aria-controls="issue-resolution-detail"
                                            aria-selected="true"
                                        >
                                            {t('issue_resolution')}
                                        </button>
                                    </li>

                                    <li className="nav-item" role="presentation">
                                        <button
                                            className="nav-link d-flex align-items-center px-24"
                                            id="issue-resolution-file-detail-tab"
                                            data-bs-toggle="pill"
                                            data-bs-target="#issue-resolution-file-detail"
                                            type="button"
                                            role="tab"
                                            aria-controls="issue-resolution-file-detail"
                                            aria-selected="false"
                                            tabIndex={-1}
                                        >
                                            {t('issue_resolution_file')}
                                        </button>
                                    </li>
                                </ul>
                                <div className="tab-content" id="pills-tabContent">
                                    <IssueDetail issue={issue} />
                                    <IssueResolutionDetail issue={issue} />
                                    <IssueResolutionFileDetail issue={issue} />
                                </div>
                            </div>
                            <div className="d-flex justify-content-end gap-2 mt-20">

                                {canAcceptButton && (
                                    <>
                                        <Button
                                            type="button"
                                            className="btn btn-success"
                                            onClick={handleAccept}
                                            isLoading={processing}
                                        >
                                            <Icon
                                                icon="mdi:check"
                                                className="me-2 mb-2"
                                                width="20"
                                                height="20"
                                            />
                                            {t('accept_it_and_do_it')}
                                        </Button>
                                    </>
                                )}
                                {canApprovetButton && (
                                    <>
                                        <Button
                                            type="button"
                                            className="btn btn-success"
                                            onClick={handleApprove}
                                            isLoading={processing}
                                        >
                                            <Icon
                                                icon="mdi:check"
                                                className="me-2 mb-2"
                                                width="20"
                                                height="20"
                                            />
                                            {t('resolve')}
                                        </Button>
                                        <Button
                                            type="button"
                                            className="btn btn-danger"
                                            onClick={() => {
                                                setModalShow(true)
                                            }}
                                            isLoading={processing}
                                        >
                                            <Icon
                                                icon="mdi:warning-circle"
                                                className="me-2 mb-2"
                                                width="20"
                                                height="20"
                                            />
                                            {t('reject')}
                                        </Button>
                                    </>
                                )}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            {/* Comment Modal */}
            <Modal show={modalShow} onHide={handleClose}>
                <Modal.Header closeButton>
                    <Modal.Title>
                        <h6>{t('enter_attribute', { 'attribute': t('comment') })}</h6>
                    </Modal.Title>
                </Modal.Header>
                <form onSubmit={(e) => {
                    e.preventDefault();
                    handleReject(data.status)
                }}>
                    <Modal.Body>
                        <div className="mb-3">
                            <label htmlFor="comments" className="form-label">{t('comment')}</label>
                            <TextInput
                                className="form-control"
                                autoComplete="off"
                                onChange={(e) => setData('comment', e.target.value)}
                                placeholder={t('enter_attribute', { 'attribute': t('comment') })}
                                value={data.comment}
                                errorMessage={errors.comment} />
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
