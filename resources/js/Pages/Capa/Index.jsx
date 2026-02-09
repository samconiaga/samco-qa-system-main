import { useTranslation } from "react-i18next";
import AppLayout from './../../Layouts/AppLayout';
import Breadcrumb from "../../src/components/ui/Breadcrumb";
import { Link, router, usePage } from "@inertiajs/react";
import { Icon } from "@iconify/react/dist/iconify.js";
import { notifyError, notifySuccess } from "../../src/components/ui/Toastify";
import { useEffect, useRef, useState } from "react";
import axios from "axios";
import { toDateTimeString } from "../../helper";
import EditButton from "../../src/components/ui/Datatables/EditButton";
import DeleteButton from "../../src/components/ui/Datatables/DeleteButton";
import ShowButton from "../../src/components/ui/Datatables/ShowButton";
import DataTable from "react-data-table-component";
import Search from "../../src/components/ui/Datatables/Search";
import Loading from "../../src/components/ui/Datatables/Loading";
import { route } from "ziggy-js";
import PrintButton from "../../src/components/ui/Datatables/PrintButton";
import IssuePrint from "../Print/Capa/IssuePrint";

export default function Index() {
    const { t } = useTranslation();
    const { auth, flash } = usePage().props;
    const { permissions, user } = auth;
    const [isLoading, setIsLoading] = useState(false);
    const [printData, setPrintData] = useState(null);
    const printRef = useRef();
    const [tableData, setTableData] = useState([]);
    // Pagination states
    const [totalRows, setTotalRows] = useState(0);
    const [currentPage, setCurrentPage] = useState(1);
    const [rowsPerPage, setRowsPerPage] = useState(10);
    // Search state
    const [search, setSearch] = useState('');
    const [filterStatus, setFilterStatus] = useState('All');
    const statusTabs = ["All", "Open", "In Progress", "Submitted", "Resolved", "Rejected"];
    const loadTableData = () => {
        setIsLoading(true);
        axios.get(route('datatable.issues'), {
            params: {
                page: currentPage,
                per_page: rowsPerPage,
                search: search,
                status: filterStatus,
            },
        }).then((res) => {
            setTableData(res.data.data);
            setTotalRows(res.data.total);
            setIsLoading(false);
        });
    };
    const handleShow = id => {
        router.get(route('capa.issues.show', id))
    }
    const handleEdit = id => {
        router.get(route('capa.issues.edit', id))
    }
    const handleDelete = id => {

    }

    const handleOnBeforePrint = async (id) => {
        try {
            const res = await axios.get(route("capa.issue.print", id));
            setPrintData(res.data.data);

            await new Promise((resolve) => setTimeout(resolve, 300));
        } catch (error) {
            console.error("Error saat print:", err);
            setIsLoading(false);
        }
    }

    // DATATABLE COLUMN
    const COLUMN = [
        {
            name: 'No',
            cell: (row, index) => (currentPage - 1) * rowsPerPage + index + 1,
            width: '70px',
            sortable: true,
        },
        {
            name: t('issue_number'),
            minWidth: "200px",
            selector: row => row.issue_number ?? '-',
            sortable: true,

        },
        {
            name: t('capa_type'),
            minWidth: "200px",
            selector: row => row?.capa_type?.name ?? '-',
            sortable: true,
        },
        {
            name: t('department'),
            minWidth: "200px",
            selector: row => row?.department?.name,
            sortable: true,
        },
        {
            name: t('subject'),
            selector: row => row?.subject,
            sortable: true,
        },
        {
            name: t('deadline'),
            minWidth: "200px",
            selector: row => toDateTimeString(row?.deadline),
            sortable: true,
        },
        {
            name: t('status'),
            selector: row => {
                const status = row.status ?? '-';
                if (status === 'Open') {
                    return <span className="badge bg-success">{t('open')}</span>;
                } else if (status === 'In Progress') {
                    return <span className="badge bg-warning">{t('in_progress')}</span>;
                } else if (status === 'Resolved') {
                    return <span className="badge bg-success">{t('resolved')}</span>;
                } else if (status === 'Rejected') {
                    return <span className="badge bg-danger">{t('rejected')}</span>;
                } else if (status === 'Submitted') {
                    return <span className="badge bg-info">{t('submitted')}</span>;
                } else {
                    return <span className="badge bg-secondary">{status}</span>;
                }
            },
            sortable: true,
        },
        {
            name: t('actions'),
            cell: (row) => {
                const buttons = [];
                const isCreator = permissions.includes('Create CAPA');
                buttons.push(
                    <ShowButton key="show" onClick={() => handleShow(row.id)} />
                );
                if (row.status == 'Resolved') {
                    buttons.push(
                        <PrintButton key="print" contentRef={printRef} loadingType={2} onBeforePrint={() => handleOnBeforePrint(row.id)} />
                    );
                }
                if (row.status === 'Rejected' || row.status === 'Open' && isCreator) {
                    buttons.push(
                        <EditButton key="edit" onClick={() => handleEdit(row.id)} isLoading={isLoading} />,
                        <DeleteButton key="delete" onClick={() => handleDelete(row.id)} isLoading={isLoading} />
                    );
                }

                return buttons.length > 0 ? <>{buttons}</> : null;
            },
            sortable: true,
        }
    ];


    useEffect(() => {
        loadTableData();
    }, [currentPage, rowsPerPage, search, filterStatus]);

    useEffect(() => {
        const msg = flash.error || flash.success;
        if (msg) (flash.error ? notifyError : notifySuccess)(msg, 'bottom-center');
    }, [flash.error, flash.success]);


    return (
        <>
            <AppLayout>
                <Breadcrumb title={t('issues')} subtitle={`${t('issues')}`} />
                <div className="container">
                    <div className="d-flex justify-content-end mb-3">
                        {permissions.includes('Create CAPA') && (
                            <Link href={route('capa.issues.create')} className="btn btn-sm btn-danger" data-title={t('add_issue')}>
                                <Icon icon="line-md:plus" className="me-2" width="20" height="20" />
                                {t('add_issue')}
                            </Link>
                        )}
                    </div>
                    <div className="card">
                        <div className="card-body">
                            <div className="row">
                                {/* === FILTER TAB === */}
                                <div className="col-12 d-flex justify-content-center flex-wrap gap-2 border-bottom pb-3 mb-3">
                                    {statusTabs.map((status) => {
                                        const isActive = filterStatus === status;

                                        // Pilih warna BS5 untuk setiap status
                                        let btnClass = "btn-light text-dark"; // default (All)
                                        if (status === "Open") btnClass = "btn-primary";
                                        else if (status === "In Progress") btnClass = "btn-warning text-dark";
                                        else if (status === "Submitted") btnClass = "btn-info";
                                        else if (status === "Resolved") btnClass = "btn-success";
                                        else if (status === "Rejected") btnClass = "btn-danger";
                                        else if (status === "All") btnClass = "btn-secondary";

                                        // Kalau tidak aktif, jadikan versi outline
                                        const finalClass = isActive
                                            ? `btn ${btnClass} btn-sm fw-semibold rounded-pill px-3`
                                            : `btn btn-outline-secondary btn-sm rounded-pill px-3`;

                                        return (
                                            <button
                                                key={status}
                                                onClick={() => {
                                                    setFilterStatus(status);
                                                    setCurrentPage(1);
                                                }}
                                                className={finalClass}
                                                style={{ transition: "all 0.2s ease" }}
                                            >
                                                {t(status)}
                                            </button>
                                        );
                                    })}
                                </div>

                                {/* === SEARCH BAR === */}
                                <div className="col-12 d-flex justify-content-end mb-3">
                                    <div className="col-md-4">
                                        <Search search={search} setSearch={setSearch} />
                                    </div>
                                </div>

                                {/* === DATATABLE === */}
                                <div className="col-12">
                                    <div className="datatable-wrapper">
                                        <DataTable
                                            className="table-responsive"
                                            columns={COLUMN}
                                            data={tableData}
                                            progressPending={isLoading}
                                            noDataComponent={
                                                isLoading ? (
                                                    <Loading />
                                                ) : search && tableData.length === 0 ? (
                                                    t("datatable.zeroRecords")
                                                ) : (
                                                    t("datatable.emptyTable")
                                                )
                                            }
                                            searchable
                                            defaultSortField="name"
                                            progressComponent={<Loading />}
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
                                            responsive={true}
                                            striped
                                            fixedHeader
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div style={{ position: "absolute", top: "-9999px", visibility: "hidden" }}>

                    {printData && <IssuePrint ref={printRef} issue={printData} />}
                </div>
            </AppLayout>
        </>
    )
}