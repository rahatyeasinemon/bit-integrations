/* eslint-disable no-param-reassign */
// eslint-disable-next-line import/no-extraneous-dependencies
import { __ } from '../../../Utils/i18nwrap'
import { useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { useRecoilState, useRecoilValue } from 'recoil'
import SnackMsg from '../../Utilities/SnackMsg'
import IntegrationStepThree from '../IntegrationHelpers/IntegrationStepThree'
import { saveActionConf } from '../IntegrationHelpers/IntegrationHelpers'
import { handleInput } from './ConvertKitCommonFunc'
import ConvertKitIntegLayout from './ConvertKitIntegLayout'
import EditFormInteg from '../EditFormInteg'
import SetEditIntegComponents from '../IntegrationHelpers/SetEditIntegComponents'
import { $actionConf, $formFields, $newFlow } from '../../../GlobalStates'
import EditWebhookInteg from '../EditWebhookInteg'
import { create } from 'mutative'

function EditConvertKit({ allIntegURL }) {
  const navigate = useNavigate()
  const { id, formID } = useParams()

  const [convertKitConf, setConvertKitConf] = useRecoilState($actionConf)
  const [flow, setFlow] = useRecoilState($newFlow)
  const formFields = useRecoilValue($formFields)
  const [isLoading, setIsLoading] = useState(false)
  const [snack, setSnackbar] = useState({ show: false })

  useEffect(() => {
    if (!convertKitConf?.module) {
      setConvertKitConf(prevConf => create(prevConf, draftConf => {
        draftConf['module'] = 'add_subscriber_to_a_form'
      }))
    }
  }, [])


  return (
    <div style={{ width: 900 }}>
      <SnackMsg snack={snack} setSnackbar={setSnackbar} />

      <div className="flx mt-3">
        <b className="wdt-200 d-in-b">{__('Integration Name:', 'bit-integrations')}</b>
        <input
          className="btcd-paper-inp w-5"
          onChange={(e) => handleInput(e, convertKitConf, setConvertKitConf)}
          name="name"
          value={convertKitConf.name}
          type="text"
          placeholder={__('Integration Name...', 'bit-integrations')}
        />
      </div>
      <br />

      <SetEditIntegComponents entity={flow.triggered_entity} setSnackbar={setSnackbar} />
      <ConvertKitIntegLayout
        formID={formID}
        formFields={formFields}
        convertKitConf={convertKitConf}
        setConvertKitConf={setConvertKitConf}
        isLoading={isLoading}
        setIsLoading={setIsLoading}
        setSnackbar={setSnackbar}
      />

      <IntegrationStepThree
        edit
        saveConfig={() =>
          saveActionConf({
            flow,
            setFlow,
            allIntegURL,
            convertKitConf,
            navigate,
            conf: convertKitConf,
            edit: 1,
            setIsLoading,
            setSnackbar
          })
        }
        disabled={convertKitConf.field_map.length < 1}
        isLoading={isLoading}
        dataConf={convertKitConf}
        setDataConf={setConvertKitConf}
        formFields={formFields}
      />
      <br />
    </div>
  )
}

export default EditConvertKit
