/* eslint-disable no-param-reassign */

import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useRecoilState, useRecoilValue } from 'recoil'
import { $actionConf, $formFields, $newFlow } from '../../../GlobalStates'
import { __ } from '../../../Utils/i18nwrap'
import SnackMsg from '../../Utilities/SnackMsg'
import SetEditIntegComponents from '../IntegrationHelpers/SetEditIntegComponents'
import { saveActionConf } from '../IntegrationHelpers/IntegrationHelpers'
import IntegrationStepThree from '../IntegrationHelpers/IntegrationStepThree'
import JetEngineIntegLayout from './JetEngineIntegLayout'
import toast from 'react-hot-toast'
import { TASK_LIST_VALUES } from './jetEngineConstants'
import { checkMappedFields, handleInput } from './jetEngineCommonFunctions'

function EditJetEngine({ allIntegURL }) {
  const navigate = useNavigate()
  const [flow, setFlow] = useRecoilState($newFlow)
  const [jetEngineConf, setJetEngineConf] = useRecoilState($actionConf)
  const [isLoading, setIsLoading] = useState(false)
  const [loading, setLoading] = useState({
    auth: false,
    euFields: false,
    vendors: false
  })
  const [snack, setSnackbar] = useState({ show: false })
  const formField = useRecoilValue($formFields)

  const saveConfig = () => {
    if (!jetEngineConf.selectedTask) {
      toast.error('Please select a task!')
      return
    }

    if (
      (jetEngineConf.selectedTask === TASK_LIST_VALUES.UPDATE_VENDOR ||
        jetEngineConf.selectedTask === TASK_LIST_VALUES.WITHDRAW_REQUEST) &&
      !jetEngineConf.selectedVendor
    ) {
      toast.error('Please select a vendor!')
      return
    }

    if (
      jetEngineConf.selectedTask !== TASK_LIST_VALUES.DELETE_VENDOR &&
      !checkMappedFields(jetEngineConf)
    ) {
      toast.error('Please map mandatory fields!')
      return
    }

    if (
      jetEngineConf.selectedTask === TASK_LIST_VALUES.DELETE_VENDOR &&
      !jetEngineConf.selectedVendor &&
      !checkMappedFields(jetEngineConf)
    ) {
      toast.error('Please select a topic or map fields!')
      return
    }

    if (
      jetEngineConf.selectedTask === TASK_LIST_VALUES.WITHDRAW_REQUEST &&
      !jetEngineConf.selectedPaymentMethod
    ) {
      toast.error('Please select a payment method!')
      return
    }

    saveActionConf({
      flow,
      allIntegURL,
      conf: jetEngineConf,
      navigate,
      edit: 1,
      setLoading,
      setSnackbar
    })
  }

  return (
    <div style={{ width: 900 }}>
      <SnackMsg snack={snack} setSnackbar={setSnackbar} />

      <div className="flx mt-3">
        <b className="wdt-200 d-in-b">{__('Integration Name:', 'bit-integrations')}</b>
        <input
          className="btcd-paper-inp w-5"
          onChange={(e) => handleInput(e, jetEngineConf, setJetEngineConf)}
          name="name"
          defaultValue={jetEngineConf.name || ''}
          type="text"
          placeholder={__('Integration Name...', 'bit-integrations')}
        />
      </div>
      <br />

      <SetEditIntegComponents entity={flow.triggered_entity} setSnackbar={setSnackbar} />
      <JetEngineIntegLayout
        formID={flow.triggered_entity_id}
        formFields={formField}
        handleInput={(e) => handleInput(e, jetEngineConf, setJetEngineConf, setLoading, setSnackbar)}
        jetEngineConf={jetEngineConf}
        setJetEngineConf={setJetEngineConf}
        loading={loading}
        setLoading={setLoading}
        setSnackbar={setSnackbar}
      />

      <IntegrationStepThree
        edit
        saveConfig={saveConfig}
        // disabled={!jetEngineConf?.selectedLists}
        isLoading={isLoading}
        dataConf={jetEngineConf}
        setDataConf={setJetEngineConf}
        formFields={formField}
      />
      <br />
    </div>
  )
}

export default EditJetEngine
