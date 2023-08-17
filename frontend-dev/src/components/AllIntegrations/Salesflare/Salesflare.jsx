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
import SalesflareAuthorization from './SalesflareAuthorization'
import { checkMappedFields, handleInput } from './SalesflareCommonFunc'
import SalesflareIntegLayout from './SalesflareIntegLayout'

function Salesflare({ formFields, setFlow, flow, allIntegURL }) {
  const navigate = useNavigate()
  const [isLoading, setIsLoading] = useState(false)
  const [loading, setLoading] = useState({})

  const [step, setStep] = useState(1)
  const [snack, setSnackbar] = useState({ show: false })

  const customerFields = [
    { key: 'customer_name', label: 'Full Name', required: true },
    { key: 'mobile_no', label: 'Mobile Number', required: false },
    { key: 'email_id', label: 'Email Address', required: false },
    { key: 'primary_address', label: 'Primary Address', required: false },
    { key: 'customer_primary_contact', label: 'customer Primary Contact', required: false },
    { key: 'customer_primary_address', label: 'customer Primary Address', required: false }
  ]

  const contactFields = [
    { key: 'first_name', label: 'First Name', required: true },
    { key: 'middle_name', label: 'Middle Name', required: false },
    { key: 'last_name', label: 'Last Name', required: false },
    { key: 'email_id', label: 'Email Address', required: false },
    { key: 'designation', label: 'Designation', required: false },
    { key: 'phone', label: 'Phone Number', required: false },
    { key: 'mobile_no', label: 'Mobile Number', required: false },
    { key: 'gender', label: 'Gender', required: false },
    { key: 'department', label: 'Department', required: false },
    { key: 'company_name', label: 'Company Name', required: false },
  ]

  const leadFields = [
    { key: 'lead_name', label: 'Person Name', required: true },
    { key: 'company_name', label: 'Organization Name', required: true },
    { key: 'email_id', label: 'Email Address', required: false },
    { key: 'title', label: 'Title', required: false },
    { key: 'phone', label: 'Phone Number', required: false },
    { key: 'mobile_no', label: 'Mobile Number', required: false },
    { key: 'fax', label: 'Fax', required: false },
    { key: 'designation', label: 'Designation', required: false },
    { key: 'gender', label: 'Gender', required: false },
    { key: 'campaign_name', label: 'Campaign Name', required: false },
    { key: 'website', label: 'Website', required: false },
    { key: 'notes', label: 'Notes', required: false },
    { key: 'address_title', label: 'Address Title', required: false },
    { key: 'address_line1', label: 'Address Line 1', required: false },
    { key: 'address_line2', label: 'Address Line 2', required: false },
    { key: 'city', label: 'City', required: false },
    { key: 'county', label: 'County', required: false },
    { key: 'state', label: 'State', required: false },
    { key: 'pincode', label: 'Postal Code', required: false },
    { key: 'country', label: 'Country', required: false },
  ]

  const [salesflareConf, setSalesflareConf] = useState({
    name: 'Salesflare',
    type: 'Salesflare',
    api_key: process.env.NODE_ENV === 'development' ? 'qL2oAebEsF6UM-QZ84t6WgeYm4aBQ1cwkPPOQfwDPM_wD' : '',
    field_map: [
      { formField: '', salesflareFormField: '' },
    ],
    actionName: '',
    actionId: '',
    customerFields,
    contactFields,
    leadFields,
    actions: {},
  })

  const saveConfig = () => {
    setIsLoading(true)
    const resp = saveIntegConfig(flow, setFlow, allIntegURL, salesflareConf, navigate, '', '', setIsLoading)
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

    if (!checkMappedFields(salesflareConf)) {
      toast.error('Please map mandatory fields')
      return
    }

    if (salesflareConf.actionName === 'customer' && !salesflareConf.selectedCustomerType) {
      toast.error('Please select Customer Type')
      return
    }
    if (salesflareConf.actionName === 'lead' && !salesflareConf.selectedLeadStatus) {
      toast.error('Please select Lead Status')
      return
    }

    salesflareConf.field_map.length > 0 && setStep(pageNo)
  }

  return (
    <div>
      <SnackMsg snack={snack} setSnackbar={setSnackbar} />
      <div className="txt-center mt-2"><Steps step={3} active={step} /></div>

      {/* STEP 1 */}
      <SalesflareAuthorization
        salesflareConf={salesflareConf}
        setSalesflareConf={setSalesflareConf}
        step={step}
        setStep={setStep}
        loading={loading}
        setLoading={setLoading}
        setSnackbar={setSnackbar}
      />

      {/* STEP 2 */}
      <div className="btcd-stp-page" style={{ ...(step === 2 && { width: 900, height: 'auto', overflow: 'visible' }) }}>

        <SalesflareIntegLayout
          formFields={formFields}
          handleInput={(e) => handleInput(e, salesflareConf, setSalesflareConf, setLoading, setSnackbar)}
          salesflareConf={salesflareConf}
          setSalesflareConf={setSalesflareConf}
          loading={loading}
          setLoading={setLoading}
          isLoading={isLoading}
          setIsLoading={setIsLoading}
          setSnackbar={setSnackbar}
        />

        {salesflareConf?.actionName && (
          <button
            onClick={() => nextPage(3)}
            disabled={!(checkMappedFields(salesflareConf))}
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
      {salesflareConf?.actionName && (
        <IntegrationStepThree
          step={step}
          saveConfig={() => saveConfig()}
          isLoading={isLoading}
          dataConf={salesflareConf}
          setDataConf={setSalesflareConf}
          formFields={formFields}
        />
      )}
    </div>
  )
}

export default Salesflare
