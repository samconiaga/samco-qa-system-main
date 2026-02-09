

import { useTranslation } from "react-i18next";
import AppLayout from "../../../Layouts/AppLayout";
import Breadcrumb from "../../../src/components/ui/Breadcrumb";
import Button from "../../../src/components/ui/Button";
import { useEffect, useState } from "react";
import { Icon } from "@iconify/react/dist/iconify.js";
import { route } from "ziggy-js";
import { notifyError, notifySuccess } from "../../../src/components/ui/Toastify";
import DataTable from "react-data-table-component";
import axios from "axios";
import { confirmAlert } from "../../../src/components/ui/SweetAlert";
import Loading from "../../../src/components/ui/Datatables/Loading";
import Search from "../../../src/components/ui/Datatables/Search";
import { Link, useForm } from "@inertiajs/react";

export default function CapaType({ title }) {

    const { t } = useTranslation()
    const [isLoading, setIsLoading] = useState(false);
    const { data, setData, post, delete: destroy, processing, errors, clearErrors, reset } = useForm({
        id: []
    });
    const [tableData, setTableData] = useState([]);
    // Pagination states
    const [totalRows, setTotalRows] = useState(0);
    const [currentPage, setCurrentPage] = useState(1);
    const [rowsPerPage, setRowsPerPage] = useState(10);
    // Search state
    const [search, setSearch] = useState('');

    const loadTableData = () => {
        setIsLoading(true);
        axios.get(route('datatable.capa-types.trash'), {
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


    const COLUMN = [
        {
            name: t('name'),
            selector: row => row.name,
            sortable: true,
        },
    ];

    const handleDelete = () => {
        confirmAlert(t('are_you_sure'), t('delete_description'), 'warning', () => {
            destroy(route('master-data.capa-types.delete'), {
                onSuccess: (page) => {
                    setData({ id: [] });
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
    };
    const handleRestore = () => {
        confirmAlert(t('are_you_sure'), t('restore_description'), 'warning', () => {
            post(route('master-data.capa-types.restore'), {
                onSuccess: (page) => {
                    setData({ id: [] });
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
    };

    const handleSelectedRowsChange = (state) => {
        setData({ id: state.selectedRows.map(row => row.id) });
    }
    return (
        <>
            <AppLayout>
                <Breadcrumb title={t('capa_types')} subtitle={`${t('master_data')} / ${t('capa_types')}`} />

                <div className="container">
                    <div className="d-flex justify-content-end mb-3">
                        <Button type="button" className="btn btn-sm btn-success me-3" data-title={t('add_new_product')} onClick={handleRestore} disabled={processing || data.id.length === 0}>
                            <Icon icon="line-md:backup-restore" className="me-2" width="20" height="20" />
                            {t('restore')}
                        </Button>
                        <Button type="button" className="btn btn-sm btn-danger me-3" data-title={t('add_new_product')} onClick={handleDelete} disabled={processing || data.id.length === 0}>
                            <Icon icon="line-md:document-delete" className="me-2" width="20" height="20" />
                            {t('delete')}
                        </Button>
                        <Link href={route('master-data.capa-types.index')} aria-label={t('back')} className="btn btn-sm btn-secondary" >
                            <Icon icon="line-md:arrow-left" className="me-2" width="20" height="20" />
                            {t('back')}
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
                                    <DataTable
                                        className="table-responsive"
                                        columns={COLUMN}
                                        data={tableData}
                                        selectableRows
                                        onSelectedRowsChange={handleSelectedRowsChange}
                                        progressPending={isLoading}
                                        noDataComponent={
                                            isLoading ? (
                                                <Loading />
                                            ) : search && tableData.length === 0 ? (
                                                t('datatable.zeroRecords')
                                            ) : (
                                                t('datatable.emptyTable')
                                            )
                                        }

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
                                        persistTableHead
                                        striped />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </AppLayout >
        </>
    )
}