import { useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import CustomApiIntegrationLayout from './CustomApiIntegrationLayout'
import { useRecoilState, useRecoilValue } from 'recoil'
import { $actionConf, $formFields, $newFlow } from '../../../GlobalStates'
import SnackMsg from '../../Utilities/SnackMsg'
import EditFormInteg from '../EditFormInteg'
import EditWebhookInteg from '../EditWebhookInteg'
import { checkWebhookIntegrationsExist, saveActionConf } from '../IntegrationHelpers/IntegrationHelpers'
import IntegrationStepThree from '../IntegrationHelpers/IntegrationStepThree'

export default function EditCustomApi({ allIntegURL }) {
    const navigate = useNavigate()
    const { formID } = useParams()

    const [customApiConf, setCustomApiConf] = useRecoilState($actionConf)
    const [isLoading, setIsLoading] = useState(false)
    const [flow, setFlow] = useRecoilState($newFlow)
    const formFields = useRecoilValue($formFields)
    const [snack, setSnackbar] = useState({ show: false })

    return (
        <div style={{ width: 900 }}>
            <SnackMsg snack={snack} setSnackbar={setSnackbar} />
            {!checkWebhookIntegrationsExist(flow.triggered_entity) && <EditFormInteg setSnackbar={setSnackbar} />}
            {checkWebhookIntegrationsExist(flow.triggered_entity) && <EditWebhookInteg setSnackbar={setSnackbar} />}
            <div className="mt-3">
                <CustomApiIntegrationLayout
                    formID={formID}
                    formFields={formFields}
                    customApiConf={customApiConf}
                    setCustomApiConf={setCustomApiConf}
                />
            </div>

            <IntegrationStepThree
                edit
                saveConfig={() => saveActionConf({ flow, setFlow, allIntegURL, conf: customApiConf, navigate, edit: 1, setIsLoading, setSnackbar })}
                isLoading={isLoading}
            />
            <br />
        </div>
    )
}
