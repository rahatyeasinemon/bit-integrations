import toast from 'react-hot-toast'
import bitsFetch from '../../../../Utils/bitsFetch'
import { __ } from '../../../../Utils/i18nwrap'

export const getFluentFluentBookingFields = (setFlowData, value, edit, setFormFields) => {
    const loadFields = bitsFetch(
        { id: value },
        'fluentbooking/get/fields',
        null,
        'POST',
    ).then((result) => {
        if (result && result.success) {
            if (edit) {
                setFormFields(result.data)
            } else {
                setFlowData(result.data, 'fields')
            }

            return 'Fetched fields successfully'
        }

        return 'Fields fetching failed. please try again'
    })

    toast.promise(loadFields, {
        success: (data) => data,
        error: __('Error Occurred', 'bit-integrations'),
        loading: __('Loading fields...'),
    })
}


export const getFluentBookingEvents = (data, setFlow) => {
    const loadEvents = bitsFetch(
        { id: 'fluentbooking' },
        'fluentbooking/get/form',
        null,
        'POST',
    ).then((result) => {
        if (result && result.success) {
            const tmpFlow = { ...data }
            tmpFlow.flow_details.events = result.data.events
            setFlow({ ...tmpFlow })
            return 'Fetched Events successfully'
        }
        return 'Events fetching failed. please try again'
    })
    toast.promise(loadEvents, {
        success: (data) => data,
        error: __('Error Occurred', 'bit-integrations'),
        loading: __('Loading Events...'),
    })
}
