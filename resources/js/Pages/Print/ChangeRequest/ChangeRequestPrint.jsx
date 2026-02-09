import { forwardRef } from "react";
import { useTranslation } from "react-i18next";
import Kop from "../Kop";
import ChangeInitiationSection from "./Partials/ChangeInitiationSection";
import RiskAssessmentSection from "./Partials/RiskAssessmentSection";
import RelatedDepartmentSection from "./Partials/RelatedDepartmentSection";
import RegulatorySection from "./Partials/RegulatorySection";
import LastVerificationSection from "./Partials/LastVerificationSection";

const ChangeRequestPrint = forwardRef(({ changeRequest }, ref) => {
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
                        {t("change_report")}
                    </div>
                    <div style={{ fontSize: "12pt", marginTop: "2px" }}>
                        {t("number")}: {changeRequest?.request_number || "-"}
                    </div>
                </div>

                {/* Inisiasi Perubahan */}
                <div className="mt-3">
                    <ChangeInitiationSection changeRequest={changeRequest} />
                </div>
            </div>

            {/* === Page Break === */}
            <div className="page-break"></div>

            {/* === Halaman 2 === */}
            <Kop />
            <div style={{ margin: "0cm 1cm" }}>
                {/* Halaman berikutnya (bisa Section B, C, dll) */}
                <div className="mt-3">
                    <RiskAssessmentSection changeRequest={changeRequest} />
                </div>
            </div>

            {/* === Page Break === */}
            <div className="page-break"></div>

            {/* === Halaman 3 === */}
            <Kop />
            <div style={{ margin: "0cm 1cm" }}>
                {/* Halaman berikutnya (bisa Section B, C, dll) */}
                <div className="mt-3">
                    <RelatedDepartmentSection changeRequest={changeRequest} />
                </div>
            </div>

            {/* === Page Break === */}
            <div className="page-break"></div>

            {/* === Halaman 4 === */}
            <Kop />
            <div style={{ margin: "0cm 1cm" }}>
                {/* Halaman berikutnya (bisa Section B, C, dll) */}
                <div className="mt-3">
                    <RegulatorySection changeRequest={changeRequest} />
                </div>
            </div>
            {/* === Page Break === */}
            <div className="page-break"></div>

             {/* === Halaman 5 === */}
            <Kop />
            <div style={{ margin: "0cm 1cm" }}>
                {/* Halaman berikutnya (bisa Section B, C, dll) */}
                <div className="mt-3">
                    <LastVerificationSection changeRequest={changeRequest} />
                </div>
            </div>


        </div>
    );
});

export default ChangeRequestPrint;
