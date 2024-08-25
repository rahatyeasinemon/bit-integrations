import toast from 'react-hot-toast'
import bitsFetch from '../../../../Utils/bitsFetch'
import { __ } from '../../../../Utils/i18nwrap'

export const getWPJobManagerJobTypes = (data, setFlow) => {
  const loadJobTypes = bitsFetch(null, 'wpjobmanager/get/job-types', null, 'GET')
    .then((result) => {
      if (result && result.data) {
        const tmpFlow = { ...data }
        tmpFlow.flow_details.jobTypes = result.data
        setFlow({ ...tmpFlow })
        return 'Types fetched successfully'
      }
      return 'Types fetching failed. please try again'
    })
  toast.promise(loadJobTypes, {
    success: (data) => data,
    error: __('Error Occurred', 'bit-integrations'),
    loading: __('Loading Types...'),
  })
}
