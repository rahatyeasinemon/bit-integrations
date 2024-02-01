import Editor, { useMonaco } from "@monaco-editor/react";
import { memo, useRef } from "react";
import toast from "react-hot-toast";
import { __ } from '../../../Utils/i18nwrap';
import "./style.scss"

function JsonEditor({ data = {}, onChange, formFields = [] }) {
    const editorRef = useRef(null)
    const monaco = useMonaco()

    const handleEditorDidMount = (editor, monaco) => {
        editorRef.current = editor
        prettify()
    }

    const prettify = () => {
        editorRef.current.getAction('editor.action.formatDocument').run()
    }

    const handleEditorValidation = (markers) => {
        markers.forEach((marker) => toast.error(marker.message))
    }

    const handlePasteFormField = (field) => {
        const editor = editorRef.current
        if (editor) {
            const position = editor.getPosition()
            editor.executeEdits("", [
                {
                    range: new monaco.Range(position.lineNumber, position.column, position.lineNumber, position.column),
                    text: `"input_key": ${field},`,
                    forceMoveMarkers: true,
                },
            ])
        }
    }

    return (
        <div className="json-editor my-3 py-3">
            <Editor
                height="300px"
                width="60%"
                defaultLanguage="json"
                defaultValue={JSON.stringify(data)}
                theme="vs-dark"
                onValidate={handleEditorValidation}
                onMount={handleEditorDidMount}
                onChange={onChange}
            />
            <div className="form-fields flx p-atn">
                <select className="btcd-paper-inp" onChange={(e) => handlePasteFormField(e.target.value)}>
                    <option value="">{__('Form Fields', 'bit-integrations')}</option>
                    {formFields.map((field, key) => <option key={key} value={`"\${${field.name}}"`}>{field.label}</option>)}
                </select>
            </div>
            <button
                onClick={prettify}
                className="prettier-btn btn btcd-btn-sm sh-sm flx"
                type="button"
            >
                {__('{ } Prettify', 'bit-integrations')}
            </button>
        </div>
    );
}

export default memo(JsonEditor)
