/* eslint-disable no-console */
/* eslint-disable no-unused-expressions */
import { useState } from 'react'
import toast from 'react-hot-toast'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import { useNavigate } from 'react-router-dom'
import { __ } from '../../../Utils/i18nwrap'
import SnackMsg from '../../Utilities/SnackMsg'
import Steps from '../../Utilities/Steps'
import { saveIntegConfig } from '../IntegrationHelpers/IntegrationHelpers'
import IntegrationStepThree from '../IntegrationHelpers/IntegrationStepThree'
import WoodpeckerAuthorization from './WoodpeckerAuthorization'
import { checkMappedFields, handleInput } from './WoodpeckerCommonFunc'
import WoodpeckerIntegLayout from './WoodpeckerIntegLayout'

function Woodpecker({ formFields, setFlow, flow, allIntegURL }) {
  const navigate = useNavigate()
  const [isLoading, setIsLoading] = useState(false)
  const [loading, setLoading] = useState({})

  const [step, setStep] = useState(1)
  const [snack, setSnackbar] = useState({ show: false })

  const accountFields = [
    { key: 'name', label: 'Account name', required: true },
    { key: 'website', label: 'Account website', required: false },
    { key: 'description', label: 'Description', required: false },
    { key: 'size', label: 'Size', required: false },
    { key: 'email', label: 'Account email', required: false },
    { key: 'phone_number', label: 'Phone', required: false },
    { key: 'street', label: 'Street', required: false },
    { key: 'zip', label: 'Zip/Postal Code', required: false },
    { key: 'city', label: 'City', required: false },
    { key: 'region', label: 'State/Region', required: false },
    { key: 'country', label: 'Country', required: false },
  ]

  const contactFields = [
    { key: 'firstname', label: 'First Name', required: true },
    { key: 'Prefix', label: 'Prefix', required: false },
    { key: 'middle', label: 'Middle Name', required: false },
    { key: 'lastname', label: 'Last Name', required: false },
    { key: 'email', label: 'Email Address', required: true },
    { key: 'suffix', label: 'Suffix', required: false },
    { key: 'phone_number', label: 'Phone Number', required: false },
    { key: 'role', label: 'Role', required: false },
    { key: 'organisation', label: 'Organisation', required: false },
    { key: 'street', label: 'Street', required: false },
    { key: 'zip', label: 'Zip/Postal Code', required: false },
    { key: 'city', label: 'City', required: false },
    { key: 'region', label: 'State/Region', required: false },
    { key: 'country', label: 'Country', required: false },
  ]

  const opportunitiyFields = [
    { key: 'name', label: 'Opportunity name', required: true },
    { key: 'value', label: 'Value', required: false },
    { key: 'start_date', label: 'Start date', required: false },
    { key: 'close_date', label: 'Close date', required: false },
  ]

  const [woodpeckerConf, setWoodpeckerConf] = useState({
    name: 'Woodpecker',
    type: 'Woodpecker',
    api_key: process.env.NODE_ENV === 'development' ? '411340.5e782bcaf07998f5344948482d2e862630f438756971599d0c95f76c5b7098f7' : '',
    field_map: [
      { formField: '', woodpeckerFormField: '' },
    ],
    actionName: '',
    actionId: '',
    accountFields,
    contactFields,
    opportunitiyFields,
    actions: {},
  })

  const saveConfig = () => {
    setIsLoading(true)
    const resp = saveIntegConfig(flow, setFlow, allIntegURL, woodpeckerConf, navigate, '', '', setIsLoading)
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

    if (!checkMappedFields(woodpeckerConf)) {
      toast.error('Please map mandatory fields')
      return
    }

    if (woodpeckerConf.actionName === 'opportunities') {
      if (!woodpeckerConf.selectedAccount) {
        toast.error('Please select an Account')
        return
      }
      if (!woodpeckerConf.selectedPipeline) {
        toast.error('Please select a Pipeline')
        return
      }
      if (woodpeckerConf.selectedPipeline && !woodpeckerConf.selectedStage) {
        toast.error('Please select a Stage')
        return
      }
    }

    woodpeckerConf.field_map.length > 0 && setStep(pageNo)
  }

  return (
    <div>
      <SnackMsg snack={snack} setSnackbar={setSnackbar} />
      <div className="txt-center mt-2"><Steps step={3} active={step} /></div>

      {/* STEP 1 */}
      <WoodpeckerAuthorization
        woodpeckerConf={woodpeckerConf}
        setWoodpeckerConf={setWoodpeckerConf}
        step={step}
        setStep={setStep}
        loading={loading}
        setLoading={setLoading}
        setSnackbar={setSnackbar}
      />

      {/* STEP 2 */}
      <div className="btcd-stp-page" style={{ ...(step === 2 && { width: 900, height: 'auto', overflow: 'visible' }) }}>

        <WoodpeckerIntegLayout
          formFields={formFields}
          handleInput={(e) => handleInput(e, woodpeckerConf, setWoodpeckerConf, setLoading, setSnackbar)}
          woodpeckerConf={woodpeckerConf}
          setWoodpeckerConf={setWoodpeckerConf}
          loading={loading}
          setLoading={setLoading}
          isLoading={isLoading}
          setIsLoading={setIsLoading}
          setSnackbar={setSnackbar}
        />

        {woodpeckerConf?.actionName && (
          <button
            onClick={() => nextPage(3)}
            disabled={!(checkMappedFields(woodpeckerConf))}
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
      {woodpeckerConf?.actionName && (
        <IntegrationStepThree
          step={step}
          saveConfig={() => saveConfig()}
          isLoading={isLoading}
          dataConf={woodpeckerConf}
          setDataConf={setWoodpeckerConf}
          formFields={formFields}
        />
      )}
    </div>
  )
}

export default Woodpecker
