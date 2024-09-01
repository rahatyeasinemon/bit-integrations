import { __, sprintf } from '../../Utils/i18nwrap'

export default function Note({ note, isInstruction = false, isHeadingNull = false }) {
  return (
    <div className="note">
      {!isHeadingNull && (
        <h4 className="mt-0">
          {sprintf(__('%s', 'bit-integrations'), isInstruction ? 'Instruction' : 'Note')}
        </h4>
      )}
      <div
        className="note-text"
        dangerouslySetInnerHTML={{ __html: __(note, 'bit-integrations') }}
      />
    </div>
  )
}
