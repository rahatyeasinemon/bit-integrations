import { useRecoilValue } from 'recoil'
import { $flowFormFields } from '../../../GlobalStates'
import { __ } from '../../../Utils/i18nwrap'
import { memo } from 'react'

function FlowFormFieldsOptions() {
  const flowFormFields = useRecoilValue($flowFormFields)
  return (
    <optgroup label={__('Form Fields', 'bit-integrations')}>
      {flowFormFields?.map((f, index) => (
        <option key={`ff-rm-${index}`} value={`\${${f.name}}`}>
          {f.label}
        </option>
      ))}
    </optgroup>
  )
}

export default memo(FlowFormFieldsOptions)
