import { useTranslation } from "react-i18next";
import { stripHtml, toDateString, toDateTimeString } from "../../../../helper";
import DetailItem from "../../../../src/components/ui/DetailItem";
import { useMemo } from "react";

export default function ChangeInitiationDetail({ changeRequest }) {
    const { t } = useTranslation();
    const statusFiles = changeRequest.current_status_files_meta || [];
    const proposedFiles = changeRequest.proposed_change_files_meta || [];
    const otherFiles = changeRequest.supporting_attachments_meta || [];

    const maxFiles = Math.max(
        statusFiles.length,
        proposedFiles.length,
        otherFiles.length
    );
    const followUpMap = useMemo(() => {
        if (!changeRequest?.followUpImplementations) return new Map();

        return new Map(
            changeRequest.followUpImplementations.map(item => [
                item.department_id,
                item.evaluation_status
            ])
        );
    }, [changeRequest?.followUpImplementations]);
    const getRowClass = (status) => {
        if (status === 'Disagree') return 'table-danger';
        if (!status) return 'table-warning';
        return '';
    };
    return (
        <div className="tab-pane fade show active container" id="change-initiation" role="tabpanel" aria-labelledby="change-initiation-tab" tabIndex={0}>
            <ul>
                <DetailItem
                    label={t('title')}
                    value={changeRequest.title}
                />

                <DetailItem
                    label={t('initiator_name')}
                    value={changeRequest.initiator_name}
                />

                <DetailItem
                    label={t('email')}
                    value={changeRequest.employee?.user?.email}
                />

                <DetailItem
                    label={t('department')}
                    value={changeRequest?.department?.name}
                />

                <DetailItem
                    label={t('scope_of_changes')}
                    value={
                        changeRequest.scope_of_change?.map((scope, index) => (
                            <span key={index} className="d-block">
                                - {scope.name}
                            </span>
                        ))
                    }
                />

                <DetailItem
                    label={t('type_of_change')}
                    value={
                        changeRequest.type_of_change?.map((type, index) => (
                            <span key={index} className="d-block">
                                - {type.type_name}
                            </span>
                        ))
                    }
                />

                <DetailItem
                    label={t('current_status')}
                    value={stripHtml(changeRequest.current_status)}
                />

                <DetailItem
                    label={t('proposed_change')}
                    value={stripHtml(changeRequest.proposed_change)}
                />

                <DetailItem
                    label={t('reason')}
                    value={stripHtml(changeRequest.reason)}
                />
            </ul>





            {/* Related Department */}
            <span className="mt-4 mb-2 fw-semibold fs-6 text-primary-light">
                {t('related_departments')}
            </span>
            <table className="table table-responsive table-bordered mt-3" style={{ tableLayout: "fixed" }}>
                <thead>
                    <tr>
                        <th style={{ width: "60px" }} className="text-center">#</th>
                        <th>{t('department')}</th>
                        <th>{t('evaluation_status')}</th>
                    </tr>
                </thead>
                <tbody>
                    {changeRequest?.related_departments?.map((item, index) => {
                        const status = followUpMap.get(item.department_id);

                        return (
                            <tr
                                key={item.id}
                                className={getRowClass(status)}
                            >
                                <td className="text-center">{index + 1}</td>
                                <td>{t(item.department.name)}</td>
                                <td>{status ?? '-'}</td>
                            </tr>
                        );
                    })}
                </tbody>


            </table>
            {/* Change Request File */}
            <span className="mt-4 mb-2 fw-semibold fs-6 text-primary-light">
                {t('supporting_attachment')}
            </span>
            <table className="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th className="text-center">#</th>
                        <th>{t('current_status')}</th>
                        <th>{t('proposed_change')}</th>
                        <th>{t('other')}</th>
                    </tr>
                </thead>

                <tbody>
                    {[...Array(maxFiles)].map((_, i) => (
                        <tr key={i} >
                            {/* NOMOR BARIS */}
                            <td className="text-center">{i + 1}</td>

                            {/* STATUS SAAT INI */}
                            <td>
                                {statusFiles[i] ? (
                                    <a href={statusFiles[i].url} target="_blank" className="text-primary text-decoration-underline" rel="noreferrer">
                                        {statusFiles[i].name}
                                    </a>
                                ) : "-"}
                            </td>

                            {/* USULAN PERUBAHAN */}
                            <td>
                                {proposedFiles[i] ? (
                                    <a href={proposedFiles[i].url} target="_blank" className="text-primary text-decoration-underline" rel="noreferrer">
                                        {proposedFiles[i].name}
                                    </a>
                                ) : "-"}
                            </td>

                            {/* LAINNYA */}
                            <td>
                                {otherFiles[i] ? (
                                    <a href={otherFiles[i].url} target="_blank" className="text-primary text-decoration-underline" rel="noreferrer">
                                        {otherFiles[i].name}
                                    </a>
                                ) : "-"}
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>


            {/* Approval History */}
            <span className="mt-4 mb-2 fw-semibold fs-6 text-primary-light">
                {t('approval_history')}
            </span>
            <table className="table table-responsive table-bordered mt-3">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{t('department')}</th>
                        <th>{t('name')}</th>
                        <th>{t('status')}</th>
                        <th>{t('comment')}</th>
                        <th>{t('date')}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>{changeRequest?.department?.name ?? "-"}</td>
                        <td>{changeRequest?.initiator_name ?? "-"}</td>
                        <td>Submitted</td>
                        <td>-</td>
                        <td>{toDateTimeString(changeRequest?.created_at, false, false) ?? "-"}</td>
                    </tr>
                    {changeRequest?.approvals
                        ?.filter(item => item.decision !== "Pending")
                        .map((item, index) => (
                            <tr key={index}>
                                <td>{index + 1}</td>
                                <td>{item?.approver?.department?.name ?? "-"}</td>
                                <td>{item?.approver?.name ?? "-"}</td>
                                <td>{item?.decision ?? "-"}</td>
                                <td>{item?.comments ?? "-"}</td>
                                <td>{toDateTimeString(item.approved_at, false, false) ?? "-"}</td>
                            </tr>
                        ))}
                </tbody>

            </table>
        </div>
    );
}
