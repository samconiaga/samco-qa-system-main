import React from "react";
import { useTranslation } from "react-i18next";
import { stripHtml } from "../../../../helper";
const RiskAssessmentSection = ({ changeRequest }) => {
    const { t } = useTranslation();
    const isChecked = (condition) => {
        return condition ? "☑" : "☐";
    };
    return (
        <div style={{ marginTop: "0.5cm" }}>
            {/* === Header Nomor Usulan === */}
            <table className="print-table" style={{ width: "100%" }} >
                <tbody>
                    {/* ===G. Sebelum Mitigasi === */}
                    <tr>
                        <td colSpan={7}>
                            <b>G. {t('risk_assessmemts')} ({t('before_risk_mitigation')})</b>
                        </td>
                    </tr>
                    <tr>
                        <th style={{ textAlign: "center" }}>{t('source_of_risk')}</th>
                        <th style={{ textAlign: "center" }}>{t('impact_of_risk')}</th>
                        <th style={{ textAlign: "center" }}>{t('severity')}</th>
                        <th style={{ textAlign: "center" }}>{t('probability')}</th>
                        <th style={{ textAlign: "center" }}>{t('detectability')}</th>
                        <th style={{ textAlign: "center" }}>{t('rpn')}</th>
                        <th style={{ textAlign: "center" }}>{t('category')}</th>
                    </tr>
                    <tr>
                        <td>{stripHtml(changeRequest?.impact_risk_assesment?.source_of_risk)}</td>
                        <td>{stripHtml(changeRequest?.impact_risk_assesment?.impact_of_risk)}</td>
                        <td style={{ textAlign: "center" }}>{changeRequest?.impact_risk_assesment?.severity}</td>
                        <td style={{ textAlign: "center" }}>{changeRequest?.impact_risk_assesment?.probability}</td>
                        <td style={{ textAlign: "center" }}>{changeRequest?.impact_risk_assesment?.detectability}</td>
                        <td style={{ textAlign: "center" }}>{changeRequest?.impact_risk_assesment?.rpn}</td>
                        <td>{changeRequest?.impact_risk_assesment?.risk_category}</td>
                    </tr>

                    {/* ===H. Setelah Mitigasi === */}
                    <tr>
                        <td colSpan={7}>
                            <b>H. {t('risk_assessmemts')} ({t('after_risk_mitigation')})</b>
                        </td>
                    </tr>
                    <tr>
                        <th style={{ textAlign: "center" }}>{t('source_of_risk')}</th>
                        <th style={{ textAlign: "center" }}>{t('impact_of_risk')}</th>
                        <th style={{ textAlign: "center" }}>{t('severity')}</th>
                        <th style={{ textAlign: "center" }}>{t('probability')}</th>
                        <th style={{ textAlign: "center" }}>{t('detectability')}</th>
                        <th style={{ textAlign: "center" }}>{t('rpn')}</th>
                        <th style={{ textAlign: "center" }}>{t('category')}</th>
                    </tr>
                    <tr>
                        <td>{stripHtml(changeRequest?.qa_risk_assesment?.source_of_risk)}</td>
                        <td>{stripHtml(changeRequest?.qa_risk_assesment?.impact_of_risk)}</td>
                        <td style={{ textAlign: "center" }}>{changeRequest?.qa_risk_assesment?.severity}</td>
                        <td style={{ textAlign: "center" }}>{changeRequest?.qa_risk_assesment?.probability}</td>
                        <td style={{ textAlign: "center" }}>{changeRequest?.qa_risk_assesment?.detectability}</td>
                        <td style={{ textAlign: "center" }}>{changeRequest?.qa_risk_assesment?.rpn}</td>
                        <td>{changeRequest?.qa_risk_assesment?.risk_category}</td>
                    </tr>
                    {/* ===I. PRODUK TERDAMPAK === */}
                    <tr>
                        <td colSpan={7}>
                            <b>I. {t('affected_product')}</b>
                        </td>
                    </tr>
                    <tr style={{ width: "100%" }}>
                        <td colSpan={7} style={{ padding: "6px" }}>
                            {changeRequest?.affected_products?.length > 0 ? (
                                <div className="row gy-2">
                                    {changeRequest.affected_products.map((product) => (
                                        <div key={product.id} className="col-md-4">
                                            <div className="d-flex align-items-center">
                                                <span>- {product?.name}</span>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div>-</div>
                            )}
                        </td>
                    </tr>
                    {/* ===J. Keterlibatan Pihak Ketiga === */}
                    <tr>
                        <td colSpan={7}>
                            <b>J. {t('third_party_involvement')}</b>
                        </td>
                    </tr>
                    <tr style={{ width: "100%" }}>
                        <td colSpan={7} style={{ padding: "6px" }}>
                            <span>{t('third_party_question')}</span>
                            <div className="d-flex align-items-center gap-4 mt-1 mb-1">
                                <div>
                                    {isChecked(changeRequest?.impact_of_change_assesment?.third_party_involved)} {t('yes')}
                                </div>
                                <div>
                                    {isChecked(!changeRequest?.impact_of_change_assesment?.third_party_involved)} {t('no')}
                                </div>
                            </div>
                            <span>{t('third_party_name')} : {changeRequest?.impact_of_change_assesment?.third_party_name ?? "-"}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div >
    );
};

export default RiskAssessmentSection;
