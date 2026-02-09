import React from "react";
import { useTranslation } from "react-i18next";
import { toDateString, stripHtml } from "../../../../helper";
const ChangeInitiationSection = ({ changeRequest }) => {
    const { t } = useTranslation();
    return (
        <div style={{ marginTop: "0.5cm" }}>
            {/* === Header Nomor Usulan === */}
            <table className="print-table" style={{ width: "100%" }} >
                <tbody>
                    <tr>
                        <td colSpan={2}>
                            <b>{t('change_request_number')}</b>  : {changeRequest?.request_number ?? "-"}
                        </td>
                    </tr>

                    {/* === A. Judul Perubahan === */}
                    <tr>
                        <td colSpan={2}>
                            <div style={{ fontWeight: "bold" }}>A. {t('change_title')}</div>
                            <div style={{ paddingLeft: "0.5cm" }}>
                                {changeRequest?.title || "-"}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style={{ width: "50%" }}>
                            <table className="no-border">
                                <tbody>
                                    <tr>
                                        <td style={{ width: "40%" }}>{t('date')}</td>
                                        <td style={{ width: "5%" }}>:</td>
                                        <td>{toDateString(changeRequest?.requested_date)}</td>
                                    </tr>
                                    <tr>
                                        <td>{t('initiator_name')}</td>
                                        <td>:</td>
                                        <td>{changeRequest?.initiator_name ?? "-"}</td>
                                    </tr>
                                    <tr>
                                        <td>{t('department')}</td>
                                        <td>:</td>
                                        <td>{changeRequest?.department?.name}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td style={{ width: "50%", textAlign: "center", alignContent: "center" }}>
                            {t('sign_and_date')}
                            <table className="no-border" style={{ marginTop: "50px" }}>
                                <tbody style={{ textAlign: "center" }}>
                                    <tr>
                                        <td style={{ width: "65%", textAlign: 'center' }}>({changeRequest?.employee?.name})</td>
                                        <td style={{ textAlign: 'center' }}>({changeRequest?.approvals?.find(
                                            (a) => a.stage === "Approve Manager"
                                        )?.approver?.name})</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    {/* === B. Perubahan Terkait === */}
                    <tr>
                        <td colSpan={2}>
                            <div style={{ fontWeight: "bold" }}> B. {t('scope_of_changes')}* : </div>
                            <ul>
                                {changeRequest.scope_of_change.map((scope, index) => (
                                    <li key={index} style={{ paddingLeft: "0.5cm" }}>- {scope.name}</li>
                                ))}
                            </ul>
                        </td>
                    </tr>

                    {/* === C. Jenis Perubahan === */}
                    <tr>
                        <td colSpan={2}>
                            <div style={{ fontWeight: "bold" }}>C. {t('type_of_change')}* : </div>
                            <div className="row gy-2">
                                {changeRequest.type_of_change.map((type) => (
                                    <div key={type.id} className="col-md-6">
                                        <div className="d-flex align-items-center">
                                            <span>- {type?.type_name}</span>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </td>
                    </tr>

                    {/* === D. Status Saat Ini === */}
                    <tr>
                        <td colSpan={2}>
                            <div style={{ fontWeight: "bold" }}> D. {t('current_status')}* :</div>
                            <div style={{
                                padding: "3px 0 0 0.5cm",
                                whiteSpace: "pre-line",
                                fontWeight: "normal",
                            }}>
                                {stripHtml(changeRequest?.current_status) ||
                                    "-"}
                            </div>
                        </td>
                    </tr>
                    {/* === E. Usulan Perubahan === */}
                    <tr>
                        <td colSpan={2}>
                            <div style={{ fontWeight: "bold" }}>E. {t('proposed_change')}* :</div>
                            <div style={{
                                padding: "3px 0 0 0.5cm",
                                whiteSpace: "pre-line",
                                fontWeight: "normal",
                            }}>
                                {stripHtml(changeRequest?.proposed_change) ||
                                    "-"}
                            </div>
                        </td>
                    </tr>
                    {/* === F. Alasan Perubahan === */}
                    <tr>
                        <td colSpan={2}>
                            <div style={{ fontWeight: "bold" }}>F. {t('change_reason')}* :</div>
                            <div style={{
                                padding: "3px 0 0 0.5cm",
                                whiteSpace: "pre-line",
                                fontWeight: "normal",
                            }}>
                                {stripHtml(changeRequest?.reason) ||
                                    "-"}
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div >
    );
};

export default ChangeInitiationSection;
