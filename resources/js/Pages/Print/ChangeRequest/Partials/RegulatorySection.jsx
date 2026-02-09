import React from "react";
import { useTranslation } from "react-i18next";
const RegulatorySection = ({ changeRequest }) => {
    const { t } = useTranslation();
    const isChecked = (condition) => {
        return condition ? "☑" : "☐";
    };
    return (
        <>
            <div style={{ marginTop: "0.5cm" }}>
                <table className="print-table" style={{ width: "100%" }} >
                    <tbody>
                        {/* ===M. Perizinan Perubahan Fasilitas === */}
                        <tr>
                            <td>
                                <div style={{ fontWeight: "bold" }}>M. {t('approval_related_to_halal')}</div>
                                <div style={{
                                    padding: "6px 0 0 0.5cm",
                                    whiteSpace: "pre-line",
                                    fontWeight: "normal",
                                }}>

                                    {t('halal_question')}
                                </div>
                                <div style={{
                                    padding: "6px 0 0 0.5cm",
                                    whiteSpace: "pre-line",
                                    fontWeight: "normal",
                                }}>

                                    {t(changeRequest?.impact_of_change_assesment?.halal_status) || "-"}
                                </div>
                            </td>
                        </tr>
                        {/* ===N. Pesetujuan Terkait Halal ===
                        <tr>
                            <td>
                                <div style={{ fontWeight: "bold" }}>N. {t('registration_reporting_category')}</div>
                                <div style={{
                                    padding: "6px 0 0 0.5cm",
                                    whiteSpace: "pre-line",
                                    fontWeight: "normal",
                                }}>
                                    {t(changeRequest?.impact_of_change_assesment?.halal_status) || "-"}
                                </div>
                            </td>
                        </tr> */}



                    </tbody>
                </table>
            </div>
            <div style={{ marginTop: "0.3cm" }}>
                <table className="print-table" style={{ width: "100%" }} >
                    <tbody>
                        {/* ===N. Disposisi === */}
                        <tr>
                            <td colspan={3}>
                                <b>N. {t('disposition')}</b> :  {t(isChecked(true))} {t('approved')} {t(isChecked(false))} {t('not_approved')}
                            </td>
                        </tr>
                        <tr>
                            <td colspan={2} style={{ textAlign: "center" }}>
                                {t('sign_and_date')}
                            </td>
                            <td style={{ textAlign: "center" }}>{t('note')}</td>
                        </tr>
                        <tr>
                            <td style={{ width: "40%", height: "80px", verticalAlign: "middle" }}>Quality Assurance Sub Dept. Head</td>
                            <td style={{ width: "25%" }}>{new Date(changeRequest?.closing?.qa_spv_sign).toLocaleString()}</td>
                            <td ></td>
                        </tr>
                        <tr>
                            <td style={{ width: "40%", height: "80px", verticalAlign: "middle" }}>Quality Assurance Dept. Head</td>
                            <td style={{ width: "25%" }}>{new Date(changeRequest?.closing?.qa_manager_sign).toLocaleString()}</td>
                            <td></td>
                        </tr>
                        <tr style={{ width: "40%", height: "80px", verticalAlign: "middle" }}>
                            <td>Plant Division Head</td>
                            <td style={{ width: "25%" }}>
                                {(() => {
                                    // cari approval yang sesuai
                                    const approval = changeRequest?.approvals?.find(
                                        a =>
                                            a.stage === "Approve Plant Manager" &&
                                            a.decision === "Approved"
                                    );

                                    // jika tidak ketemu
                                    if (!approval) return "-";

                                    // pakai updated_at kalau ada, kalau tidak pakai created_at
                                    const dateString = approval.updated_at || approval.created_at;

                                    // jika dua-duanya kosong
                                    if (!dateString) return "-";

                                    return new Date(dateString).toLocaleString();
                                })()}
                            </td>
                            <td></td>
                        </tr>


                    </tbody>
                </table>
            </div>
        </>
    );
};

export default RegulatorySection;
