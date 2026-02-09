import { useTranslation } from "react-i18next";
import Kop from "../Kop";
import { forwardRef } from "react";
import { stripHtml, toDateTimeString } from "../../../helper";

const IssuePrint = forwardRef(({ issue }, ref) => {
    const { t } = useTranslation();

    return (
        <div ref={ref} style={{ fontFamily: "Times New Roman, Times, serif" }}>

            {/* === Halaman 1 === */}
            <Kop />
            <div style={{ margin: "0cm 1cm" }}>
                <div style={{
                    textAlign: "center",
                    marginTop: "0.5cm",
                    marginBottom: "0.5cm",
                    lineHeight: "1.2",
                }} >
                    <div
                        style={{
                            fontSize: "16pt",
                            fontWeight: "bold",
                            textDecoration: "underline",
                        }}
                    >
                        {t("issue_report")}
                    </div>
                    <div style={{ fontSize: "12pt", marginTop: "2px" }}>
                        {t("number")}: {issue?.issue_number || "-"}
                    </div>
                </div>

                {/* Inisiasi Masalah */}
                <div className="mt-3">
                    <div style={{ marginTop: "0.5cm" }}>
                        {/* === Header Nomor Usulan === */}
                        <table className="print-table" style={{ width: "100%" }}>
                            <tbody>
                                <tr>
                                    <td colSpan={3}>
                                        <b>A. {t('issue_initiation')}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style={{ width: "40%" }}>{t('issue_number')}</td>
                                    <td style={{ width: "5%", textAlign: "center" }}>:</td>
                                    <td>{issue?.issue_number}</td>
                                </tr>
                                <tr>
                                    <td style={{ width: "40%" }}>{t('subject')}</td>
                                    <td style={{ width: "5%", textAlign: "center" }}>:</td>
                                    <td>{issue?.subject}</td>
                                </tr>
                                <tr>
                                    <td style={{ width: "40%" }}>{t('capa_type')}</td>
                                    <td style={{ width: "5%", textAlign: "center" }}>:</td>
                                    <td>{issue?.capa_type?.name}</td>
                                </tr>
                                <tr>
                                    <td style={{ width: "40%" }}>{t('criteria')}</td>
                                    <td style={{ width: "5%", textAlign: "center" }}>:</td>
                                    <td>{t(issue?.criteria)}</td>
                                </tr>
                                <tr>
                                    <td style={{ width: "40%" }}>{t('department')}</td>
                                    <td style={{ width: "5%", textAlign: "center" }}>:</td>
                                    <td>{issue?.department?.name}</td>
                                </tr>
                                <tr>
                                    <td style={{ width: "40%" }}>{t('deadline')}</td>
                                    <td style={{ width: "5%", textAlign: "center" }}>:</td>
                                    <td>{toDateTimeString(issue?.deadline)}</td>
                                </tr>
                                <tr>
                                    <td style={{ width: "40%" }}>{t('finding')}</td>
                                    <td style={{ width: "5%", textAlign: "center" }}>:</td>
                                    <td>{stripHtml(issue?.finding)}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div >
                </div>

                {/* Resolusi Masalah */}
                <div className="mt-3">
                    <div style={{ marginTop: "0.5cm" }}>
                        {/* === Header Nomor Usulan === */}
                        <table className="print-table" style={{ width: "100%" }}>
                            <tbody>
                                <tr>
                                    <td colSpan={3}>
                                        <b>B. {t('issue_resolution')}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style={{ width: "40%" }}>{t('resolution_description')}</td>
                                    <td style={{ width: "5%", textAlign: "center" }}>:</td>
                                    <td>{stripHtml(issue?.resolution?.resolution_description)}</td>
                                </tr>
                                <tr>
                                    <td style={{ width: "40%" }}>{t('gap_analysis')}</td>
                                    <td style={{ width: "5%", textAlign: "center" }}>:</td>
                                    <td>{stripHtml(issue?.resolution?.gap_analysis)}</td>
                                </tr>
                                <tr>
                                    <td style={{ width: "40%" }}>{t('root_cause_analysis')}</td>
                                    <td style={{ width: "5%", textAlign: "center" }}>:</td>
                                    <td>{stripHtml(issue?.resolution?.root_cause_analysis)}</td>
                                </tr>
                                <tr>
                                    <td style={{ width: "40%" }}>{t('preventive_action')}</td>
                                    <td style={{ width: "5%", textAlign: "center" }}>:</td>
                                    <td>{stripHtml(issue?.resolution?.preventive_action)}</td>
                                </tr>
                                <tr>
                                    <td style={{ width: "40%" }}>{t('corrective_action')}</td>
                                    <td style={{ width: "5%", textAlign: "center" }}>:</td>
                                    <td>{stripHtml(issue?.resolution?.corrective_action)}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div >
                </div>
            </div>



        </div>
    );
});

export default IssuePrint;
