import toast from 'react-hot-toast'
import bitsFetch from '../../../../Utils/bitsFetch'
import { __ } from '../../../../Utils/i18nwrap'

export const getWPForoForums = (data, setFlow) => {
  const loadForum = bitsFetch(null, 'wpforo/get/forums', null, 'GET')
    .then((result) => {
      if (result && result.data) {
        const tmpFlow = { ...data }
        tmpFlow.flow_details.forums = result.data
        setFlow({ ...tmpFlow })
        return 'Forums fetched successfully'
      }
      return 'Forums fetching failed. please try again'
    })
  toast.promise(loadForum, {
    success: (data) => data,
    error: __('Error Occurred', 'bit-integrations'),
    loading: __('Loading Forums...'),
  })
}

export const getWPForoTopics = (data, setFlow) => {
  const loadForum = bitsFetch(null, 'wpforo/get/topics', null, 'GET')
    .then((result) => {
      if (result && result.data) {
        const tmpFlow = { ...data }
        tmpFlow.flow_details.topics = result.data
        setFlow({ ...tmpFlow })
        return 'Topics fetched successfully'
      }
      return 'Topics fetching failed. please try again'
    })
  toast.promise(loadForum, {
    success: (data) => data,
    error: __('Error Occurred', 'bit-integrations'),
    loading: __('Loading Topics...'),
  })
}
