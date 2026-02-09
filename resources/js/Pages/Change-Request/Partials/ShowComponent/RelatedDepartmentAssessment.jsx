import DataTable from "react-data-table-component";
import Loading from "../../../../src/components/ui/Datatables/Loading";
import axios from "axios";
import { useTranslation } from "react-i18next";
import { useEffect, useState } from "react";
import { toDateString } from "../../../../helper";
import { router, useForm, usePage } from "@inertiajs/react";
import EditButton from "../../../../src/components/ui/Datatables/EditButton";
import ShowButton from "../../../../src/components/ui/Datatables/ShowButton";
import { Modal } from "react-bootstrap";
import TextInput from "../../../../src/components/ui/TextInput";
import SelectInput from "../../../../src/components/ui/SelectInput";
import Button from "../../../../src/components/ui/Button";
import { route } from "ziggy-js";

export default function RelatedDepartmentAssessment({ changeRequest, refreshTable }) {
    const { t } = useTranslation();
    const [modalShow, setModalShow] = useState(false);
    const [isLoading, setIsLoading] = useState(false);
    const [tableData, setTableData] = useState([]);
    const { user, permissions } = usePage().props.auth;
    // Pagination states
    const [totalRows, setTotalRows] = useState(0);
    const [currentPage, setCurrentPage] = useState(1);
    const [rowsPerPage, setRowsPerPage] = useState(10);


    const loadTableData = () => {
        setIsLoading(true);
        axios.get(route('datatable.related-department-assessment', changeRequest?.id), {
            params: {
                page: currentPage,
                per_page: rowsPerPage,
            },
        }).then((res) => {
            setTableData(res.data.data);
            setTotalRows(res.data.total);
            setIsLoading(false);
        });
    };

    useEffect(() => {
        loadTableData();
    }, [currentPage, rowsPerPage, refreshTable]);

    const handleEdit = (row) => {
        setData({
            id: row?.id || "",
            comments: row?.comments || "",
            evaluation_status: row?.evaluation_status || "",
        });
        setModalShow(true);
    }

    const canApprove =
        permissions.some((perm) =>
            ["Approve QA Manager", "Approve Plant Manager", "Approve QA SPV"].includes(perm)
        );
    const COLUMN = [
        {
            name: 'No',
            cell: (row, index) => (currentPage - 1) * rowsPerPage + index + 1,
            width: '70px',
            sortable: true,
        },
        {
            name: t('assessment_by'),
            selector: row => row?.employee?.employee_code ?? '-',
            sortable: true,
        },
        {
            name: t('date'),
            selector: row => toDateString(row?.created_at) ?? '-',
            sortable: true,
        },
        {
            name: t('evaluation_status'),
            selector: row => t(row?.evaluation_status) ?? '-',
            sortable: true,
        },
        {
            name: t('department'),
            selector: row => t(row?.department?.name) ?? '-',
            sortable: true,
        },
        {
            name: t('comments'),
            selector: row => row?.comments ?? '-',
            width: "200px",
            sortable: true,
        },
        {
            name: t('actions'),
            cell: (row) => (
                <>
                    {(row?.evaluation_status == "Agree" && (row?.department_id == user?.employee?.department_id || canApprove)) && (
                        <ShowButton onClick={() => handleShow(row)} />
                    )}


                    {/*
                    {row?.department_id === user?.employee?.department_id && (
                        <EditButton onClick={() => handleEdit(row)} />
                    )} */}
                </>
            ),
        }

    ];

    const { data, setData, processing, post, put, transform, errors, setError, clearErrors } = useForm({
        id: "",
        comments: "",
        evaluation_status: "",

    });
    const handleClose = () => {
        setModalShow(false);

    };
    const handleSubmit = () => {
        put(route('change-requests.related-department-assessment.update', data.id), {
            onSuccess: () => {
                setModalShow(false);
                loadTableData();
            },
        });
    };


    const handleShow = (row) => {
        router.get(route('change-requests.follow-up-implementations.index', {
            id: row?.id,
        }));
    };

    return (

        <div className="tab-pane fade show container" id="related-department-assessment" role="tabpanel" aria-labelledby="related-department-assessment-tab" tabIndex={0}>
            <div className="datatable-wrapper">
                <DataTable
                    className="table-responsive"
                    columns={COLUMN}
                    data={tableData}
                    progressPending={isLoading}
                    noDataComponent={isLoading ? (
                        <Loading />
                    ) : tableData.length === 0 ? (
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
            {/* Edit Modal */}
            {/* <Modal show={modalShow} onHide={handleClose}>
                <Modal.Header closeButton>
                    <Modal.Title>
                        <h6>{t('edit')}</h6>
                    </Modal.Title>
                </Modal.Header>
                <form onSubmit={(e) => {
                    e.preventDefault();
                    handleSubmit()
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
                        <div className="mb-3">
                            <label htmlFor="evaluation_status" className="form-label">{t('evaluation_status')}</label>
                            <SelectInput
                                className="form-control"
                                autoComplete="off"
                                onChange={(e) => setData('evaluation_status', e.target.value)}
                                placeholder={t('select_attribute', { 'attribute': t('evaluation_status') })}
                                value={data.evaluation_status}
                                errorMessage={errors.evaluation_status}>
                                <option value="Agree">{t('agree')}</option>
                                <option value="Agree Not Impacted">{t('agree_not_impacted')}</option>
                                <option value="Disagree">{t('disagree')}</option>
                            </SelectInput>
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
            </Modal> */}
        </div >

    )
}