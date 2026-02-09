import { Icon } from "@iconify/react/dist/iconify.js";
import { useReactToPrint } from "react-to-print";
import { useTranslation } from "react-i18next";
import { useState } from "react";

export default function PrintButton({
    contentRef, // ref ke elemen yang mau dicetak
    documentTitle = "Document", // default nama dokumen
    pageStyle, // optional: CSS print style
    onAfterPrint, // callback setelah print
    onBeforePrint, // callback sebelum print
    className = "btn btn-sm btn-secondary me-1",
    loadingType,
    children,
    ...props
}) {
    const { t } = useTranslation();
    const [isLoading, setIsLoading] = useState(false);
    // Konfigurasi dasar React-To-Print

    const handlePrint = useReactToPrint({
        contentRef,
        documentTitle,
        pageStyle:
            pageStyle ||
            `
        @page { size: A4; margin: 20mm; }
        @media print {
            body { -webkit-print-color-adjust: exact; }
        }
        `,
        onBeforePrint: async () => {
            setIsLoading(true);
            if (onBeforePrint) await onBeforePrint();
        },
        onAfterPrint: async () => {
            setIsLoading(false);
            if (onAfterPrint) await onAfterPrint();
        },
    });


    return (
        <button
            type="button"
            className={className}
            disabled={isLoading}
            onClick={handlePrint}
            {...props}
        >
            {isLoading ? (
                <>
                    <Icon icon="line-md:loading-loop" className="me-2" width="20" height="20" />
                    {loadingType === 1 && "Loading..."}
                </>
            ) : (
                <>
                    <Icon icon="mdi:printer" className="me-2" width="20" height="20" />
                    {children}
                </>
            )}
        </button>
    );
}
