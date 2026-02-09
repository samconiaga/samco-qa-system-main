import { Icon } from "@iconify/react/dist/iconify.js";
import { useTranslation } from "react-i18next";

export default function EditButton({ type, isLoading, children,loadingType, ...props }) {
    return (
        <>
            <button
                type={type}
                className="btn btn-sm btn-warning me-1"
                disabled={isLoading}
                {...props}
            >
                {isLoading ? (
                    <>
                        <Icon icon="line-md:loading-loop" className=" me-2" width="20" height="20" />
                        {loadingType === 1 && "Loading..."}
                    </>
                ) : (
                    <>
                        <Icon icon="mdi:edit" className="me-2 " width="20" height="20" />
                        {children}
                    </>
                )}
            </button>
        </>
    );
}