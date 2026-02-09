import { useTranslation } from "react-i18next";
import { usePage } from "@inertiajs/react";
import { stripHtml } from "../../../../helper";
import DetailItem from "../../../../src/components/ui/DetailItem";


export default function ImpactRiskAssessmentDetail({ impactRiskAssessment }) {
    const { t } = useTranslation();
    const { auth } = usePage().props;
    const { permissions } = auth;

    return (
        <div
            className="tab-pane fade show container"
            id="impact-risk-assesment"
            role="tabpanel"
            aria-labelledby="impact-risk-assesment-tab"
            tabIndex={0}
        >
            <ul className="list-unstyled">
                <DetailItem
                    label={t('source_of_risk')}
                    value={stripHtml(impactRiskAssessment.source_of_risk)}
                />

                <DetailItem
                    label={t('impact_of_risk')}
                    value={stripHtml(impactRiskAssessment.impact_of_risk)}
                />

                <DetailItem
                    label={`${t('risk_evaluation_criteria')} (Probability)`}
                    value={impactRiskAssessment.probability}
                />

                <DetailItem
                    label={`${t('risk_evaluation_criteria')} (Detectability)`}
                    value={impactRiskAssessment.detectability}
                />

                <DetailItem
                    label={`${t('risk_evaluation_criteria')} (Severity)`}
                    value={impactRiskAssessment.severity}
                />

                <DetailItem
                    label={t('cause_of_risk')}
                    value={stripHtml(impactRiskAssessment.cause_of_risk)}
                />

                <DetailItem
                    label={t('control_implemented')}
                    value={stripHtml(impactRiskAssessment.control_implemented)}
                />

                <DetailItem
                    label={t('risk_priority_number')}
                    value={impactRiskAssessment.rpn}
                />

                <DetailItem
                    label={t('risk_category')}
                    value={impactRiskAssessment.risk_category}
                />
            </ul>
        </div>
    );
}
