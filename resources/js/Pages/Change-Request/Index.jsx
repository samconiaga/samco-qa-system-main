import { useTranslation } from "react-i18next";
import AppLayout from "../../Layouts/AppLayout";
import Breadcrumb from "../../src/components/ui/Breadcrumb";
import Button from "../../src/components/ui/Button";
import { Icon } from "@iconify/react/dist/iconify.js";
import { Link, router, useForm, usePage } from "@inertiajs/react";
import { route } from "ziggy-js";
import { useEffect, useState } from "react";
import { notifySuccess } from "../../src/components/ui/Toastify";
import Search from "../../src/components/ui/Datatables/Search";
import Loading from "../../src/components/ui/Datatables/Loading";
import axios from "axios";
import EditButton from "../../src/components/ui/Datatables/EditButton";
import DeleteButton from "../../src/components/ui/Datatables/DeleteButton";
import { confirmAlert } from "../../src/components/ui/SweetAlert";
import ShowButton from "../../src/components/ui/Datatables/ShowButton";
import { toDateString } from "../../helper";
export default function Index({ title }) {
    const { t } = useTranslation();
    const { auth, flash } = usePage().props;
    const { permissions, user } = auth;
    const [isLoading, setIsLoading] = useState(false);
    const { delete: destroy } = useForm({});
    const [tableData, setTableData] = useState([]);
    // Pagination states
    const [totalRows, setTotalRows] = useState(0);
    const [currentPage, setCurrentPage] = useState(1);
    const [rowsPerPage, setRowsPerPage] = useState(10);
    const totalPages = Math.ceil(totalRows / rowsPerPage);
    // Search state
    const [search, setSearch] = useState("");

    const loadTableData = () => {
        setIsLoading(true);
        axios
            .get(route("datatable.change-requests"), {
                params: {
                    page: currentPage,
                    per_page: rowsPerPage,
                    search: search,
                },
            })
            .then((res) => {
                setTableData(res.data.data);
                setTotalRows(res.data.total);
                setIsLoading(false);
            });
    };
    useEffect(() => {
        loadTableData();
    }, [currentPage, rowsPerPage, search]);
    const handleEdit = (id) => {
        router.get(route("change-requests.edit", id));
    };
    const handleShow = (id) => {
        router.get(route("change-requests.show", id));
    };
    const handleDelete = (id) => {
        confirmAlert(
            t("are_you_sure"),
            t("delete_description"),
            "warning",
            () => {
                destroy(route("change-requests.destroy", id), {
                    onStart: () => setIsLoading(true),
                    onSuccess: (page) => {
                        setIsLoading(false);
                        const error = page.props?.flash?.error;
                        const success = page.props?.flash?.success;
                        if (error) {
                            notifyError(error, "bottom-center");
                        } else {
                            notifySuccess(success, "bottom-center");
                            loadTableData();
                        }
                    },
                });
            },
        );
    };

    return (
        <AppLayout>
            <Breadcrumb
                title={t("change_requests")}
                subtitle={`${t("change_requests")}`}
            />
            <div className="container-fluid">
                <div className="d-flex justify-content-end mb-3">
                    {permissions.includes("Create Change Control") && (
                        <Link
                            href={route("change-requests.create")}
                            className="btn btn-sm btn-danger"
                            data-title={t("create_change_request")}
                        >
                            <Icon
                                icon="line-md:plus"
                                className="me-2"
                                width="20"
                                height="20"
                            />
                            {t("create_change_request")}
                        </Link>
                    )}
                </div>
                <div className="card">
                    <div className="card-body">
                        <div className="row">
                            <div className="col-12 d-flex justify-content-end">
                                <div className="col-md-4">
                                    <Search
                                        search={search}
                                        setSearch={setSearch}
                                    />
                                </div>
                            </div>
                            <div className="col-12">
                                <div className="table-responsive">
                                    <table className="table table-bordered table-hover align-middle bg-white">
                                        <thead className="table-light">
                                            <tr>
                                                <th className="text-center">
                                                    No
                                                </th>
                                                <th>{t("request_number")}</th>
                                                <th>{t("title")}</th>
                                                <th className="text-center">
                                                    {t("requested_date")}
                                                </th>
                                                <th>{t("initiator_name")}</th>
                                                <th>{t("department")}</th>
                                                <th className="text-center">
                                                    {t("status")}
                                                </th>
                                                <th className="text-center">
                                                    {t("action")}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {isLoading ? (
                                                <tr>
                                                    <td
                                                        colSpan="9"
                                                        className="text-center"
                                                    >
                                                        <Loading />
                                                    </td>
                                                </tr>
                                            ) : tableData.length === 0 ? (
                                                <tr>
                                                    <td
                                                        colSpan="9"
                                                        className="text-center"
                                                    >
                                                        {t(
                                                            "datatable.emptyTable",
                                                        )}
                                                    </td>
                                                </tr>
                                            ) : (
                                                tableData.map((row, i) => (
                                                    <tr key={row.id}>
                                                        <td>
                                                            {(currentPage - 1) *
                                                                rowsPerPage +
                                                                i +
                                                                1}
                                                        </td>
                                                        <td>
                                                            {
                                                                row?.request_number
                                                            }
                                                        </td>
                                                        <td>{row?.title}</td>
                                                        <td className="text-center">
                                                            {toDateString(
                                                                row?.requested_date,
                                                                false,
                                                            )}
                                                        </td>
                                                        <td>
                                                            {
                                                                row?.initiator_name
                                                            }
                                                        </td>
                                                        <td>
                                                            {
                                                                row?.department
                                                                    ?.short_name
                                                            }
                                                        </td>
                                                        <td className="text-center">
                                                            {(row.overall_status ??
                                                                "-") ===
                                                            "Pending" ? (
                                                                <span className="badge bg-warning text-wrap">
                                                                    {t(
                                                                        "waiting_initiator_manager_approval",
                                                                    )}
                                                                </span>
                                                            ) : (row.overall_status ??
                                                                  "-") ===
                                                              "In Progress" ? (
                                                                <span className="badge bg-warning text-dark text-wrap">
                                                                    {t(
                                                                        "in_progress",
                                                                    )}
                                                                </span>
                                                            ) : (row.overall_status ??
                                                                  "-") ===
                                                              "Approved" ? (
                                                                <span className="badge bg-success text-wrap">
                                                                    {t(
                                                                        "approved",
                                                                    )}
                                                                </span>
                                                            ) : (row.overall_status ??
                                                                  "-") ===
                                                              "Rejected" ? (
                                                                <span className="badge bg-danger text-wrap">
                                                                    {t(
                                                                        "rejected",
                                                                    )}
                                                                </span>
                                                            ) : (row.overall_status ??
                                                                  "-") ===
                                                              "Reviewed" ? (
                                                                <span className="badge bg-info text-wrap">
                                                                    {t(
                                                                        "review_by_relevant_departments_and_prodev",
                                                                    )}
                                                                </span>
                                                            ) : (row.overall_status ??
                                                                  "-") ===
                                                              "Waiting SPV Approval" ? (
                                                                <span className="badge bg-warning text-wrap">
                                                                    {t(
                                                                        "waiting_spv_approval",
                                                                    )}
                                                                </span>
                                                            ) : (row.overall_status ??
                                                                  "-") ===
                                                              "Waiting QA Manager Approval" ? (
                                                                <span className="badge bg-warning text-wrap">
                                                                    {t(
                                                                        "waiting_qa_manager_approval",
                                                                    )}
                                                                </span>
                                                            ) : (row.overall_status ??
                                                                  "-") ===
                                                              "Waiting Plant Manager Approval" ? (
                                                                <span className="badge bg-warning text-wrap">
                                                                    {t(
                                                                        "waiting_plant_manager_approval",
                                                                    )}
                                                                </span>
                                                            ) : (row.overall_status ??
                                                                  "-") ===
                                                              "Closed" ? (
                                                                <span className="badge bg-danger text-wrap">
                                                                    {t(
                                                                        "Closed",
                                                                    )}
                                                                </span>
                                                            ) : (
                                                                <span className="badge bg-secondary text-wrap">
                                                                    {row.overall_status ??
                                                                        "-"}
                                                                </span>
                                                            )}
                                                        </td>
                                                        <td>
                                                            {[
                                                                // Case 1 — Creator bisa edit/hapus kalau Rejected
                                                                [
                                                                    "Rejected",
                                                                    "Draft",
                                                                ].includes(
                                                                    row.overall_status,
                                                                ) &&
                                                                    permissions.includes(
                                                                        "Create Change Control",
                                                                    ) && (
                                                                        <EditButton
                                                                            key="edit"
                                                                            onClick={() =>
                                                                                handleEdit(
                                                                                    row.id,
                                                                                )
                                                                            }
                                                                            isLoading={
                                                                                isLoading
                                                                            }
                                                                        />
                                                                    ),

                                                                row.overall_status ===
                                                                    "Rejected" &&
                                                                    permissions.includes(
                                                                        "Create Change Control",
                                                                    ) && (
                                                                        <DeleteButton
                                                                            key="delete"
                                                                            onClick={() =>
                                                                                handleDelete(
                                                                                    row.id,
                                                                                )
                                                                            }
                                                                            isLoading={
                                                                                isLoading
                                                                            }
                                                                        />
                                                                    ),

                                                                (row.overall_status !==
                                                                    "Draft" ||
                                                                    row.overall_status !==
                                                                        "Rejected") && (
                                                                    // Always show button
                                                                    <ShowButton
                                                                        key="show"
                                                                        onClick={() =>
                                                                            handleShow(
                                                                                row.id,
                                                                            )
                                                                        }
                                                                    />
                                                                ),
                                                            ].filter(Boolean)}
                                                        </td>
                                                    </tr>
                                                ))
                                            )}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            {/* ===========================
                                            PAGINATION
                                        ============================ */}
                            <div className="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <select
                                        className="form-select form-select-sm"
                                        style={{ width: "80px" }}
                                        value={rowsPerPage}
                                        onChange={(e) => {
                                            setRowsPerPage(
                                                Number(e.target.value),
                                            );
                                            setCurrentPage(1);
                                        }}
                                    >
                                        {[10, 20, 30, 50].map((n) => (
                                            <option key={n} value={n}>
                                                {n}
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                <div>
                                    <Button
                                        className="btn btn-sm btn-secondary me-2"
                                        disabled={currentPage === 1}
                                        onClick={() =>
                                            setCurrentPage((prev) => prev - 1)
                                        }
                                    >
                                        {t("previous")}
                                    </Button>

                                    <span>
                                        {currentPage} / {totalPages}
                                    </span>

                                    <Button
                                        className="btn btn-sm btn-danger ms-2"
                                        disabled={currentPage === totalPages}
                                        onClick={() =>
                                            setCurrentPage((prev) => prev + 1)
                                        }
                                    >
                                        {t("next")}
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {/* <div style={{ position: "absolute", top: "-9999px", visibility: "hidden" }}>

                {printData && <ChangeRequestPrint ref={printRef} changeRequest={printData} />}
            </div> */}
        </AppLayout>
    );
}
