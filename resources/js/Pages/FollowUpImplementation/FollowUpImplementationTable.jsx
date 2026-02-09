import { useEffect, useState } from "react";
import { Link, useForm } from "@inertiajs/react";
import axios from "axios";
import DataTable from "react-data-table-component";
import { useTranslation } from "react-i18next";
import { Icon } from "@iconify/react";
import { Modal } from "react-bootstrap";
import { notifySuccess, notifyError } from "../../src/components/ui/Toastify";
import Button from "../../src/components/ui/Button";
import Loading from "../../src/components/ui/Datatables/Loading";
import TextInput from "../../src/components/ui/TextInput";
import { stripHtml, toDateString } from "../../helper";
import { confirmAlert } from "../../src/components/ui/SweetAlert";
import RelatedDepartmentForm from "../Change-Request/Partials/FormComponent/RelatedDepartmentForm";
import DeleteButton from "../../src/components/ui/Datatables/DeleteButton";
import EditButton from "../../src/components/ui/Datatables/EditButton";
import { Inertia } from "@inertiajs/inertia";
import MultipleFileUpload from './../../src/components/ui/MultipleFileUpload';
import ErrorInput from "../../src/components/ui/ErrorInput";

export default function FollowUpImplementationTable({ auth, permissions, changeRequestId, impactOfChangeCategories, data, setData, errors, clearErrors, reset, handleSave }) {
    const { t } = useTranslation();
    // === STATE ===
    const [tableData, setTableData] = useState([]);
    const [totalRows, setTotalRows] = useState(0);
    const [currentPage, setCurrentPage] = useState(1);
    const [rowsPerPage, setRowsPerPage] = useState(10);
    const [isLoading, setIsLoading] = useState(false);
    const [loadingButtonId, setLoadingButtonId] = useState(null);

    // === MODAL ===
    const [show, setShow] = useState(false);
    const [modalType, setModalType] = useState("");
    const [modalTitle, setModalTitle] = useState("");

    // === FORM ===
    const { data: actionPlanData, setData: setActionPlanData, post: actionPlanPost, delete: destroy, processing, reset: actionPlanReset, errors: actionPlanErrors, transform } = useForm({
        id: null,
        deadline: null,
        completion_proof_file: [],
        realization: ""
    });

    // === LOAD DATA ===
    const loadTableData = () => {
        setIsLoading(true);
        axios
            .get(route("datatable.follow-up-implementations", { changeRequest: changeRequestId }), {
                params: { page: currentPage, per_page: rowsPerPage },
            })
            .then((res) => {
                setTableData(res.data.data);
                setTotalRows(res.data.total);
            })
            .catch(() => notifyError(t("error_loading_data")))
            .finally(() => setIsLoading(false));
    };

    useEffect(() => {
        loadTableData();
    }, [currentPage, rowsPerPage]);

    // =========================================================
    // 🧩 UNIVERSAL SUBMIT HANDLER
    // =========================================================
    const submitHandler = (id, { method = "post", routeName, confirmMsg, payload }) => {
        transform((data) => ({
            ...data,
            ...payload,
            ...(id ? { id } : {}),
        }));
        confirmAlert(t("are_you_sure"), confirmMsg, "warning", () => {
            setLoadingButtonId(id);

            const action = method === "put" ? form.put : actionPlanPost;

            action(route(routeName, id), {
                preserveScroll: true,
                onSuccess: (page) => {
                    const message = page.props?.flash?.success ?? page.props?.flash?.error;
                    message && (page.props?.flash?.success ? notifySuccess : notifyError)(message, "bottom-center");
                    loadTableData();
                    handleClose();
                },
                onFinish: () => setLoadingButtonId(null),
            });
        });
    };

    // =========================================================


    // === INDIVIDUAL ACTION WRAPPERS ===
    const handleRequestOverdue = (id) => {
        submitHandler(id, {
            method: "post",
            routeName: "change-requests.overdue-request.store",
            confirmMsg: t("request_overdue_process"),
            payload: {}, // ga perlu data tambahan
        });
    };

    const handleUploadProof = (e) => {
        e.preventDefault();
        submitHandler(actionPlanData.id, {
            method: "post",
            routeName: "change-requests.upload.proof-of-work",
            confirmMsg: t("confirm_upload"),
            payload: { completion_proof_file: actionPlanData.completion_proof_file },
        });
    };

    const handleApproveOverdue = (id) => {
        submitHandler(id, {
            method: "post",
            routeName: "change-requests.overdue-request.approve",
            confirmMsg: t("approve_description"),
            payload: { deadline: actionPlanData.deadline },
        });
    };

    const handleReject = (id, status) => {
        const route = status == "Submitted" ? "change-requests.action-plan.reject" : "change-requests.overdue-request.reject";
        submitHandler(id, {
            method: "post",
            routeName: route,
            confirmMsg: t("reject_description"),
            payload: {},
        });
    };
    const handleApproveActionPlan = (id) => {
        submitHandler(id, {
            method: "post",
            routeName: "change-requests.action-plan.approve",
            confirmMsg: t("approve_description"),
            payload: {},
        });
    };
    const handleEdit = (e) => {
        handleSave(e);
    };
    const handleDelete = (id) => {
        confirmAlert(t('are_you_sure'), t('delete_description'), 'warning', () => {
            destroy(route('change-requests.follow-up-implementations.destroy', id), {
                onStart: () => setIsLoading(true),
                onSuccess: (page) => {
                    setIsLoading(false);
                    const error = page.props?.flash?.error;
                    const success = page.props?.flash?.success;
                    if (error) {
                        notifyError(error, 'bottom-center');
                    } else {
                        notifySuccess(success, 'bottom-center');
                        loadTableData();
                    }
                },
            });
        });
    }

    const handleSavePendingActionPlan = () => {
        submitHandler(
            null, // ❗ id NULL karena seleksi by change_request_id + department_id
            {
                method: "post",
                routeName: "change-requests.action-plan.submit",
                confirmMsg: t("submit_action_plan"),
                payload: {
                    change_request_id: data?.change_request_id,
                    department_id: auth?.user?.employee?.department_id
                },
            }
        )
    }
    // === MODAL HANDLING ===
    const handleShow = (type, id, title) => {
        setModalType(type);
        setModalTitle(title);
        setActionPlanData("id", id);
        setShow(true);
    };

    const handleClose = () => {
        setShow(false);
        reset();
        actionPlanReset();
        setModalType("");
    };

    const handleFollowUpImplementationModalEdit = (type, row, title) => {
        setModalType(type);
        setModalTitle(title);
        const impactValue = row?.impact_category?.impact_of_change_category || "";
        const match = impactOfChangeCategories.find(i => i.name === impactValue);
        setData(prev => ({
            ...prev,
            old_impact_category: impactValue,
            impact_of_change_category: match ? impactValue : null,
            custom_category: match ? "" : impactValue,
            deadline: row?.deadline || "",
            impact_of_change_description: row?.impact_of_change_description,
            action_plan_id: row?.id
        }));
        setShow(true);
    };


    // === TABLE COLUMNS ===
    const getBadgeClass = (status) => {
        const classes = {
            Open: "bg-success",
            Closed: "bg-danger",
            Overdue: "bg-danger text-white",
            "Request Overdue": "bg-primary",
            Pending: "bg-warning text-black",
            Submitted: "bg-info text-dark",
        };

        return classes[status] || "bg-secondary";
    };

    // const COLUMN = [
    //     {
    //         name: "No",
    //         cell: (row, index) => (currentPage - 1) * rowsPerPage + index + 1,
    //         width: "70px",
    //         fixed: "left",
    //     },
    //     {
    //         name: t("impact_of_change_category"),
    //         width: "200px",
    //         fixed: "left",
    //         selector: (row) => row?.impact_category?.impact_of_change_category
    //     },
    //     {
    //         name: t("impact_of_change"),
    //         width: "300px",
    //         fixed: "left",
    //         selector: row => (
    //             <div
    //                 title={row.impact_of_change_description}
    //                 style={{
    //                     whiteSpace: "normal",
    //                     wordBreak: "break-word",
    //                     lineHeight: "1.6",
    //                 }}
    //             >
    //                 {row.impact_of_change_description}
    //             </div>
    //         )

    //     },
    //     {
    //         name: t("PIC"),
    //         width: "200px",
    //         selector: (row) => row?.department?.name
    //     },
    //     // { name: t("officer"), selector: (row) => row?.pic?.name },
    //     {
    //         name: t("deadline"),
    //         width: "150px",
    //         selector: (row) => toDateString(row?.deadline, false)
    //     },
    //     {
    //         name: t("proof_of_work"),
    //         width: "250px",
    //         cell: (row) => {
    //             if (!row?.completion_proof_files?.length) return "-";

    //             return (
    //                 <div style={{ display: "flex", flexDirection: "column", gap: "4px" }}>
    //                     {row.completion_proof_files.map((file, idx) => (
    //                         <a
    //                             key={idx}
    //                             href={`/storage/${file.file_path}`}
    //                             target="_blank"
    //                             rel="noopener noreferrer"
    //                             className="text-primary text-decoration-underline"
    //                         >
    //                             {t("view_file")} {idx + 1}
    //                         </a>
    //                     ))}
    //                 </div>
    //             );
    //         }
    //     },
    //     {
    //         name: t("realization"),
    //         width: "200px",
    //         selector: (row) => row?.realization
    //     },
    //     {
    //         name: t("status"),
    //         width: "150px",
    //         cell: (row) => (
    //             <span className={`badge ${getBadgeClass(row.status ?? 'Pending')} text-center`}>
    //                 {t(row.status) ?? t('pending')}
    //             </span>
    //         ),
    //     },
    //     {
    //         name: t("action"),
    //         cell: (row) => {
    //             const isPIC =
    //                 auth?.user?.employee?.department_id === row.department_id &&
    //                 permissions.includes("PIC Action Plan");
    //             if (["Open", "Overdue"].includes(row.status) && isPIC) {
    //                 return (
    //                     <>
    //                         <Button
    //                             className="btn btn-sm btn-warning me-2"
    //                             onClick={() => handleRequestOverdue(row.id)}
    //                             isLoading={loadingButtonId === row.id}
    //                             loadingType={2}
    //                         >
    //                             <Icon icon="mdi:history" className="me-2" width="18" height="18" />
    //                         </Button>

    //                         <Button
    //                             onClick={() => handleShow("upload", row.id, t("upload_proof_of_work"))}
    //                             className="btn btn-info btn-sm"
    //                             isLoading={loadingButtonId === row.id}
    //                             loadingType={2}
    //                         >
    //                             <Icon icon="mdi:upload" className="me-2" width="18" height="18" />
    //                         </Button>
    //                     </>
    //                 );
    //             }
    //             if (!row.status && isPIC) {
    //                 return (
    //                     <>
    //                         <EditButton key="edit" onClick={() => handleFollowUpImplementationModalEdit("edit", row, t("edit_follow_up_implementation"))} isLoading={isLoading} />
    //                         <DeleteButton key="delete" onClick={() => handleDelete(row.id)} isLoading={isLoading} />
    //                     </>
    //                 );
    //             }

    //             if (["Request Overdue", "Submitted"].includes(row.status) && permissions.includes('Approve QA SPV')) {
    //                 return (
    //                     <>
    //                         <Button
    //                             className="btn btn-success btn-sm me-2"
    //                             onClick={() => {
    //                                 if (row.status === "Request Overdue") {
    //                                     handleShow("approve overdue", row.id, t("overdue_submission"));
    //                                 } else {
    //                                     handleApproveActionPlan(row.id);
    //                                 }
    //                             }}
    //                             isLoading={loadingButtonId === row.id}
    //                             loadingType={2}
    //                         >
    //                             <Icon icon="mdi:check" className="me-2" width="18" height="18" />
    //                         </Button >
    //                         <Button
    //                             className="btn btn-danger btn-sm"
    //                             onClick={() => handleReject(row.id, row.status)}
    //                             isLoading={loadingButtonId === row.id}
    //                             loadingType={2}
    //                         >
    //                             <Icon icon="mdi:alpha-x-circle-outline" className="me-2" width="18" height="18" />
    //                         </Button>
    //                     </>
    //                 );
    //             }
    //         },
    //     },
    // ];

    // === RENDER ===
    const totalPages = Math.ceil(totalRows / rowsPerPage);
    const hasPendingRows = tableData.some(
        (row) =>
            row.department_id === auth?.user?.employee?.department_id &&
            !row.status // status null atau undefined
    );
    return (
        <>
            {/* <div className="datatable-wrapper" style={{ overflowX: "auto" }}>
                <DataTable
                    columns={COLUMN}
                    data={tableData}
                    progressPending={isLoading}
                    progressComponent={<Loading />}
                    noDataComponent={isLoading ? <Loading /> : t('datatable.emptyTable')}
                    pagination
                    paginationServer
                    paginationTotalRows={totalRows}
                    paginationPerPage={rowsPerPage}
                    onChangePage={(page) => setCurrentPage(page)}
                    onChangeRowsPerPage={(newPerPage, page) => {
                        setRowsPerPage(newPerPage);
                        setCurrentPage(page);
                    }}
                    highlightOnHover
                    fixedHeader
                    fixedHeaderScrollHeight="500px"
                    responsive
                    striped
                />
            </div> */}

            {/* ===========================
                MANUAL TABLE
            ============================ */}
            <div
                className="table-responsive table-wrapper"
                style={{
                    overflowX: "auto",
                    overflowY: "hidden"
                }}
            >
                <table className="table table-freeze table-bordered " style={{ minWidth: "1500px" }}>

                    <thead className="table-light"
                        style={{ position: "sticky", top: 0, zIndex: 10 }}>
                        <tr>
                            <th style={{ width: "70px" }}>No</th>
                            <th style={{ width: "200px" }}>{t("impact_of_change_category")}</th>
                            <th style={{ width: "300px" }}>{t("impact_of_change")}</th>
                            <th style={{ width: "200px" }} className="text-center">{t("PIC")}</th>
                            <th style={{ width: "150px" }}>{t("deadline")}</th>
                            <th style={{ width: "200px" }}>{t("proof_of_work")}</th>
                            <th style={{ width: "200px" }}>{t("realization")}</th>
                            <th style={{ width: "150px" }}>{t("status")}</th>
                            <th style={{ width: "200px" }}>{t("action")}</th>
                        </tr>
                    </thead>

                    <tbody>
                        {isLoading ? (
                            <tr>
                                <td colSpan="9" className="text-center">
                                    <Loading />
                                </td>
                            </tr>
                        ) : tableData.length === 0 ? (
                            <tr>
                                <td colSpan="9" className="text-center">
                                    {t("datatable.emptyTable")}
                                </td>
                            </tr>
                        ) : (
                            tableData.map((row, i) => (
                                <tr key={row.id}>
                                    <td>{(currentPage - 1) * rowsPerPage + i + 1}</td>
                                    <td>{row?.impact_category?.impact_of_change_category}</td>
                                    <td>{stripHtml(row?.impact_of_change_description)}</td>
                                    <td className="text-center">{row?.department?.name}</td>
                                    <td>{toDateString(row.deadline, false)}</td>
                                    <td>
                                        {row?.completion_proof_files?.length ?
                                            row.completion_proof_files.map((file, idx) => (
                                                <a key={idx}
                                                    href={`/storage/${file.file_path}`}
                                                    target="_blank"
                                                    className="text-primary">
                                                    {t("view_file")} {idx + 1}
                                                </a>
                                            ))
                                            : "-"
                                        }
                                    </td>
                                    <td>{row.realization || "-"}</td>

                                    <td>
                                        <span className={`badge ${getBadgeClass(row.status)}`}>
                                            {t(row.status)}
                                        </span>
                                    </td>

                                    <td>
                                        {(() => {
                                            const isPIC =
                                                auth?.user?.employee?.department_id === row.department_id &&
                                                permissions.includes("PIC Action Plan");

                                            // Jika status Open / Overdue dan PIC
                                            if (["Open", "Overdue"].includes(row.status) && isPIC) {
                                                return (
                                                    <>
                                                        <Button
                                                            className="btn btn-sm btn-warning me-2"
                                                            onClick={() => handleRequestOverdue(row.id)}
                                                            isLoading={loadingButtonId === row.id}
                                                            loadingType={2}
                                                        >
                                                            <Icon icon="mdi:history" className="me-2" width="18" height="18" />
                                                        </Button>

                                                        <Button
                                                            onClick={() => handleShow("upload", row.id, t("upload_proof_of_work"))}
                                                            className="btn btn-info btn-sm"
                                                            isLoading={loadingButtonId === row.id}
                                                            loadingType={2}
                                                        >
                                                            <Icon icon="mdi:upload" className="me-2" width="18" height="18" />
                                                        </Button>
                                                    </>
                                                );
                                            }

                                            // Jika belum ada status dan PIC
                                            if (!row.status && isPIC) {
                                                return (
                                                    <>
                                                        <EditButton
                                                            key="edit"
                                                            onClick={() =>
                                                                handleFollowUpImplementationModalEdit(
                                                                    "edit",
                                                                    row,
                                                                    t("edit_follow_up_implementation")
                                                                )
                                                            }
                                                            isLoading={isLoading}
                                                        />

                                                        <DeleteButton
                                                            key="delete"
                                                            onClick={() => handleDelete(row.id)}
                                                            isLoading={isLoading}
                                                        />
                                                    </>
                                                );
                                            }

                                            // Jika Request Overdue / Submitted dan QA SPV bisa approve
                                            if (
                                                ["Request Overdue", "Submitted"].includes(row.status) &&
                                                permissions.includes("Approve QA SPV")
                                            ) {
                                                return (
                                                    <>
                                                        <Button
                                                            className="btn btn-success btn-sm me-2"
                                                            onClick={() => {
                                                                if (row.status === "Request Overdue") {
                                                                    handleShow("approve overdue", row.id, t("overdue_submission"));
                                                                } else {
                                                                    handleApproveActionPlan(row.id);
                                                                }
                                                            }}
                                                            isLoading={loadingButtonId === row.id}
                                                            loadingType={2}
                                                        >
                                                            <Icon icon="mdi:check" className="me-2" width="18" height="18" />
                                                        </Button>

                                                        <Button
                                                            className="btn btn-danger btn-sm"
                                                            onClick={() => handleReject(row.id, row.status)}
                                                            isLoading={loadingButtonId === row.id}
                                                            loadingType={2}
                                                        >
                                                            <Icon icon="mdi:alpha-x-circle-outline" className="me-2" width="18" height="18" />
                                                        </Button>
                                                    </>
                                                );
                                            }

                                            return null;
                                        })()}
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>

                </table>
            </div>

            {/* ===========================
                PAGINATION
            ============================ */}
            <div className="d-flex justify-content-between align-items-center mt-3">
                {/* KIRI: Rows per page */}
                <div className="d-flex justify-content-start">
                    <select
                        className="form-select form-select-sm"
                        style={{ width: "80px" }}
                        value={rowsPerPage}
                        onChange={(e) => { setRowsPerPage(Number(e.target.value)); setCurrentPage(1); }}
                    >
                        {[10, 20, 30, 50].map(n => (
                            <option key={n} value={n}>{n}</option>
                        ))}
                    </select>
                </div>

                {/* TENGAH: Pagination */}
                <div className="d-flex justify-content-center align-items-center gap-2">
                    <Button
                        className="btn btn-sm btn-secondary"
                        disabled={currentPage === 1}
                        onClick={() => setCurrentPage(prev => prev - 1)}
                    >
                        {t('previous')}
                    </Button>

                    <span>{currentPage} / {totalPages}</span>

                    <Button
                        className="btn btn-sm btn-danger"
                        disabled={currentPage === totalPages}
                        onClick={() => setCurrentPage(prev => prev + 1)}
                    >
                        {t('next')}
                    </Button>
                </div>

                {/* KANAN: Submit */}
                <div className="d-flex justify-content-end">
                    {hasPendingRows && (
                        <Button
                            className="btn btn-sm btn-primary"
                            disabled={processing}
                            onClick={handleSavePendingActionPlan}
                        >
                            <Icon icon="mdi:send" className="me-2" width="20" height="20" /> {t('submit')}
                        </Button>
                    )}
                </div>
            </div>



            {/* === MODAL === */}
            <Modal show={show} onHide={handleClose} size="lg">
                <Modal.Header closeButton>
                    <h6>{modalTitle}</h6>
                </Modal.Header>

                <form
                    onSubmit={(e) => {
                        e.preventDefault();
                        if (modalType === "upload") {
                            handleUploadProof(e);
                        } else if (modalType == "edit") {
                            handleEdit(e)
                        } else {
                            handleApproveOverdue(actionPlanData.id);
                        }
                    }}
                >

                    <Modal.Body>
                        {modalType === "upload" && (
                            <>
                                <div className="mb-3">
                                    <label className="form-label">{t("realization")}</label>
                                    <TextInput
                                        value={actionPlanData.realization || ""}
                                        onChange={(e) => {
                                            const value = e.target.value;
                                            setActionPlanData("realization", value);
                                        }}
                                        placeholder={t("enter_attribute", {
                                            attribute: t("realization"),
                                        })}
                                        errorMessage={actionPlanErrors?.realization}
                                    />
                                </div>
                                <div className="mb-3">
                                    <label className="form-label">{t("completion_proof_file")}</label>
                                    <MultipleFileUpload
                                        data={actionPlanData}
                                        setData={setActionPlanData}
                                        fieldName="completion_proof_file"
                                        maxFiles={10}
                                        allowedFileTypes={['application/pdf']}
                                    />
                                    {actionPlanErrors.completion_proof_file && <ErrorInput message={actionPlanErrors.completion_proof_file} className="text-danger" />}
                                </div>
                            </>
                        )}

                        {modalType === "approve overdue" && (
                            <div className="mb-3">
                                <label className="form-label">{t("deadline")}</label>
                                <TextInput
                                    type="date"
                                    className="form-control"
                                    autoComplete="off"
                                    value={data.deadline || ""}
                                    onChange={(e) => setActionPlanData("deadline", e.target.value)}
                                    errorMessage={actionPlanErrors.deadline}
                                />
                            </div>
                        )}

                        {modalType === "edit" && (
                            <RelatedDepartmentForm
                                impactOfChangeCategories={impactOfChangeCategories}
                                data={data}
                                setData={setData}
                                errors={errors}
                                clearErrors={clearErrors}
                            />
                        )}
                    </Modal.Body>

                    <Modal.Footer>
                        <Button type="button" className="btn btn-secondary" onClick={handleClose}>
                            {t("cancel")}
                        </Button>
                        <Button type="submit" className="btn btn-danger" isLoading={processing}>
                            {t("save")}
                        </Button>
                    </Modal.Footer>
                </form>
            </Modal>
        </>
    );
}
