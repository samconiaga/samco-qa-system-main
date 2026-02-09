import { useTranslation } from "react-i18next";
import { stripHtml, toDateTimeString } from "../../../../helper";

export default function IssueResolutionDetail({ issue }) {
    const { t } = useTranslation();
    return (
        <div className="tab-pane fade show container" id="issue-resolution-detail" role="tabpanel" aria-labelledby="issue-resolution-detail-tab" tabIndex={0}>
            <ul>
                <li className="d-flex align-items-start gap-1">
                    <span className="w-30 text-md fw-semibold text-primary-light">
                        {t('resolution_description')}
                    </span>
                    <span className="fw-medium text-secondary-light">:</span>
                    <span className="text-secondary-light">
                        {stripHtml(issue?.resolution?.resolution_description) ?? '-'}
                    </span>
                </li>
                <li className="d-flex align-items-start gap-1">
                    <span className="w-30 text-md fw-semibold text-primary-light">
                        {t('gap_analysis')}
                    </span>
                    <span className="fw-medium text-secondary-light">:</span>
                    <span className="text-secondary-light">
                        {stripHtml(issue?.resolution?.gap_analysis) ?? '-'}
                    </span>
                </li>
                <li className="d-flex align-items-start gap-1">
                    <span className="w-30 text-md fw-semibold text-primary-light">
                        {t('root_cause_analysis')}
                    </span>
                    <span className="fw-medium text-secondary-light">:</span>
                    <span className="text-secondary-light">
                        {stripHtml(issue?.resolution?.root_cause_analysis) ?? '-'}
                    </span>
                </li>
                <li className="d-flex align-items-start gap-1">
                    <span className="w-30 text-md fw-semibold text-primary-light">
                        {t('preventive_action')}
                    </span>
                    <span className="fw-medium text-secondary-light">:</span>
                    <span className="text-secondary-light">
                        {stripHtml(issue?.resolution?.preventive_action) ?? '-'}
                    </span>
                </li>
                <li className="d-flex align-items-start gap-1">
                    <span className="w-30 text-md fw-semibold text-primary-light">
                        {t('corrective_action')}
                    </span>
                    <span className="fw-medium text-secondary-light">:</span>
                    <span className="text-secondary-light">
                        {stripHtml(issue?.resolution?.corrective_action) ?? '-'}
                    </span>
                </li>
            </ul>
        </div>
    );
}
