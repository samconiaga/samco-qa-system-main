import React, { useEffect, useRef } from 'react';
import Uppy from '@uppy/core';
import { Dashboard } from '@uppy/react';
import '@uppy/core/dist/style.css';
import '@uppy/dashboard/dist/style.css';

export default function MultipleFileUpload({
    initialFileUrls = [],
    data,
    setData,
    fieldName,
    maxFiles = 5,
    allowedFileTypes = null,
    height = 300,
}) {
    const uppyRef = useRef(null);

    if (!uppyRef.current) {
        uppyRef.current = new Uppy({
            restrictions: {
                maxNumberOfFiles: maxFiles,
                allowedFileTypes,
            },
            autoProceed: false,
        });
    }

    const uppy = uppyRef.current;

    // 🔥 RESET FIELD FILE (PENTING)
    useEffect(() => {
        setData(fieldName, undefined);
    }, []);

    // ===============================
    // FILE BARU SAJA MASUK STATE
    // ===============================
    useEffect(() => {
        const handleAdd = (file) => {
            if (file.meta?.fromDB) return;

            setData(fieldName, prev =>
                prev ? [...prev, file.data] : [file.data]
            );
        };

        const handleRemove = (file) => {
            if (file.meta?.fromDB) return;

            setData(fieldName, prev =>
                (prev || []).filter(f => f !== file.data)
            );
        };

        uppy.on('file-added', handleAdd);
        uppy.on('file-removed', handleRemove);

        return () => {
            uppy.off('file-added', handleAdd);
            uppy.off('file-removed', handleRemove);
        };
    }, []);

    // ===============================
    // PRELOAD FILE DB (DISPLAY ONLY)
    // ===============================
    useEffect(() => {
        if (!initialFileUrls.length) return;

        Promise.all(
            initialFileUrls.map(url =>
                fetch(url)
                    .then(res => res.blob())
                    .then(blob =>
                        new File(
                            [blob],
                            url.split('/').pop(),
                            { type: blob.type }
                        )
                    )
            )
        ).then(files => {
            files.forEach(file => {
                uppy.addFile({
                    name: file.name,
                    type: file.type,
                    data: file,
                    source: 'server',
                    meta: { fromDB: true },
                });
            });
        });
    }, []);

    return (
        <Dashboard
            uppy={uppy}
            height={height}
            showProgressDetails
            hideUploadButton
            proudlyDisplayPoweredByUppy={false}
        />
    );
}
