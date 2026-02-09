import { useTranslation } from "react-i18next";
import { useEffect, useState } from "react";
import { Icon } from "@iconify/react/dist/iconify.js";
import { Link, router, useForm, usePage } from "@inertiajs/react";
import DataTable from "react-data-table-component";

import Button from "../../src/components/ui/Button";
import Loading from "../../src/components/ui/Datatables/Loading";
import axios from "axios";
import AppLayout from "../../Layouts/AppLayout";
import Breadcrumb from "../../src/components/ui/Breadcrumb";
import { notifySuccess } from "../../src/components/ui/Toastify";
import EditButton from "../../src/components/ui/Datatables/EditButton";
import DeleteButton from "../../src/components/ui/Datatables/DeleteButton";
import Search from "../../src/components/ui/Datatables/Search";
import { route } from "ziggy-js";
import { Inertia } from "@inertiajs/inertia";
import { confirmAlert } from "../../src/components/ui/SweetAlert";




export default function Employee() {
    const { t } = useTranslation()
    const [show, setShow] = useState(false);
    const [isLoading, setIsLoading] = useState(false);
    const [modalTitle, setModalTitle] = useState('');
    const { data, setData, post, delete: destroy, put, processing, errors, clearErrors, reset } = useForm({
        name: '',
        email: '',
        phone: '',
        gender: '',
        address: '',
        department_id: '',
        position_id: '',
    });
    const [tableData, setTableData] = useState([]);
    // Pagination states
    const [totalRows, setTotalRows] = useState(0);
    const [currentPage, setCurrentPage] = useState(1);
    const [rowsPerPage, setRowsPerPage] = useState(10);
    // Search state
    const [search, setSearch] = useState('');
    const { flash } = usePage().props;

    useEffect(() => {
        if (flash.success) {
            notifySuccess(flash.success, 'bottom-center');
        }
    }, [flash.success]);
    const loadTableData = () => {
        setIsLoading(true);
        axios.get(route('datatable.employees'), {
            params: {
                page: currentPage,
                per_page: rowsPerPage,
                search: search,
            },
        }).then((res) => {
            setTableData(res.data.data);
            setTotalRows(res.data.total);
            setIsLoading(false);
        });
    };
    useEffect(() => {
        loadTableData();
    }, [currentPage, rowsPerPage, search]);

    const handleEdit = (id) => {
        router.get(route('master-data.employees.edit', id));
    };

    const handleDelete = (id) => {
        confirmAlert(t('are_you_sure'), t('delete_description'), 'warning', () => {
            destroy(route('master-data.employees.destroy', id), {
                onSuccess: (page) => {
                    const error = page.props?.flash?.error;
                    const success = page.props?.flash?.success;
                    if (error) {
                        notifyError(error, 'bottom-center');
                    } else {
                        loadTableData();
                    }
                },
            });
        });
    };


    const COLUMN = [
        {
            name: 'No',
            cell: (row, index) => (currentPage - 1) * rowsPerPage + index + 1,
            width: '70px',
            sortable: true,
        },
        {
            name: t('name'),
            selector: row => row.name,
            sortable: true,
        },
        {
            name: t('email'),
            selector: row => row.user.email,
            sortable: true,
        },
        {
            name: t('phone'),
            selector: row => row.phone,
            sortable: true,
        },
        {
            name: t('department'),
            selector: row => row.department.name,
            sortable: true,
        },
        {
            name: t('position'),
            selector: row => row.position.name,
            sortable: true,
        },

        {
            name: t('gender'),
            selector: row => t(row.gender),
            sortable: true,
        },
        {
            name: t('address'),
            selector: row => row.address,
            sortable: true,
        },

        {
            name: t('actions'),
            cell: (row) => (
                <>
                    <EditButton onClick={() => handleEdit(row.id)} isLoading={isLoading} />
                    <DeleteButton onClick={() => handleDelete(row.id)} isLoading={isLoading} />
                </>
            ),
            sortable: true,
        }
    ];
    return (
        <>
            <AppLayout>
                <Breadcrumb title={t('employees')} subtitle={`${t('master_data')} / ${t('employees')}`} />

                <div className="container">
                    <div className="d-flex justify-content-end mb-3">
                        <Link href={route('master-data.employees.create')} className="btn btn-danger btn-sm">
                            <Icon icon="line-md:plus" className="me-2" width="20" height="20" />
                            {t('add_new_employee')}
                        </Link>
                    </div>
                    <div className="card">
                        <div className="card-body">
                            <div className="row">
                                <div className="col-12 d-flex justify-content-end">
                                    <div className="col-md-4">
                                        <Search search={search} setSearch={setSearch} />
                                    </div>
                                </div>
                                <div className="col-12">
                                    <div className="datatable-wrapper">
                                        <DataTable
                                            className="table-responsive"
                                            columns={COLUMN}
                                            data={tableData}
                                            progressPending={isLoading}
                                            noDataComponent={isLoading ? (
                                                <Loading />
                                            ) : search && tableData.length === 0 ? (
                                                t('datatable.zeroRecords')
                                            ) : (
                                                t('datatable.emptyTable')
                                            )
                                            }
                                            searchable
                                            defaultSortField="name"
                                            progressComponent={<Loading />}
                                            pagination
                                            paginationServer
                                            paginationTotalRows={totalRows}
                                            paginationPerPage={rowsPerPage}
                                            onChangePage={page => setCurrentPage(page)}
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
            </AppLayout >
        </>
    )
}