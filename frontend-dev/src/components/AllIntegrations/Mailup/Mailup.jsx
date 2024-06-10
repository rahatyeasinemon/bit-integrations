/* eslint-disable no-console */
import { useEffect, useState } from 'react'
import toast from 'react-hot-toast'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import { useNavigate, useParams } from 'react-router-dom'
import { __ } from '../../../Utils/i18nwrap'
import SnackMsg from '../../Utilities/SnackMsg'
import Steps from '../../Utilities/Steps'
import { saveActionConf } from '../IntegrationHelpers/IntegrationHelpers'
import IntegrationStepThree from '../IntegrationHelpers/IntegrationStepThree'
import MailupAuthorization from './MailupAuthorization'
import { handleInput, setGrantTokenResponse, checkMappedFields } from './MailupCommonFunc'
import MailupIntegLayout from './MailupIntegLayout'

function Mailup({ formFields, setFlow, flow, allIntegURL }) {
  const navigate = useNavigate()
  const { formID } = useParams()
  const [isLoading, setIsLoading] = useState(false)
  const [step, setStep] = useState(1)
  const [snack, setSnackbar] = useState({ show: false })

  const [mailupConf, setMailupConf] = useState({
    name: 'Mailup',
    type: 'Mailup',
    clientId: process.env.NODE_ENV === 'development' ? '85e59098-8c70-46c6-b2a2-a95bf9c0a356' : '',
    clientSecret: process.env.NODE_ENV === 'development' ? '4e8c54cf-3c07-4c18-8d9c-47e0aa8ed6c1' : '',
    allList: [],
    allGroup: [],
    listId: '',
    groupId: '',
    field_map: [
      { formField: '', mailupFormField: '' },
    ],
    staticFields: [],
    actions: {},
  })

  useEffect(() => {
    // eslint-disable-next-line no-unused-expressions
    window.opener && setGrantTokenResponse('mailup')
  }, [])

  const nextPage = (pageNo) => {
    setTimeout(() => {
      document.getElementById('btcd-settings-wrp').scrollTop = 0
    }, 300)

    if (!checkMappedFields(mailupConf)) {
      toast.error('Please map mandatory fields')
      return
    }
    // eslint-disable-next-line no-unused-expressions
    mailupConf.field_map.length > 0 && setStep(pageNo)
  }

  return (
    <div>
      <SnackMsg snack={snack} setSnackbar={setSnackbar} />
      <div className="txt-center mt-2"><Steps step={3} active={step} /></div>

      {/* STEP 1 */}
      <MailupAuthorization
        formID={formID}
        mailupConf={mailupConf}
        setMailupConf={setMailupConf}
        step={step}
        setStep={setStep}
        isLoading={isLoading}
        setIsLoading={setIsLoading}
        setSnackbar={setSnackbar}
      />

      {/* STEP 2 */}
      <div className="btcd-stp-page" style={{ ...(step === 2 && { width: 900, height: 'auto', overflow: 'visible' }) }}>

        <MailupIntegLayout
          formFields={formFields}
          handleInput={(e) => handleInput(e, mailupConf, setMailupConf, setIsLoading, setSnackbar)}
          mailupConf={mailupConf}
          setMailupConf={setMailupConf}
          isLoading={isLoading}
          setIsLoading={setIsLoading}
          setSnackbar={setSnackbar}
        />

        {mailupConf.listId && (
          <button
            onClick={() => nextPage(3)}
            disabled={!mailupConf?.listId || !checkMappedFields(mailupConf)}
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
      {mailupConf.listId && (
        <IntegrationStepThree
          step={step}
          saveConfig={() => saveActionConf({ flow, setFlow, allIntegURL, navigate, conf: mailupConf, setIsLoading, setSnackbar })}
          isLoading={isLoading}
          dataConf={mailupConf}
          setDataConf={setMailupConf}
          formFields={formFields}
        />
      )}

    </div>
  )
}

export default Mailup
