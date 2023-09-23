/* eslint-disable no-param-reassign */

import { useState } from 'react'
import toast from 'react-hot-toast'
import { useNavigate } from 'react-router-dom'
import { useRecoilState, useRecoilValue } from 'recoil'
import { __ } from '../../../Utils/i18nwrap'
import EditFormInteg from '../EditFormInteg'
import EditWebhookInteg from '../EditWebhookInteg'
import { checkWebhookIntegrationsExist, saveActionConf } from '../IntegrationHelpers/IntegrationHelpers'
import IntegrationStepThree from '../IntegrationHelpers/IntegrationStepThree'
import { $actionConf, $formFields, $newFlow } from '../../../GlobalStates'
import AcademyLmsIntegLayout from './AcademyLmsIntegLayout'
import { handleInput } from './AcademyLmsCommonFunc'

function EditAcademyLms({ allIntegURL }) {
  const navigate = useNavigate()

  const [flow, setFlow] = useRecoilState($newFlow)
  const [academyLmsConf, setAcademyLmsConf] = useRecoilState($actionConf)
  const [isLoading, setIsLoading] = useState(false)
  const [snack, setSnackbar] = useState({ show: false })
  const formField = useRecoilValue($formFields)

  const saveConfig = () => {
    saveActionConf({ flow, allIntegURL, conf: academyLmsConf, navigate, edit: 1, setIsLoading, setSnackbar })
  }
  return (
    <div style={{ width: 900 }}>

      <div className="flx mt-3">
        <b className="wdt-200 d-in-b">{__('Integration Name:', 'bit-integrations')}</b>
        <input className="btcd-paper-inp w-5" onChange={e => handleInput(e, academyLmsConf, setAcademyLmsConf)} name="name" value={academyLmsConf.name} type="text" placeholder={__('Integration Name...', 'bit-integrations')} />
      </div>
      <br />
      {!checkWebhookIntegrationsExist(flow.triggered_entity) && <EditFormInteg setSnackbar={setSnackbar} />}
      {checkWebhookIntegrationsExist(flow.triggered_entity) && <EditWebhookInteg setSnackbar={setSnackbar} />}

      <AcademyLmsIntegLayout
        academyLmsConf={academyLmsConf}
        setAcademyLmsConf={setAcademyLmsConf}
        isLoading={isLoading}
        setIsLoading={setIsLoading}
      />

      <IntegrationStepThree
        edit
        saveConfig={saveConfig}
        disabled={false}
        isLoading={isLoading}
        dataConf={academyLmsConf}
        setDataConf={setAcademyLmsConf}
        formFields={formField}
      />
      <br />
    </div>
  )
}

export default EditAcademyLms
