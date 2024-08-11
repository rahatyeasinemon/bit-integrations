/* eslint-disable no-console */
/* eslint-disable no-unused-expressions */
import { useState } from 'react'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import { useNavigate } from 'react-router-dom'
import toast from 'react-hot-toast'
import { __ } from '../../../Utils/i18nwrap'
import SnackMsg from '../../Utilities/SnackMsg'
import Steps from '../../Utilities/Steps'
import { saveIntegConfig } from '../IntegrationHelpers/IntegrationHelpers'
import IntegrationStepThree from '../IntegrationHelpers/IntegrationStepThree'
import { checkMappedFields } from './dokanCommonFunctions'
import DokanIntegLayout from './DokanIntegLayout'
import DokanAuthorization from './DokanAuthorization'
import { TASK_LIST_VALUES } from './dokanConstants'

function Dokan({ formFields, setFlow, flow, allIntegURL }) {
  const navigate = useNavigate()
  const [isLoading, setIsLoading] = useState(false)
  const [loading, setLoading] = useState({
    auth: false,
    reputation: false,
    groups: false,
    forums: false,
    topics: false,
    euFields: false
  })

  const [step, setStep] = useState(1)
  const [snack, setSnackbar] = useState({ show: false })

  const [dokanConf, setDokanConf] = useState({
    name: 'Dokan',
    type: 'Dokan',
    field_map: [],
    staticFields: [],
    selectedTask: '',
    groups: [],
    selectedGroup: '',
    reputations: [],
    selectedReputation: '',
    forums: [],
    selectedForum: '',
    selectedTags: '',
    actions: {},
    selectedTopic: '',
    topics: [],
    deleteTopicFieldMap: false
  })

  const saveConfig = () => {
    setIsLoading(true)
    const resp = saveIntegConfig(
      flow,
      setFlow,
      allIntegURL,
      dokanConf,
      navigate,
      '',
      '',
      setIsLoading
    )
    resp.then((res) => {
      if (res.success) {
        toast.success(res.data?.msg)
        navigate(allIntegURL)
      } else {
        toast.error(res.data || res)
      }
    })
  }

  const nextPage = (pageNo) => {
    setTimeout(() => {
      document.getElementById('btcd-settings-wrp').scrollTop = 0
    }, 300)

    if (!dokanConf.selectedTask) {
      toast.error('Please select a task!')
      return
    }

    if (dokanConf.selectedTask !== TASK_LIST_VALUES.DELETE_TOPIC && !checkMappedFields(dokanConf)) {
      toast.error('Please map mandatory fields!')
      return
    }

    if (
      dokanConf.selectedTask === TASK_LIST_VALUES.USER_REPUTATION &&
      !dokanConf.selectedReputation
    ) {
      toast.error('Please select a reputation!')
      return
    }

    if (dokanConf.selectedTask === TASK_LIST_VALUES.ADD_TO_GROUP && !dokanConf.selectedGroup) {
      toast.error('Please select a group!')
      return
    }

    if (dokanConf.selectedTask === TASK_LIST_VALUES.REMOVE_FROM_GROUP && !dokanConf.selectedGroup) {
      toast.error('Please select a group!')
      return
    }

    if (dokanConf.selectedTask === TASK_LIST_VALUES.CREATE_TOPIC && !dokanConf.selectedForum) {
      toast.error('Please select a forum!')
      return
    }

    if (
      dokanConf.selectedTask === TASK_LIST_VALUES.DELETE_TOPIC &&
      !dokanConf.selectedTopic &&
      !checkMappedFields(dokanConf)
    ) {
      toast.error('Please select a topic or map fields!')
      return
    }

    dokanConf.field_map.length > 0 && setStep(pageNo)
  }

  return (
    <div>
      <SnackMsg snack={snack} setSnackbar={setSnackbar} />
      <div className="txt-center mt-2">
        <Steps step={3} active={step} />
      </div>

      {/* STEP 1 */}
      <DokanAuthorization
        dokanConf={dokanConf}
        setDokanConf={setDokanConf}
        step={step}
        setStep={setStep}
        loading={loading}
        setLoading={setLoading}
        setSnackbar={setSnackbar}
      />

      {/* STEP 2 */}
      <div
        className="btcd-stp-page"
        style={{ ...(step === 2 && { width: 900, height: 'auto', overflow: 'visible' }) }}>
        <DokanIntegLayout
          formFields={formFields}
          dokanConf={dokanConf}
          setDokanConf={setDokanConf}
          loading={loading}
          setLoading={setLoading}
          setSnackbar={setSnackbar}
        />
        <button
          onClick={() => nextPage(3)}
          disabled={
            dokanConf?.selectedTask !== TASK_LIST_VALUES.DELETE_TOPIC &&
            !checkMappedFields(dokanConf)
          }
          className="btn f-right btcd-btn-lg purple sh-sm flx"
          type="button">
          {__('Next', 'bit-integrations')} &nbsp;
          <div className="btcd-icn icn-arrow_back rev-icn d-in-b" />
        </button>
      </div>

      {/* STEP 3 */}
      <IntegrationStepThree
        step={step}
        saveConfig={() => saveConfig()}
        isLoading={isLoading}
        dataConf={dokanConf}
        setDataConf={setDokanConf}
        formFields={formFields}
      />
    </div>
  )
}

export default Dokan
