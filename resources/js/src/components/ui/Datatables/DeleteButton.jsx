import React from "react";
import { Icon } from "@iconify/react"; // atau spinner favoritmu

export default function DeleteButton({ type, isLoading, children, loadingType, ...props }) {
    return (

        <button
            type={type}
            className="btn btn-sm btn-danger me-1"
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
                    <Icon icon="mdi:trash" className="me-2" width="20" height="20" />
                    {children}
                </>
            )}
        </button>
    );
}
