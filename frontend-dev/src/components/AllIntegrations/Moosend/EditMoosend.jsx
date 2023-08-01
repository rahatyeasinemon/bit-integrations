import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useRecoilState, useRecoilValue } from 'recoil'
import { $actionConf, $formFields, $newFlow } from '../../../GlobalStates'
import { __ } from '../../../Utils/i18nwrap'
import SnackMsg from '../../Utilities/SnackMsg'
import EditFormInteg from '../EditFormInteg'
import EditWebhookInteg from '../EditWebhookInteg'
import { checkWebhookIntegrationsExist } from '../IntegrationHelpers/IntegrationHelpers'
import IntegrationStepThree from '../IntegrationHelpers/IntegrationStepThree'
import { handleInput, saveUpdateConfig } from './MoosendCommonFunc'
import MoosendIntegLayout from './MoosendIntegLayout'

function EditMoosend({ allIntegURL }) {
  const navigate = useNavigate()
  const [snack, setSnackbar] = useState({ show: false })
  const flow = useRecoilValue($newFlow)
  const [moosendConf, setMoosendConf] = useRecoilState($actionConf)
  const [error, setError] = useState({ name: '', authKey: '' })
  const formFields = useRecoilValue($formFields)
  const [loading, setLoading] = useState({
    auth: false,
    list: false,
    page: false,
    update: false,
  })

  const setUpdateLoading = (value) => {
    setLoading({ ...loading, update: value })
  }
  return (
    <div style={{ width: 900 }}>
      <SnackMsg snack={snack} setSnackbar={setSnackbar} />

      <div className="flx mt-3">
        <b className="wdt-200 d-in-b">{__('Integration Name:', 'bit-integrations')}</b>
        <input className="btcd-paper-inp w-5" name="name" onChange={(e) => handleInput(e, moosendConf, setMoosendConf, error, setError)} value={moosendConf.name} type="text" placeholder={__('Integration Name...', 'bit-integrations')} />
      </div>
      <div className="my-3">
        {!checkWebhookIntegrationsExist(flow.triggered_entity) && <EditFormInteg setSnackbar={setSnackbar} />}
        {checkWebhookIntegrationsExist(flow.triggered_entity) && <EditWebhookInteg setSnackbar={setSnackbar} />}
      </div>

      <MoosendIntegLayout
        moosendConf={moosendConf}
        setMoosendConf={setMoosendConf}
        formFields={formFields}
        loading={loading}
        setLoading={setLoading}
      />

      <IntegrationStepThree
        edit
        saveConfig={() => { saveUpdateConfig(flow, allIntegURL, moosendConf, navigate, { edit: 1 }, setUpdateLoading) }}
        isLoading={loading.update}
        disabled={moosendConf.field_map.length < 1}
        dataConf={moosendConf}
        setDataConf={setMoosendConf}
        formFields={formFields}
      />

    </div>
  )
}

export default EditMoosend
