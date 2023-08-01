/* eslint-disable no-unused-vars */
/* eslint-disable no-param-reassign */

import { useState } from 'react'
import { toast } from 'react-hot-toast'
import { useNavigate, useParams } from 'react-router-dom'
import { useRecoilState, useRecoilValue } from 'recoil'
import { $actionConf, $formFields, $newFlow } from '../../../GlobalStates'
import { __ } from '../../../Utils/i18nwrap'
import SnackMsg from '../../Utilities/SnackMsg'
import EditFormInteg from '../EditFormInteg'
import EditWebhookInteg from '../EditWebhookInteg'
import { checkWebhookIntegrationsExist, saveActionConf } from '../IntegrationHelpers/IntegrationHelpers'
import IntegrationStepThree from '../IntegrationHelpers/IntegrationStepThree'
import { checkMappedFields, handleInput } from './PerfexCRMCommonFunc'
import PerfexCRMIntegLayout from './PerfexCRMIntegLayout'

function EditPerfexCRM({ allIntegURL }) {
  const navigate = useNavigate()
  const { id } = useParams()
  const [flow, setFlow] = useRecoilState($newFlow)
  const [perfexCRMConf, setPerfexCRMConf] = useRecoilState($actionConf)
  const [isLoading, setIsLoading] = useState(false)
  const [loading, setLoading] = useState({})
  const [snack, setSnackbar] = useState({ show: false })
  const formField = useRecoilValue($formFields)

  const saveConfig = () => {
    if (!checkMappedFields(perfexCRMConf)) {
      setSnackbar({ show: true, msg: __('Please map mandatory fields', 'bit-integrations') })
      return
    }

    if ((perfexCRMConf.actionName === 'contact' || perfexCRMConf.actionName === 'lead') && !perfexCRMConf.selectedCustomer) {
      toast.error('Please select a Customer')
      return
    }
    if (perfexCRMConf.actionName === 'project') {
      if (!perfexCRMConf.selectedProjectStatus) {
        toast.error('Please select Project status')
        return
      }
      if (!perfexCRMConf.selectedProjectType) {
        toast.error('Please select Project Related With...')
        return
      }
      if (!perfexCRMConf.selectedbillingType) {
        toast.error('Please select a Billing type')
        return
      }
      if (perfexCRMConf.selectedProjectType === 'customer' && !perfexCRMConf.selectedCustomer) {
        toast.error('Please select a Customer')
        return
      }
      if (perfexCRMConf.selectedProjectType === 'lead' && !perfexCRMConf.selectedLead) {
        toast.error('Please select a Lead')
        return
      }
      if (Number(perfexCRMConf.selectedbillingType) === 1 && !perfexCRMConf.totalRate) {
        toast.error('Please select a Total Rate')
        return
      }
      if (Number(perfexCRMConf.selectedbillingType) === 2 && !perfexCRMConf.ratePerHour) {
        toast.error('Please select a Rate Per Hour')
        return
      }
    }

    saveActionConf({ flow, allIntegURL, conf: perfexCRMConf, navigate, edit: 1, setIsLoading, setSnackbar })
  }

  return (
    <div style={{ width: 900 }}>
      <SnackMsg snack={snack} setSnackbar={setSnackbar} />

      <div className="flx mt-3">
        <b className="wdt-200 d-in-b">{__('Integration Name:', 'bit-integrations')}</b>
        <input className="btcd-paper-inp w-5" onChange={e => handleInput(e, perfexCRMConf, setPerfexCRMConf)} name="name" value={perfexCRMConf.name} type="text" placeholder={__('Integration Name...', 'bit-integrations')} />
      </div>
      <br />
      {!checkWebhookIntegrationsExist(flow.triggered_entity) && <EditFormInteg setSnackbar={setSnackbar} />}
      {checkWebhookIntegrationsExist(flow.triggered_entity) && <EditWebhookInteg setSnackbar={setSnackbar} />}
      <PerfexCRMIntegLayout
        formID={flow.triggered_entity_id}
        formFields={formField}
        handleInput={(e) => handleInput(e, perfexCRMConf, setPerfexCRMConf, setLoading, setSnackbar)}
        perfexCRMConf={perfexCRMConf}
        setPerfexCRMConf={setPerfexCRMConf}
        loading={loading}
        setLoading={setLoading}
        setIsLoading={setIsLoading}
        setSnackbar={setSnackbar}
      />

      <IntegrationStepThree
        edit
        saveConfig={saveConfig}
        disabled={!checkMappedFields(perfexCRMConf)}
        isLoading={isLoading}
        dataConf={perfexCRMConf}
        setDataConf={setPerfexCRMConf}
        formFields={formField}
      />
      <br />
    </div>
  )
}

export default EditPerfexCRM
