import EditableFileItem from "./EditableFileItem";

export default function FileListEditable({ files, onRename, onRemove }) {
  if (!files.length) return null;

  return (
    <div className="mt-20 divide-y border rounded-md bg-gray-50">
      {files.map(file => (
        <EditableFileItem
          key={file.id}
          file={file}
          onRename={onRename}
          onRemove={onRemove}
        />
      ))}
    </div>
  );
}
