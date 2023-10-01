import React, { useEffect, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import uuid from 'react-uuid'
import { __ } from '../../../Utils/i18nwrap'
import SnackMsg from '../../Utilities/SnackMsg'
import Steps from '../../Utilities/Steps'
import { saveIntegConfig } from '../IntegrationHelpers/IntegrationHelpers'
import CustomActionStepTwo from './CustomActionStepTwo'
import CustomFuncEditor from './CustomFuncEditor'
import { checkFunctionValidity } from './CustomFunctionHelper'
import TutorialLink from '../../Utilities/TutorialLink'
import tutorialLinks from '../../../Utils/StaticData/tutorialLinks'

const CustomAction = ({ formFields, setFlow, flow, allIntegURL }) => {
  const navigate = useNavigate()
  const [step, setStep] = useState(1)
  const [isLoading, setIsLoading] = useState(false)
  const [snack, setSnackbar] = useState({ show: false })
  const randomFileName = uuid()
  const { customAction } = tutorialLinks

  const [customActionConf, setCustomActionConf] = useState({
    name: 'CustomAction',
    type: 'CustomAction',
    randomFileName,
    defaultValue: `<?php if (!defined('ABSPATH')) {exit;} 
    
  function yourFunctionName($trigger){
    $trigger['yourKey'];
    //write here your custom function
  }  
  yourFunctionName($trigger);`,
    value: '',
  })
  useEffect(() => {
    const newConf = { ...customActionConf }
    delete newConf.isValid
    setCustomActionConf({ ...newConf })
  }, [customActionConf?.value])

  const nextPage = () => {
    setTimeout(() => {
      document.getElementById('btcd-settings-wrp').scrollTop = 0
    }, 300)
    setStep(2)
  }

  return (
    <div>
      <SnackMsg snack={snack} setSnackbar={setSnackbar} />
      <div className="txt-center mt-2"><Steps step={2} active={step} /></div>

      <div className="btcd-stp-page" style={{ ...(step === 1 && { width: '70%', height: 'auto', overflow: 'visible' }) }}>
        {customAction?.youTubeLink && (
          <TutorialLink
            title={customAction?.title}
            youTubeLink={customAction?.youTubeLink}
          />
        )}
        {customAction?.docLink && (
          <TutorialLink
            title={customAction?.title}
            docLink={customAction?.docLink}
          />
        )}

        <h1>custom action</h1>
        <CustomFuncEditor
          customActionConf={customActionConf}
          setCustomActionConf={setCustomActionConf}
          formFields={formFields}
        />
        <button
          onClick={() => checkFunctionValidity(customActionConf, setCustomActionConf, setIsLoading)}
          disabled={!customActionConf.value}
          className="btn f-left btcd-btn-lg green sh-sm flx mt-5"
          type="button"
        >
          {__('Validated ', 'bit-integrations')}
        </button>
        <button
          onClick={() => nextPage(2)}
          disabled={!customActionConf.isValid}
          className="btn f-right btcd-btn-lg green sh-sm flx mt-5"
          type="button"
        >
          {__('Next', 'bit-integrations')}
          {' '}
          &nbsp;
          <div className="btcd-icn icn-arrow_back rev-icn d-in-b" />
        </button>
      </div>

      <div className="btcd-stp-page" style={{ width: step === 2 && '100%', height: step === 2 && 'auto' }}>
        <CustomActionStepTwo
          step={step}
          saveConfig={() => saveIntegConfig(flow, setFlow, allIntegURL, customActionConf, navigate, '', '', setIsLoading)}
          isLoading={isLoading}
        />
      </div>
    </div>
  )
}

export default CustomAction
