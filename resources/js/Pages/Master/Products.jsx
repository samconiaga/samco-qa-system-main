

import { useTranslation } from "react-i18next";
import AppLayout from "../../Layouts/AppLayout";
import Breadcrumb from "../../src/components/ui/Breadcrumb";
import { Modal } from "react-bootstrap";
import Button from "../../src/components/ui/Button";
import { useEffect, useRef, useState } from "react";
import { Icon } from "@iconify/react/dist/iconify.js";
import TextInput from "../../src/components/ui/TextInput";
import { Link, useForm } from "@inertiajs/react";
import { route } from "ziggy-js";
import { notifyError, notifySuccess } from "../../src/components/ui/Toastify";
import DataTable from "react-data-table-component";
import axios from "axios";
import DeleteButton from "../../src/components/ui/Datatables/DeleteButton";
import { confirmAlert } from "../../src/components/ui/SweetAlert";
import Loading from "../../src/components/ui/Datatables/Loading";
import Search from "../../src/components/ui/Datatables/Search";
import EditButton from "../../src/components/ui/Datatables/EditButton";


export default function Products() {

    const { t } = useTranslation()
    const [show, setShow] = useState(false);
    const [isLoading, setIsLoading] = useState(false);
    const [modalTitle, setModalTitle] = useState('');
    const { data, setData, post, delete: destroy, put, processing, errors, clearErrors, reset } = useForm({
        product_code: '',
        name: ''
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
        axios.get(route('datatable.products'), {
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
            name: 'No',
            cell: (row, index) => (currentPage - 1) * rowsPerPage + index + 1,
            sortable: true,
            width: '100px',
            style: {
                textAlign: 'center',
            },
        },
        {
            name: t('product_code'),
            cell: (row, index) => row.product_code,
            sortable: true,
            width: '200px',
            style: {
                textAlign: 'center',
            },
        },
        {
            name: t('name'),
            selector: row => row.name,
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
    const [editId, setEditId] = useState(null);
    const handleClose = () => {
        setShow(false);
        setModalTitle('');
        setEditId(null);
        clearErrors();
        reset();
        setData({
            name: '',
            product_code: '',
        });

    };
    const handleShow = (e) => {

        setModalTitle(e.currentTarget.dataset.title);
        setTimeout(() => {
            setIsLoading(false);
            setShow(true);
        }, 150);
    };
    const handleDelete = (id) => {
        confirmAlert(t('are_you_sure'), t('delete_description'), 'warning', () => {
            destroy(route('master-data.products.destroy', id), {
                onSuccess: (page) => {
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
    const handleEdit = (id) => {
        setEditId(id);
        setModalTitle(t('edit_product'));
        setShow(true);
        clearErrors();
        reset();
        const product = tableData.find(prod => prod.id === id);
        if (product) {
            setData({
                name: product.name,
                product_code: product.product_code,
            });
        }
    }
    const handleSubmit = async (e) => {
        e.preventDefault();
        if (editId) {
            return put(route('master-data.products.update', editId), {
                onSuccess: (page) => {
                    setData({
                        name: '',
                        product_code: '',
                    });
                    setEditId(null);
                    setShow(false);
                    loadTableData();
                    notifySuccess(page.props.flash.success, 'bottom-center');
                },
            });
        }

        post(route('master-data.products.store'), {
            onSuccess: (page) => {
                setData({
                    name: '',
                    product_code: '',
                });
                setShow(false);
                loadTableData();
                notifySuccess(page.props.flash.success, 'bottom-center');
            }
        });
    };

    return (
        <>
            <AppLayout>
                <Breadcrumb title={t('products')} subtitle={`${t('master_data')} / ${t('products')}`} />

                <div className="container">
                    <div className="d-flex justify-content-end mb-3">
                        <Button type="button" className="btn btn-sm btn-danger me-3" data-title={t('add_new_product')} onClick={handleShow}>
                            <Icon icon="line-md:plus" className="me-2" width="20" height="20" />
                            {t('add_new_product')}
                        </Button>
                        <Link href={route('master-data.products.trash')} aria-label={t('trash')} className="btn btn-sm btn-secondary" >
                            <Icon icon="line-md:trash" className="me-2" width="20" height="20" />
                            {t('trash')}
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


                <Modal show={show} onHide={handleClose}>
                    <Modal.Header closeButton>
                        <Modal.Title>
                            <h6>{modalTitle}</h6>
                        </Modal.Title>
                    </Modal.Header>
                    <form onSubmit={handleSubmit}>
                        <Modal.Body>
                            <div className="mb-3">
                                <label htmlFor="product_code" className="form-label">{t('product_code')}</label>
                                <TextInput name="product_code"
                                    className="form-control"
                                    autoComplete="off"
                                    onChange={(e) => setData('product_code', e.target.value)}
                                    placeholder={t('enter_attribute', { 'attribute': t('product_code') })}
                                    value={data.product_code}
                                    errorMessage={errors.product_code} />
                            </div>
                            <div className="mb-3">
                                <label htmlFor="name" className="form-label">{t('product_name')}</label>
                                <TextInput name="name"
                                    className="form-control"
                                    autoComplete="off"
                                    onChange={(e) => setData('name', e.target.value)}
                                    placeholder={t('enter_attribute', { 'attribute': t('product_name') })}
                                    value={data.name}
                                    errorMessage={errors.name} />
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
        </>
    )
}