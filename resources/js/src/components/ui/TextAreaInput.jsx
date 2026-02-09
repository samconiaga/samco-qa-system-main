import { forwardRef, useEffect, useImperativeHandle, useRef, useState } from "react";
import { CKEditor } from "@ckeditor/ckeditor5-react";
import { ClassicEditor, Essentials, Paragraph, Bold, Italic, List, Heading, Font } from "ckeditor5";
import ErrorInput from "./ErrorInput";
import "ckeditor5/ckeditor5.css";
import "../../../../css/ckeditor.css"

export default forwardRef(function RichTextInput(
  { className = "", isFocused = false, errorMessage, value = "", onChange, height = "300px", width = "100%", ...props },
  ref
) {
  const editorRef = useRef(null);
  const [localError, setLocalError] = useState(errorMessage);
  const [initialized, setInitialized] = useState(false); // Cegah sync berulang

  // Fokus ke editor dari parent
  useImperativeHandle(ref, () => ({
    focus: () => {
      editorRef.current?.focus();
    },
  }));

  // Update pesan error jika berubah
  useEffect(() => {
    setLocalError(errorMessage);
  }, [errorMessage]);

  // Set value awal sekali saja
  useEffect(() => {
    if (!initialized && value) {
      setInitialized(true);
    }
  }, [value, initialized]);

  return (
    <div className={className}>
      <CKEditor
        editor={ClassicEditor}
        data={value || ""}
        config={{
          licenseKey: 'GPL',
          plugins: [Essentials, Paragraph, Bold, Italic, List, Heading, Font],
          toolbar: ["undo", "redo", "|", "fontfamily", "fontsize", "fontColor", "fontBackgroundColor", "|", "bold", "italic", "|", "paragraph", "|", "numberedList", "bulletedList"],
        }}
        onReady={(editor) => {
          editorRef.current = editor.ui.view.editable.element;
          // Set tinggi dan lebar editor
          editor.ui.view.editable.element.style.height = height;
          editor.ui.view.editable.element.style.width = width;

          if (isFocused) {
            editor.editing.view.focus();
          }
        }}
        onFocus={(event, editor) => {
          const editable = editor.ui.view.editable.element;
          editable.style.height = height;
          editable.style.width = width;
        }}
        onChange={(event, editor) => {
          const data = editor.getData();
          setLocalError("");
          onChange?.({ target: { value: data } });
        }}
        {...props}
      />
      {localError && <ErrorInput message={localError} className="mt-2 text-danger" />}
    </div>
  );
});
