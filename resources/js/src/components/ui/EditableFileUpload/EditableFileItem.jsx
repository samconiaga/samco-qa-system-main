import React, { useEffect, useRef, useState } from "react";
import TextInput from "../TextInput";
import { Icon } from "@iconify/react";

export default function EditableFileItem({ file, onRename, onRemove }) {
    const [editing, setEditing] = useState(false);
    const [name, setName] = useState(file.name);
    const ref = useRef(null);

    const ext = file.name.split(".").pop();
    const baseName = name.replace(`.${ext}`, "");

    const save = () => {
        if (baseName.trim()) {
            onRename(file.id, `${baseName}.${ext}`);
        }
        setEditing(false);
    };

    useEffect(() => {
        const handler = e => {
            if (editing && ref.current && !ref.current.contains(e.target)) {
                save();
            }
        };
        document.addEventListener("mousedown", handler);
        return () => document.removeEventListener("mousedown", handler);
    }, [editing, name]);

    return (
        <div ref={ref} className="d-flex align-items-center gap-2 mb-2">
            <Icon icon="mdi:file-document-outline" fontSize={18} />

            <div className="flex-grow-1">
                {editing ? (
                    <TextInput
                        autoFocus
                        value={baseName}
                        onChange={e => setName(e.target.value)}
                        onKeyDown={e => e.key === "Enter" && save()}
                        className="form-control form-control-sm"
                    />
                ) : (
                    <span
                        className="text-primary text-decoration-underline"
                        onClick={() => setEditing(true)}
                        style={{ cursor: "pointer" }}
                    >
                        {file.name}
                    </span>
                )}
            </div>

            <button
                type="button"
                className="btn btn-sm btn-link text-danger p-0"
                onClick={() => onRemove(file.id)}
            >
                <Icon icon="mdi:close-circle-outline" fontSize={18} />
            </button>
        </div>
    );
}
