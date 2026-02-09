import React, { useEffect, useRef } from 'react';
import Uppy from '@uppy/core';
import { Dashboard } from '@uppy/react';
import '@uppy/core/dist/style.css';
import '@uppy/dashboard/dist/style.css';

export default function SingleFileUpload({
    file, // ⬅️ File object dari parent
    onFileChange = () => { },
    maxFiles = 1,
    allowedFileTypes = null,
    height = 130,
}) {
    const uppyRef = useRef(null);

    if (!uppyRef.current) {
        uppyRef.current = new Uppy({
            restrictions: { maxNumberOfFiles: maxFiles, allowedFileTypes },
            autoProceed: false,
        });
    }

    const uppy = uppyRef.current;

    // sync React state → Uppy
    useEffect(() => {
        uppy.cancelAll();

        if (file instanceof File) {
            uppy.addFile({
                name: file.name,
                type: file.type,
                data: file,
            });
        }
    }, [file, uppy]);

    // sync Uppy → React state
    useEffect(() => {
        const handleAdd = (file) => onFileChange(file.data);
        const handleRemove = () => onFileChange(null);

        uppy.on('file-added', handleAdd);
        uppy.on('file-removed', handleRemove);

        return () => {
            uppy.off('file-added', handleAdd);
            uppy.off('file-removed', handleRemove);
        };
    }, [uppy, onFileChange]);

    return (
        <Dashboard
            uppy={uppy}
            height={height}
            hideUploadButton
            proudlyDisplayPoweredByUppy={false}
            showProgressDetails
        />
    );
}
