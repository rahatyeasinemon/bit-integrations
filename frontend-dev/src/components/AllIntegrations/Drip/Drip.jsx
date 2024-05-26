// eslint-disable-next-line import/no-extraneous-dependencies
import { __ } from '@wordpress/i18n'
import { useState } from 'react'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import { useNavigate, useParams } from 'react-router-dom'
import BackIcn from '../../../Icons/BackIcn'
import Steps from '../../Utilities/Steps'
import { saveIntegConfig } from '../IntegrationHelpers/IntegrationHelpers'
import IntegrationStepThree from '../IntegrationHelpers/IntegrationStepThree'
import DripAuthorization from './DripAuthorization'
import { checkMappedFields, staticFields } from './DripCommonFunc'
import DripIntegLayout from './DripIntegLayout'
import toast from 'react-hot-toast'

function Drip({ formFields, setFlow, flow, allIntegURL }) {
  const navigate = useNavigate()
  const { formID } = useParams()
  const [isLoading, setIsLoading] = useState(false)
  const [step, setstep] = useState(1)
  const [loading, setLoading] = useState({
    auth: false,
    customFields: false,
    accounts: false
  })

  const [dripConf, setDripConf] = useState({
    name: 'Drip',
    type: 'Drip',
    api_token: process.env.NODE_ENV === 'development' ? 'fa2e5034e01d8e8f4f95b31ba8739918' : '',
    accounts: [],
    field_map: [
      { formField: '', dripField: '' },
    ],
    actions: {},
    selectedAccountId: '',
    dripFormFields: staticFields
  })

  const nextPage = (val) => {
    setTimeout(() => {
      document.getElementById('btcd-settings-wrp').scrollTop = 0
    }, 300)
    if (val === 3) {
      if (!checkMappedFields(dripConf)) {
        toast.error('Please map all required fields to continue.')
        return
      }
      if (!dripConf?.selectedAccountId) {
        toast.error('Please select account to continue.')
        return
      }
      if (dripConf.name !== '' && dripConf.field_map.length > 0) {
        setstep(3)
      }
    }
  }
  return (
    <div>
      <div className="txt-center mt-2"><Steps step={3} active={step} /></div>

      {/* STEP 1 */}
      <DripAuthorization
        formID={formID}
        dripConf={dripConf}
        setDripConf={setDripConf}
        step={step}
        setstep={setstep}
        loading={loading}
        setLoading={setLoading}
      />
      {/* STEP 2 */}
      <div className="btcd-stp-page" style={{ width: step === 2 && 900, height: step === 2 && 'auto' }}>

        <DripIntegLayout
          formID={formID}
          formFields={formFields}
          dripConf={dripConf}
          setDripConf={setDripConf}
          loading={loading}
          setLoading={setLoading}
        />
        <button
          onClick={() => nextPage(3)}
          disabled={!dripConf?.selectedAccountId || !checkMappedFields(dripConf) || dripConf.field_map.length < 1}
          className="btn f-right btcd-btn-lg green sh-sm flx"
          type="button"
        >
          {__('Next', 'bit-integrations')}
          {' '}
          &nbsp;
          <BackIcn className="ml-1 rev-icn" />
        </button>

      </div>

      {/* STEP 3 */}
      <IntegrationStepThree
        step={step}
        saveConfig={() => saveIntegConfig(flow, setFlow, allIntegURL, dripConf, navigate, '', '', setIsLoading)}
        isLoading={isLoading}
        dataConf={dripConf}
        setDataConf={setDripConf}
        formFields={formFields}
      />
    </div>
  )
}

export default Drip
