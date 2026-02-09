import { useTranslation } from "react-i18next";
import { toDateString } from "../../helper";
import DetailItem from "../../src/components/ui/DetailItem";

export default function RegulatoryAssessmentDetail({ changeRequest }) {
    const { t } = useTranslation();

    return (
        <div
            className="tab-pane fade show container"
            id="regulatory-assessments"
            role="tabpanel"
            aria-labelledby="regulatory-assessments-tab"
            tabIndex={0}
        >
            <ul>
                <DetailItem
                    label={t("product_affected")}
                    value={
                        changeRequest?.impact_of_change_assesment?.product_affected === "Yes"
                            ? t("product_yes")
                            : t("product_no")
                    }
                />

                <DetailItem
                    label={t("facility_affected")}
                    value={
                        changeRequest?.impact_of_change_assesment?.facility_affected !== "No"
                            ? t(changeRequest?.impact_of_change_assesment?.facility_affected)
                            : t("facility_no")
                    }
                />

                <DetailItem
                    label={t("halal_and_regulatory_compilance_evaluation")}
                    value={
                        changeRequest?.impact_of_change_assesment?.halal_status !== "No"
                            ? t(changeRequest?.impact_of_change_assesment?.halal_status)
                            : t("halal_no")
                    }
                />

                <DetailItem
                    label={t("regulatory_assessment")}
                    value={
                        changeRequest?.impact_of_change_assesment?.regulatory_related == null
                            ? "-"
                            : changeRequest?.impact_of_change_assesment?.regulatory_related === "No"
                                ? t("regulation_no")
                                : t("regulation_yes")
                    }
                />

                <DetailItem
                    label={t("third_party_involvement")}
                    value={
                        changeRequest?.impact_of_change_assesment?.third_party_involved
                            ? t("yes")
                            : t("no")
                    }
                />

                {changeRequest?.impact_of_change_assesment?.third_party_involved && (
                    <DetailItem
                        label={t("third_party_name")}
                        value={changeRequest?.impact_of_change_assesment?.third_party_name}
                    />
                )}

                <DetailItem
                    label={t("regulatory_variation")}
                    value={t(changeRequest?.regulatory?.regulatory_variation ?? "-")}
                />

                <DetailItem
                    label={t("regulatory_change_type")}
                    value={t(changeRequest?.regulatory?.regulatory_change_type ?? "-")}
                />

                {changeRequest?.regulatory?.regulatory_change_type === "regulatory_change_type_3" && (
                    <>
                        <DetailItem
                            label={t("reported_by")}
                            value={changeRequest?.regulatory?.reported_by}
                        />

                        <DetailItem
                            label={t("notification_date")}
                            value={toDateString(changeRequest?.regulatory?.notification_date)}
                        />
                    </>
                )}
            </ul>
        </div>
    );
}
