import { customFormIntegrations, webhookIntegrations } from "../../../Utils/StaticData/webhookIntegrations";
import EditCustomFormSubmissionInteg from "../EditCustomFormSubmissionInteg";
import EditFormInteg from "../EditFormInteg";
import EditWebhookInteg from "../EditWebhookInteg";

export default function SetEditIntegComponents({ entity, setSnackbar }) {
    const EditInteg = () => {
        if (customFormIntegrations.includes(entity)) {
            return <EditCustomFormSubmissionInteg setSnackbar={setSnackbar} />
        } else if (webhookIntegrations.includes(entity)) {
            return <EditWebhookInteg setSnackbar={setSnackbar} />
        } else {
            return <EditFormInteg setSnackbar={setSnackbar} />
        }
    }

    return <EditInteg />
}
