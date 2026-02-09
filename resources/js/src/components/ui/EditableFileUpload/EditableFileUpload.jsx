import React, { useEffect, useRef, useState } from 'react';
import Uppy from '@uppy/core';
import { Dashboard } from '@uppy/react';
import '@uppy/core/dist/style.css';
import '@uppy/dashboard/dist/style.css';
import FileListEditable from './FileListEditable';

export default function EditableFileUpload({
    existingFiles = [],
    onChange,
    onKeepChange,
    onNamesChange,
    maxFiles = 5,
    allowedFileTypes,
}) {
    const uppyRef = useRef(null);
    const initialized = useRef(false);
    const [items, setItems] = useState([]);

    if (!uppyRef.current) {
        uppyRef.current = new Uppy({
            autoProceed: false,
            restrictions: {
                maxNumberOfFiles: maxFiles,
                allowedFileTypes,
            },
        });
    }

    const uppy = uppyRef.current;

    // ===== PRELOAD =====
    useEffect(() => {
        if (!existingFiles.length || initialized.current) return;
        initialized.current = true;

        Promise.all(
            existingFiles.map(async (f) => {
                const res = await fetch(f.url);
                const blob = await res.blob();
                const name = f.name || decodeURIComponent(f.url.split('/').pop());
                const file = new File([blob], name, { type: blob.type });

                const uppyId = uppy.addFile({
                    name,
                    type: file.type,
                    data: file,
                    source: 'local',
                    meta: {
                        existing: true,
                        keepId: f.id, // 🔥 INI KUNCI
                    },
                });

                return {
                    id: uppyId,
                    file,
                    name,
                    existing: true,
                    keepId: f.id, // 🔥 disimpan juga di state
                };
            })
        ).then(setItems);

    }, [existingFiles]);



    // ===== ADD / REMOVE =====
    useEffect(() => {
        const onAdd = file => {
            if (!file.data) return;
            setItems(prev =>
                prev.some(f => f.id === file.id)
                    ? prev
                    : [
                        ...prev,
                        {
                            id: file.id,
                            file: file.data,
                            name: file.name,
                            existing: !!file.meta?.existing,
                        },
                    ]
            );
        };

        const onRemove = file => {
            setItems(prev => prev.filter(f => f.id !== file.id));
        };

        uppy.on('file-added', onAdd);
        uppy.on('file-removed', onRemove);

        return () => {
            uppy.off('file-added', onAdd);
            uppy.off('file-removed', onRemove);
        };
    }, []);
    // ===== SYNC KE PARENT =====
    useEffect(() => {
        onChange?.(
            items
                .filter(i => !i.existing)
                .map(i => i.file)
        );
        onKeepChange?.(
            items
                .filter(i => i.existing)
                .map(i => i.keepId)
        );

        onNamesChange?.(
            items.map(i => i.name)
        );
    }, [items]);

    const removeFile = id => {
        uppy.removeFile(id);
    };



    const renameFile = (id, newName) => {
        setItems(prev =>
            prev.map(f => f.id === id ? { ...f, name: newName } : f)
        );

        if (uppy.getFile(id)) {
            uppy.setFileState(id, { name: newName });
        }
    };

    return (
        <div className="border rounded p-3 bg-white">
            <Dashboard
                uppy={uppy}
                height={150}
                hideUploadButton
                proudlyDisplayPoweredByUppy={false}
            />

            <FileListEditable
                files={items}
                onRename={renameFile}
                onRemove={removeFile}
            />
        </div>
    );
}