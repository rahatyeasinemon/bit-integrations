/* eslint-disable no-console */
/* eslint-disable no-else-return */
import toast from 'react-hot-toast'
import bitsFetch from '../../../Utils/bitsFetch'
import { __ } from '../../../Utils/i18nwrap'

export const handleInput = (e, salesmateConf, setSalesmateConf) => {
  const newConf = { ...salesmateConf }
  const { name } = e.target
  if (e.target.value !== '') {
    newConf[name] = e.target.value
  } else {
    delete newConf[name]
  }
  setSalesmateConf({ ...newConf })
}

export const generateMappedField = (lmfwcFields) => {
  const requiredFlds = lmfwcFields.filter((fld) => fld.required === true)
  return requiredFlds.length > 0
    ? requiredFlds.map((field) => ({
        formField: '',
        lmfwcFormField: field.key
      }))
    : [{ formField: '', lmfwcFormField: '' }]
}

export const checkMappedFields = (licenseManagerConf) => {
  const mappedFields = licenseManagerConf?.field_map
    ? licenseManagerConf.field_map.filter(
        (mappedField) =>
          !mappedField.formField ||
          !mappedField.lmfwcFormField ||
          (mappedField.formField === 'custom' && !mappedField.customValue) ||
          (mappedField.lmfwcFormField === 'customFieldKey' && !mappedField.customFieldKey)
      )
    : []
  if (mappedFields.length > 0) {
    return false
  }
  return true
}

export const lmfwcAuthentication = (
  confTmp,
  setConf,
  setError,
  setIsAuthorized,
  loading,
  setLoading
) => {
  if (!confTmp.api_key || !confTmp.api_secret) {
    setError({
      base_url: !confTmp.base_url ? __("Consumer key can't be empty", 'bit-integrations') : '',
      api_key: !confTmp.api_key ? __("Consumer key can't be empty", 'bit-integrations') : '',
      api_secret: !confTmp.api_secret ? __("Consumer secret can't be empty", 'bit-integrations') : ''
    })
    return
  }

  setError({})
  setLoading({ ...loading, auth: true })

  const requestParams = {
    base_url: confTmp.base_url,
    api_key: confTmp.api_key,
    api_secret: confTmp.api_secret
  }

  bitsFetch(requestParams, 'lmfwc_authentication').then((result) => {
    if (result && result.success) {
      setIsAuthorized(true)
      setLoading({ ...loading, auth: false })
      toast.success(__('Authorized Successfully', 'bit-integrations'))
      return
    }
    setLoading({ ...loading, auth: false })
    toast.error(
      result?.data && typeof result.data === 'string'
        ? result.data
        : __('Authorized failed, Please enter valid Consumer key & Consumer secret', 'bit-integrations')
    )
  })
}

export const getAllEvents = (confTmp, setConf, setLoading) => {
  setLoading({ ...setLoading, event: true })

  const requestParams = {
    api_key: confTmp.api_key,
    api_secret: confTmp.api_secret
  }

  bitsFetch(requestParams, 'lmfwc_fetch_all_events').then((result) => {
    if (result && result.success) {
      if (result.data) {
        setConf((prevConf) => {
          prevConf.events = result.data
          return prevConf
        })

        setLoading({ ...setLoading, event: false })
        toast.success(__('Events fetched successfully', 'bit-integrations'))
        return
      }
      setLoading({ ...setLoading, event: false })
      toast.error(__('Events Not Found!', 'bit-integrations'))
      return
    }
    setLoading({ ...setLoading, event: false })
    toast.error(__('Events fetching failed', 'bit-integrations'))
  })
}

export const getAllSessions = (confTmp, setConf, event_id, setLoading) => {
  setLoading({ ...setLoading, session: true })

  const requestParams = {
    api_key: confTmp.api_key,
    api_secret: confTmp.api_secret,
    event_id: event_id
  }

  bitsFetch(requestParams, 'lmfwc_fetch_all_sessions').then((result) => {
    if (result && result.success) {
      if (result.data) {
        setConf((prevConf) => {
          prevConf.sessions = result.data
          return prevConf
        })

        setLoading({ ...setLoading, session: false })
        toast.success(__('Sessions fetched successfully', 'bit-integrations'))
        return
      }
      setLoading({ ...setLoading, session: false })
      toast.error(__('Sessions Not Found!', 'bit-integrations'))
      return
    }
    setLoading({ ...setLoading, session: false })
    toast.error(__('Sessions fetching failed', 'bit-integrations'))
  })
}
