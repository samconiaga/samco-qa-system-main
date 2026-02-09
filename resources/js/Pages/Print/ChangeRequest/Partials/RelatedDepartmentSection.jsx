import React from "react";
import { useTranslation } from "react-i18next";
import { toDateString } from "../../../../helper";
const RelatedDepartmentSection = ({ changeRequest }) => {
    const { t } = useTranslation();
    const isChecked = (condition) => {
        return condition ? "☑" : "☐";
    };
    return (
        <div style={{ marginTop: "0.5cm" }}>
            {/* === Header Nomor Usulan === */}
            <table className="print-table" style={{ width: "100%" }} >
                <tbody>
                    {/* ===K. KAJIAN DEPARTEMEN TERKAIT === */}
                    <tr>
                        <td colSpan={4}>
                            <b>K. {t('related_department_assessment')}</b>
                        </td>
                    </tr>
                    <tr>
                        <th style={{ textAlign: "center" }}>#</th>
                        <th style={{ textAlign: "center" }}>{t('assessment_by')}</th>
                        <th style={{ textAlign: "center" }}>{t('sign_and_date')}</th>
                        <th style={{ textAlign: "center" }}>{t('evaluation')}</th>
                    </tr>
                    {changeRequest?.follow_up_implementations.map((fup, index) => (
                        <tr key={fup.id || index}>
                            <td style={{ textAlign: "center" }}>{index + 1}</td>
                            <td>{fup?.employee?.employee_code}</td>
                            <td>{new Date(fup?.created_at).toLocaleString()}</td>

                            <td>{`(${t(fup?.evaluation_status)}) ${fup?.comments}`}</td>
                        </tr>
                    ))}

                    {/* ===L. Implementasi Tindak Lanjut === */}
                    <tr>
                        <td colSpan={4}>
                            <b>L. {t('follow_up_implementation')}</b>
                        </td>
                    </tr>
                    <tr>
                        <th style={{ textAlign: "center" }}>{t('impact_of_change')}</th>
                        <th style={{ textAlign: "center" }}>{t('PIC')}</th>
                        <th style={{ textAlign: "center" }}>{t('realization')}</th>
                        <th style={{ textAlign: "center" }}>{t('sign_and_date')}</th>
                    </tr>
                    {changeRequest?.action_plans.map((ap, index) => (
                        <tr key={ap.id || index}>
                            <td>{ap?.impact_category?.impact_of_change_category}</td>
                            <td>{ap?.department?.name}</td>
                            <td>{ap?.realization}</td>
                            <td style={{ height: "20px" }}>
                                {new Date(ap?.created_at).toLocaleString()}
                            </td>
                        </tr>
                    ))}

                </tbody>
            </table>
        </div >
    );
};

export default RelatedDepartmentSection;
