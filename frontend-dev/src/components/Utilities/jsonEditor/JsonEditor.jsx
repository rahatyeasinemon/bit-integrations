import Editor, { useMonaco } from "@monaco-editor/react";
import { memo, useRef } from "react";
import toast from "react-hot-toast";
import { __ } from '../../../Utils/i18nwrap';
import "./style.scss"
import { useAsyncDebounce } from "react-table";
import MultiSelect from 'react-multiple-select-dropdown-lite'

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

    const handleEditorValidation = useAsyncDebounce((markers) => {
        markers.forEach((marker) => toast.error(marker.message))
    }, 1000)

    const handlePasteFormField = (field) => {
        if (!field) return
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
                defaultValue={typeof data === 'object' ? JSON.stringify(data) : data}
                theme="vs-dark"
                onValidate={handleEditorValidation}
                onMount={handleEditorDidMount}
                onChange={onChange}
            />
            <div className="form-fields flx p-atn">
                <MultiSelect
                    options={formFields.map(f => ({ label: f.label, value: `"\${${f.name}}"` }))}
                    className="btcd-paper-drpdwn wdt-200 ml-2"
                    singleSelect
                    onChange={val => handlePasteFormField(val)}
                    defaultValue={''}
                />
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
