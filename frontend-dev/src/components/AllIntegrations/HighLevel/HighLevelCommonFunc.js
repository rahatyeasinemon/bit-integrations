// eslint-disable-next-line import/no-extraneous-dependencies
import { __ } from '../../../Utils/i18nwrap'
import bitsFetch from '../../../Utils/bitsFetch'
import toast from 'react-hot-toast'
import { selector } from 'recoil'
import { TASK_LIST_VALUES } from './highlevelConstants'

export const handleInput = (e, highLevelConf, setHighLevelConf) => {
  const newConf = { ...highLevelConf }
  newConf.name = e.target.value
  setHighLevelConf({ ...newConf })
}

export const highLevelAuthentication = (highLevelConf, setHighLevelConf, setError, setisAuthorized, loading, setLoading,) => {
  setLoading({ ...loading, auth: true })
  const newConf = { ...highLevelConf }

  if (!newConf.name || !newConf.api_key) {
    setError({
      name: !newConf.name ? __("Integration name can't be empty", 'bit-integrations') : '',
      api_key: !newConf.api_key
        ? __("Access Api Token Key can't be empty", 'bit-integrations')
        : ''
    })
    return
  }

  bitsFetch({ api_key: newConf.api_key }, 'highLevel_authorization').then((result) => {
    if (result?.success) {
      setisAuthorized(true)
      toast.success('Authorized Successfully')
    } else {
      toast.error('Authorization Failed')
    }

    setLoading({ ...loading, auth: false, accounts: false })
  })
}

export const checkMappedFields = (highLevelConf) => {
  const mappedFields = highLevelConf?.field_map
    ? highLevelConf.field_map.filter(
      (mappedField) =>
        !mappedField.formField ||
        !mappedField.highLevelField ||
        (!mappedField.formField === 'custom' && !mappedField.customValue)
    )
    : []
  if (mappedFields.length > 0) {
    return false
  }
  return true
}

export const generateMappedField = (highLevelConf) => {
  const requiredFlds = highLevelConf?.highLevelFields.filter((fld) => fld.required === true)
  return requiredFlds.length > 0
    ? requiredFlds.map((field) => ({ formField: '', highLevelField: field.key }))
    : [{ formField: '', highLevelField: '' }]
}

export const getCustomFields = (confTmp, setConf, loading, setLoading) => {
  setLoading({ ...loading, customFields: true })

  bitsFetch({ api_key: confTmp.api_key }, 'get_highLevel_contact_custom_fields').then((result) => {
    if (result.success && result.data) {
      const newConf = { ...confTmp }
      newConf.highLevelFields = [...contactStaticFields, ...result.data]
      setConf(newConf)
      setLoading({ ...loading, customFields: false })
      toast.success(__('Custom fields fetch successfully', 'bit-integrations'))
      return
    }
    setLoading({ ...loading, customFields: false })
    toast.error(__('Custom fields fetch failed', 'bit-integrations'))
  })
}
export const getAllTags = (confTmp, setConf, setLoading) => {
  setLoading({ ...setLoading, tags: true })

  const requestParams = {
    apiToken: confTmp.api_key,
    selectedAccountId: confTmp.selectedAccountId
  }

  bitsFetch(requestParams, 'highLevel_fetch_all_tags').then((result) => {
    if (result && result.success) {
      const newConf = { ...confTmp }
      if (result.data) {
        newConf.tags = result.data
      }
      setConf(newConf)
      setLoading({ ...setLoading, tags: false })
      toast.success(__('Tags fetched successfully', 'bit-integrations'))
      return
    }
    setLoading({ ...setLoading, customFields: false })
    toast.error(__('Tags fetching failed', 'bit-integrations'))
  })
}

export const contactStaticFields = [
  { key: 'email', label: 'Email', required: true },
  { key: 'firstName', label: 'First Name', required: false },
  { key: 'lastName', label: 'Last Name', required: false },
  { key: 'name', label: 'Full Name', required: false },
  { key: 'phone', label: 'Phone', required: false },
  { key: 'dateOfBirth', label: 'Date of Birth', required: false },
  { key: 'address1', label: 'Address 1', required: false },
  { key: 'city', label: 'City', required: false },
  { key: 'state', label: 'State', required: false },
  { key: 'country', label: 'Country', required: false },
  { key: 'postalCode', label: 'postalCode (Zip)', required: false },
  { key: 'companyName', label: 'Company Name', required: false },
  { key: 'website', label: 'Website', required: false },
]

export const highLevelStaticFields = (selectedTask) => {
  if (selectedTask === TASK_LIST_VALUES.CREATE_CONTACT) {
    return {
      staticFields: contactStaticFields,
      fieldMap: [{ formField: '', highLevelField: 'email' }]
    }
  }
}
