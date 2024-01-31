import Editor, { useMonaco } from "@monaco-editor/react";
import { memo, useRef } from "react";
import toast from "react-hot-toast";
import { __ } from '../../../Utils/i18nwrap';

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
        console.log(markers)
        markers.forEach((marker) => toast.error(marker.message))
    }

    const handlePasteFormField = (field) => {
        const editor = editorRef.current
        if (editor) {
            const position = editor.getPosition()
            // const stringToPaste = field

            editor.executeEdits("", [
                {
                    range: new monaco.Range(position.lineNumber, position.column, position.lineNumber, position.column),
                    text: field,
                    forceMoveMarkers: true,
                },
            ])
        }
    }

    return (
        <div className="bg-white rounded border w-6 my-3 py-3 table-webhook-div shadow">
            <div className="d-flx" style={{ justifyContent: 'space-between' }}>
                <div className="flx p-atn w-6">
                    <select className="btcd-paper-inp w-6" onChange={(e) => handlePasteFormField(e.target.value)}>
                        <option value="">{__('Form Fields', 'bit-integrations')}</option>
                        {formFields.map((field, key) => <option key={key} value={`"\${${field.name}}"`}>{field.label}</option>)}
                    </select>
                </div>
                <button
                    onClick={prettify}
                    className="btn btcd-btn-lg green sh-sm flx"
                    type="button"
                >
                    {__('{ } Prettify', 'bit-integrations')}
                </button>
            </div>
            <Editor
                height="300px"
                defaultLanguage="json"
                defaultValue={JSON.stringify(data)}
                theme="vs-dark"
                onValidate={handleEditorValidation}
                onMount={handleEditorDidMount}
                onChange={onChange}
            />
        </div>
    );
}

export default memo(JsonEditor)
