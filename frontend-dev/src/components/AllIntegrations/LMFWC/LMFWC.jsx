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
import LMFWCAuthorization from './LMFWCAuthorization'
import { checkMappedFields, generateMappedField } from './LMFWCCommonFunc'
import LMFWCIntegLayout from './LMFWCIntegLayout'

function LMFWC({ formFields, setFlow, flow, allIntegURL }) {
  const navigate = useNavigate()
  const [isLoading, setIsLoading] = useState(false)
  const [loading, setLoading] = useState({})

  const [step, setStep] = useState(1)
  const [snack, setSnackbar] = useState({ show: false })

  const lmfwcFields = [
    { label: __('First Name', 'bit-integrations'), key: 'name', required: true },
    { label: __('Email Address', 'bit-integrations'), key: 'email', required: true },
    { label: __('Last Name', 'bit-integrations'), key: 'last_name', required: false },
    { label: __('Phone Number', 'bit-integrations'), key: 'phone_number', required: false },
    { label: __('Company', 'bit-integrations'), key: 'company', required: false },
    { label: __('Website', 'bit-integrations'), key: 'website', required: false },
    { label: __('GDPR', 'bit-integrations'), key: 'gdpr', required: false },
    {
      label: __('Event Registration page URL', 'bit-integrations'),
      key: 'ref_url',
      required: false
    }
  ]

  const [licenseManagerConf, setLicenseManagerConf] = useState({
    name: 'License Manager For WooCommerce',
    type: 'License Manager For WooCommerce',
    baseUrl: window.location.origin,
    api_key: process.env.NODE_ENV === 'development' ? 'ck_5782ba9801de81aa84d9e38745fae2f30b3a2eed' : '',
    api_secret: process.env.NODE_ENV === 'development' ? 'cs_2f075d046a3cc5b698481aeed618841bf3e9ccb3' : '',
    field_map: generateMappedField(lmfwcFields),
    actionName: 'registerPeopletoWabinar',
    lmfwcFields,
    actions: {}
  })

  const saveConfig = () => {
    setIsLoading(true)
    const resp = saveIntegConfig(
      flow,
      setFlow,
      allIntegURL,
      licenseManagerConf,
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

    if (!checkMappedFields(licenseManagerConf)) {
      toast.error(__('Please map mandatory fields', 'bit-integrations'))
      return
    }

    if (!licenseManagerConf.selectedEvent) {
      toast.error(__('Please select a Event', 'bit-integrations'))
      return
    }

    licenseManagerConf.field_map.length > 0 && setStep(pageNo)
  }

  return (
    <div>
      <SnackMsg snack={snack} setSnackbar={setSnackbar} />
      <div className="txt-center mt-2">
        <Steps step={3} active={step} />
      </div>

      {/* STEP 1 */}
      <LMFWCAuthorization
        licenseManagerConf={licenseManagerConf}
        setLicenseManagerConf={setLicenseManagerConf}
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
        <LMFWCIntegLayout
          formFields={formFields}
          licenseManagerConf={licenseManagerConf}
          setLicenseManagerConf={setLicenseManagerConf}
          loading={loading}
          setLoading={setLoading}
          isLoading={isLoading}
          setIsLoading={setIsLoading}
          setSnackbar={setSnackbar}
        />

        {licenseManagerConf?.actionName && (
          <button
            onClick={() => nextPage(3)}
            disabled={!checkMappedFields(licenseManagerConf)}
            className="btn f-right btcd-btn-lg purple sh-sm flx"
            type="button">
            {__('Next', 'bit-integrations')} &nbsp;
            <div className="btcd-icn icn-arrow_back rev-icn d-in-b" />
          </button>
        )}
      </div>

      {/* STEP 3 */}
      {licenseManagerConf?.actionName && (
        <IntegrationStepThree
          step={step}
          saveConfig={() => saveConfig()}
          isLoading={isLoading}
          dataConf={licenseManagerConf}
          setDataConf={setLicenseManagerConf}
          formFields={formFields}
        />
      )}
    </div>
  )
}

export default LMFWC
