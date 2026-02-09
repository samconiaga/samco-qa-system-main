import { useTranslation } from "react-i18next";
import ImpactRiskAssesmentForm from "../FormComponent/ImpactRiskAssesmentForm";
import { usePage } from "@inertiajs/react";
import { stripHtml } from "../../../../helper";
import DetailItem from "../../../../src/components/ui/DetailItem";

export default function QaRiskAssessmentDetail({ riskAssessment, data, setData, errors, setError }) {
    const { t } = useTranslation();
    const { auth } = usePage().props;
    const { permissions, user } = auth;
    return (
        <div
            className="tab-pane fade show container"
            id="qa-risk-assesment"
            role="tabpanel"
            aria-labelledby="qa-risk-assesment-tab"
            tabIndex={0}
        >
            <ul>
                <DetailItem
                    label={t('source_of_risk')}
                    value={stripHtml(riskAssessment?.source_of_risk)}
                />

                <DetailItem
                    label={t('impact_of_risk')}
                    value={stripHtml(riskAssessment?.impact_of_risk)}
                />

                <DetailItem
                    label={`${t('risk_evaluation_criteria')} (Probability)`}
                    value={riskAssessment?.probability}
                />

                <DetailItem
                    label={`${t('risk_evaluation_criteria')} (Detectability)`}
                    value={riskAssessment?.detectability}
                />

                <DetailItem
                    label={`${t('risk_evaluation_criteria')} (Severity)`}
                    value={riskAssessment?.severity}
                />

                <DetailItem
                    label={t('cause_of_risk')}
                    value={stripHtml(riskAssessment?.cause_of_risk)}
                />

                <DetailItem
                    label={t('control_implemented')}
                    value={stripHtml(riskAssessment?.control_implemented)}
                />

                <DetailItem
                    label={t('risk_priority_number')}
                    value={riskAssessment?.rpn}
                />

                <DetailItem
                    label={t('risk_category')}
                    value={riskAssessment?.risk_category}
                />
            </ul>
        </div>
    );
}
