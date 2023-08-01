/* eslint-disable no-param-reassign */
import { useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { useRecoilState, useRecoilValue } from 'recoil'
import { $actionConf, $formFields, $newFlow } from '../../../GlobalStates'
import { __ } from '../../../Utils/i18nwrap'
import SnackMsg from '../../Utilities/SnackMsg'
import EditFormInteg from '../EditFormInteg'
import EditWebhookInteg from '../EditWebhookInteg'
import { checkWebhookIntegrationsExist, saveActionConf } from '../IntegrationHelpers/IntegrationHelpers'
import CustomActionStepTwo from './CustomActionStepTwo'
import CustomFuncEditor from './CustomFuncEditor'
import { checkFunctionValidity } from './CustomFunctionHelper'

function EditCustomAction({ allIntegURL }) {
  const navigate = useNavigate()
  const { formID } = useParams()

  const [customActionConf, setCustomActionConf] = useRecoilState($actionConf)
  const [flow, setFlow] = useRecoilState($newFlow)
  const formFields = useRecoilValue($formFields)
  const [isLoading, setIsLoading] = useState(false)
  const [snack, setSnackbar] = useState({ show: false })
  const [step, setStep] = useState(1)

  const handleChange = (e) => {
    const newConf = { ...customActionConf }
    if (e !== '') {
      newConf.value = e
    }
    setCustomActionConf({ ...newConf })
  }

  useEffect(() => {
    const newConf = { ...customActionConf }
    delete newConf.isValid
    setCustomActionConf({ ...newConf })
  }, [customActionConf?.value])

  return (
    <div>
      <SnackMsg snack={snack} setSnackbar={setSnackbar} />
      {!checkWebhookIntegrationsExist(flow.triggered_entity) && <EditFormInteg setSnackbar={setSnackbar} />}
      {checkWebhookIntegrationsExist(flow.triggered_entity) && <EditWebhookInteg setSnackbar={setSnackbar} />}
      <div className="mt-3">
        <div className="btcd-stp-page" style={{ ...(step === 1 && { width: '91%', height: 'auto', overflow: 'visible' }) }}>
          <h1>custom action</h1>
          <CustomFuncEditor
            customActionConf={customActionConf}
            setCustomActionConf={setCustomActionConf}
            formFields={formFields}
          />
          <button
            onClick={() => checkFunctionValidity(customActionConf, setCustomActionConf, setIsLoading)}
            disabled={!customActionConf.value}
            className="btn f-left btcd-btn-lg green sh-sm flx mt-5"
            type="button"
          >
            {__('Validated ', 'bit-integrations')}
          </button>
        </div>
      </div>

      <CustomActionStepTwo
        edit
        saveConfig={() => saveActionConf({ flow, setFlow, allIntegURL, conf: customActionConf, navigate, edit: 1, setIsLoading, setSnackbar })}
        isLoading={isLoading}
        disabled={!customActionConf.isValid}
      />
      <br />
    </div>
  )
}

export default EditCustomAction
