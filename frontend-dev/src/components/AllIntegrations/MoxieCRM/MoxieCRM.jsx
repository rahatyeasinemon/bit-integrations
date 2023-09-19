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
import MoxieCRMAuthorization from './MoxieCRMAuthorization'
import { checkMappedFields, handleInput } from './MoxieCRMCommonFunc'
import MoxieCRMIntegLayout from './MoxieCRMIntegLayout'

function MoxieCRM({ formFields, setFlow, flow, allIntegURL }) {
  const navigate = useNavigate()
  const [isLoading, setIsLoading] = useState(false)
  const [loading, setLoading] = useState({})

  const [step, setStep] = useState(1)
  const [snack, setSnackbar] = useState({ show: false })

  const companyFields = [
    { key: 'name', label: 'Name', required: true },
    { key: 'email_domain', label: 'Email', required: false },
    { key: 'details', label: 'Description', required: false },
    { key: 'street', label: 'Street', required: false },
    { key: 'city', label: 'City', required: false },
    { key: 'state', label: 'State', required: false },
    { key: 'postal_code', label: 'Postal Code', required: false },
    { key: 'country', label: 'Country', required: false },
    { key: 'phone_numbers', label: 'Phone', required: false },
    { key: 'websites', label: 'Website', required: false },
  ]

  const personFields = [
    { key: 'name', label: 'Name', required: true },
    { key: 'title', label: 'Title', required: false },
    { key: 'details', label: 'Description', required: false },
    { key: 'email_domain', label: 'Email', required: false },
    { key: 'phone_numbers', label: 'Phone', required: false },
    { key: 'street', label: 'Street', required: false },
    { key: 'city', label: 'City', required: false },
    { key: 'state', label: 'State', required: false },
    { key: 'postal_code', label: 'Postal Code', required: false },
    { key: 'country', label: 'Country', required: false },
    { key: 'websites', label: 'Website', required: false },
  ]

  const opportunityFields = [
    { key: 'name', label: 'Opportunity Name', required: true },
    { key: 'close_date', label: 'Close Date', required: false },
    { key: 'details', label: 'Opportunity Details', required: false },
    { key: 'monetary_value', label: 'Value', required: false },
  ]

  const taskFields = [
    { key: 'name', label: 'Task Name', required: true },
    { key: 'due_date', label: 'Due Date', required: false },
    { key: 'reminder_date', label: 'Reminder Date', required: false },
    { key: 'details', label: 'Description', required: false },
  ]

  const [moxiecrmConf, setMoxieCRMConf] = useState({
    name: 'MoxieCRM',
    type: 'MoxieCRM',
    api_key: process.env.NODE_ENV === 'development' ? '650812eea4abf602e48784f4.bb61de27cab66b7943d78f039cb108ea1a53e7b6c839754a3b4f7c824d4c5d45' : '',
    api_url: process.env.NODE_ENV === 'development' ? 'pod01.withmoxie.com' : '',
    field_map: [
      { formField: '', moxiecrmFormField: '' },
    ],
    actionName: '',
    companyFields,
    personFields,
    opportunityFields,
    taskFields,
    actions: {},
  })

  const saveConfig = () => {
    setIsLoading(true)
    const resp = saveIntegConfig(flow, setFlow, allIntegURL, moxiecrmConf, navigate, '', '', setIsLoading)
    resp.then(res => {
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

    if (!checkMappedFields(moxiecrmConf)) {
      toast.error('Please map mandatory fields')
      return
    }

    if (moxiecrmConf.actionName === 'opportunity') {
      if (!moxiecrmConf.selectedCRMPeople) {
        toast.error('Please select a people')
        return
      }
      if (!moxiecrmConf.selectedCRMPipelines && moxiecrmConf.actionName === 'opportunity') {
        toast.error('Please select a Pipeline')
        return
      }
    }

    moxiecrmConf.field_map.length > 0 && setStep(pageNo)
  }

  return (
    <div>
      <SnackMsg snack={snack} setSnackbar={setSnackbar} />
      <div className="txt-center mt-2"><Steps step={3} active={step} /></div>

      {/* STEP 1 */}
      <MoxieCRMAuthorization
        moxiecrmConf={moxiecrmConf}
        setMoxieCRMConf={setMoxieCRMConf}
        step={step}
        setStep={setStep}
        loading={loading}
        setLoading={setLoading}
        setSnackbar={setSnackbar}
      />

      {/* STEP 2 */}
      <div className="btcd-stp-page" style={{ ...(step === 2 && { width: 900, height: 'auto', overflow: 'visible' }) }}>

        <MoxieCRMIntegLayout
          formFields={formFields}
          handleInput={(e) => handleInput(e, moxiecrmConf, setMoxieCRMConf, setLoading, setSnackbar)}
          moxiecrmConf={moxiecrmConf}
          setMoxieCRMConf={setMoxieCRMConf}
          loading={loading}
          setLoading={setLoading}
          setSnackbar={setSnackbar}
        />

        {moxiecrmConf?.actionName && (
          <button
            onClick={() => nextPage(3)}
            disabled={!(checkMappedFields(moxiecrmConf))}
            className="btn f-right btcd-btn-lg green sh-sm flx"
            type="button"
          >
            {__('Next', 'bit-integrations')}
            {' '}
            &nbsp;
            <div className="btcd-icn icn-arrow_back rev-icn d-in-b" />
          </button>
        )}
      </div>

      {/* STEP 3 */}
      {moxiecrmConf?.actionName && (
        <IntegrationStepThree
          step={step}
          saveConfig={() => saveConfig()}
          isLoading={isLoading}
          dataConf={moxiecrmConf}
          setDataConf={setMoxieCRMConf}
          formFields={formFields}
        />
      )}
    </div>
  )
}

export default MoxieCRM
