import DataTable from "react-data-table-component";
import Loading from "../../../../src/components/ui/Datatables/Loading";
import axios from "axios";
import { useTranslation } from "react-i18next";
import { useEffect, useState } from "react";

export default function AffectedProduct({ changeRequest }) {
    const { t } = useTranslation();
    const [isLoading, setIsLoading] = useState(false);
    const [tableData, setTableData] = useState([]);
    // Pagination states
    const [totalRows, setTotalRows] = useState(0);
    const [currentPage, setCurrentPage] = useState(1);
    const [rowsPerPage, setRowsPerPage] = useState(10);

    const loadTableData = () => {
        setIsLoading(true);
        axios.get(route('datatable.affected-products', changeRequest?.id), {
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
    }, [currentPage, rowsPerPage]);
    const COLUMN = [
        {
            name: 'No',
            cell: (row, index) => (currentPage - 1) * rowsPerPage + index + 1,
            width: '70px',
            sortable: true,
        },
        {
            name: t('product_code'),
            selector: row => row.product_code ?? '-',
            sortable: true,
        },
        {
            name: t('product_name'),
            selector: row => row.name,
            sortable: true,
        },



    ];
    return (
        <div className="tab-pane fade show container" id="affected-products" role="tabpanel" aria-labelledby="affected-products-tab" tabIndex={0}>
            {changeRequest?.impact_of_change_assesment?.product_affected == "Yes" ? (
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
            ) : (
                <ul className="list-unstyled mb-40">
                    {/* Product Affected */}
                    <li className="row mb-3 align-items-start">
                        <div className="col-md-4 text-md fw-semibold text-primary-light">
                            {t("product_affected")}
                        </div>
                        <div className="col-auto fw-medium text-secondary-light">:</div>
                        <div className="col fw-medium text-secondary-light">
                            {changeRequest?.impact_of_change_assesment?.product_affected == "Yes" ? t('product_yes') : t('product_no') ?? "-"}
                        </div>
                    </li>
                </ul>
            )}
        </div >
    )
}