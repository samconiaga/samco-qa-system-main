import { useTranslation } from "react-i18next";
import { stripHtml, toDateTimeString } from "../../../../helper";

export default function IssueDetail({ issue }) {
    const { t } = useTranslation();
    console.log(issue);

    return (
        <div className="tab-pane fade show active container" id="issue-detail" role="tabpanel" aria-labelledby="issue-detail-tab" tabIndex={0}>
            <ul>
                <li className="d-flex align-items-center gap-1 mb-12">
                    <span className="w-30 text-md fw-semibold text-primary-light">
                        {t('subject')}
                    </span>
                    <span className="fw-medium text-secondary-light">:</span>
                    <span className="flex-grow-1 text-secondary-light">{issue.subject ?? '-'}</span>
                </li>
                <li className="d-flex align-items-center gap-1 mb-12">
                    <span className="w-30 text-md fw-semibold text-primary-light">
                        {t('department')}
                    </span>
                    <span className="fw-medium text-secondary-light">:</span>
                    <span className="flex-grow-1 text-secondary-light">{issue?.department?.name ?? '-'}</span>
                </li>
                <li className="d-flex align-items-start gap-1 mb-3">
                    <span className="w-30 text-md fw-semibold text-primary-light">
                        {t('capa_type')}
                    </span>
                    <span className="fw-medium text-secondary-light">:</span>
                    <span className="flex-grow-1 text-secondary-light">{issue?.capa_type?.name ?? '-'}</span>
                </li>
                <li className="d-flex align-items-start gap-1 mb-3">
                    <span className="w-30 text-md fw-semibold text-primary-light">
                        {t('criteria')}
                    </span>
                    <span className="fw-medium text-secondary-light">:</span>
                    <span className="flex-grow-1 text-secondary-light">{issue?.criteria ?? '-'}</span>
                </li>
                <li className="d-flex align-items-start gap-1 mb-3">
                    <span className="w-30 text-md fw-semibold text-primary-light">
                        {t('deadline')}
                    </span>
                    <span className="fw-medium text-secondary-light">:</span>
                    <span className="flex-grow-1 text-secondary-light">
                        {toDateTimeString(issue?.deadline)}
                    </span>
                </li>

                <li className="d-flex align-items-start gap-1">
                    <span className="w-30 text-md fw-semibold text-primary-light">
                        {t('findings')}
                    </span>
                    <span className="fw-medium text-secondary-light">:</span>
                    <span className="text-secondary-light">
                        {stripHtml(issue?.finding) ?? '-'}
                    </span>
                </li>
            </ul>

            <table className="table table-responsive table-bordered mt-3">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{t('status')}</th>
                        <th>{t('comment')}</th>
                        <th>{t('approver')}</th>
                    </tr>
                </thead>
                <tbody>
                    {issue?.approval_history?.map((item, index) => (
                        <tr key={index}>
                            <td>{index + 1}</td>
                            <td>{t(item?.status)}</td>
                            <td>{item?.comment ?? "-"}</td>
                            <td>{item?.approver?.name ?? "-"}</td>
                        </tr>
                    ))}
                </tbody>
            </table>

        </div>
    );
}
