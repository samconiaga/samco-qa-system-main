import { useTranslation } from "react-i18next";

const LastVerificationSection = ({ changeRequest }) => {
    const { t } = useTranslation();
    return (
        <div style={{ marginTop: "1cm" }}>
            <table className="print-table" style={{ width: "100%" }} >
                <tbody>
                    {/* ===P. Verifikasi Pemberlakuan Perubahan === */}
                    <tr>
                        <td>
                            <b>O. {t('verification_of_change_enforcement')}</b>
                        </td>
                    </tr>
                    {/* ===N. Pesetujuan Terkait Halal === */}
                    <tr>
                        <td style={{ textAlign: "justify" }}>
                            <div style={{ fontWeight: "bold" }}>{t("risk_assessment_after_risk_mitigation")} : </div>
                            <div>{t('verification_of_change_enforcement_desc')}</div>
                            <div style={{ marginTop: "10px", marginBottom: "10px" }}> ☑ {t('can_be_implemented')} ☐ {t('can_not_be_implemented')}</div>
                            <table style={{ width: "100%" }}>
                                <tr>
                                    <td style={{ height: "80px" }}>{t('note')} : </td>
                                </tr>
                            </table>
                            <div>
                                <table style={{ width: "100%" }} className="no-border">
                                    <tr>
                                        <td style={{ width: "50%" }}>
                                            <div>{t('assessment_by')}</div>
                                            <div style={{ marginBottom: "50px" }}>{t('sign_and_date')}</div>
                                            <div>Quality Assurance Sub Dept. Head</div>
                                        </td>
                                        <td>
                                            <div>{t('approved_by')}</div>
                                            <div style={{ marginBottom: "50px" }}>{t('sign_and_date')}</div>
                                            <div>Quality Assurance Dept. Head</div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    )
}

export default LastVerificationSection