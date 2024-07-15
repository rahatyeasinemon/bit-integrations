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
import WPForoIntegLayout from './WPForoIntegLayout'
import toast from 'react-hot-toast'
import { TASK_LIST_VALUES } from './wpforoConstants'
import { checkMappedFields, handleInput } from './WPForoCommonFunc'

function EditWPForo({ allIntegURL }) {
  const navigate = useNavigate()
  const [flow, setFlow] = useRecoilState($newFlow)
  const [wpforoConf, setWPForoConf] = useRecoilState($actionConf)
  const [isLoading, setIsLoading] = useState(false)
  const [loading, setLoading] = useState({
    auth: false,
    reputation: false
  })
  const [snack, setSnackbar] = useState({ show: false })
  const formField = useRecoilValue($formFields)

  const saveConfig = () => {
    if (!wpforoConf.selectedTask) {
      toast.error('Please select a task!')
      return
    }

    if (!checkMappedFields(wpforoConf)) {
      toast.error('Please map mandatory fields!')
      return
    }

    if (
      wpforoConf.selectedTask === TASK_LIST_VALUES.USER_REPUTATION &&
      !wpforoConf.selectedReputation
    ) {
      toast.error('Please select a reputation!')
      return
    }

    if (wpforoConf.selectedTask === TASK_LIST_VALUES.ADD_TO_GROUP && !wpforoConf.selectedGroup) {
      toast.error('Please select a group!')
      return
    }

    if (
      wpforoConf.selectedTask === TASK_LIST_VALUES.REMOVE_FROM_GROUP &&
      !wpforoConf.selectedGroup
    ) {
      toast.error('Please select a group!')
      return
    }

    saveActionConf({
      flow,
      allIntegURL,
      conf: wpforoConf,
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
          onChange={(e) => handleInput(e, wpforoConf, setWPForoConf)}
          name="name"
          defaultValue={wpforoConf.name || ''}
          type="text"
          placeholder={__('Integration Name...', 'bit-integrations')}
        />
      </div>
      <br />

      <SetEditIntegComponents entity={flow.triggered_entity} setSnackbar={setSnackbar} />
      <WPForoIntegLayout
        formID={flow.triggered_entity_id}
        formFields={formField}
        handleInput={(e) => handleInput(e, wpforoConf, setWPForoConf, setLoading, setSnackbar)}
        wpforoConf={wpforoConf}
        setWPForoConf={setWPForoConf}
        loading={loading}
        setLoading={setLoading}
        setSnackbar={setSnackbar}
      />

      <IntegrationStepThree
        edit
        saveConfig={saveConfig}
        // disabled={!wpforoConf?.selectedLists}
        isLoading={isLoading}
        dataConf={wpforoConf}
        setDataConf={setWPForoConf}
        formFields={formField}
      />
      <br />
    </div>
  )
}

export default EditWPForo
