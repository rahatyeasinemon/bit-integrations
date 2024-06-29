/* eslint-disable no-console */
/* eslint-disable no-else-return */
import toast from 'react-hot-toast'
import bitsFetch from '../../../Utils/bitsFetch'
import { __ } from '../../../Utils/i18nwrap'
import { create } from 'mutative'

export const handleInput = (e, mailsterConf, setMailsterConf) => {
  const newConf = create(mailsterConf, draftConf => {
    const { name } = e.target
    if (e.target.value !== '') {
      draftConf[name] = e.target.value
    } else {
      delete draftConf[name]
    }
  })

  setMailsterConf(newConf)
}

export const generateMappedField = (mailsterConf) => {
  const requiredFlds = mailsterConf?.staticFields.filter(fld => fld.required === true)
  return requiredFlds.length > 0 ? requiredFlds.map(field => ({ formField: '', mailsterFormField: field.key })) : [{ formField: '', mailsterFormField: '' }]
}

export const checkMappedFields = (mailsterConf) => {
  const mappedFields = mailsterConf?.field_map ? mailsterConf.field_map.filter(mappedField => (!mappedField.formField || !mappedField.mailsterFormField || (!mappedField.formField === 'custom' && !mappedField.customValue))) : []
  if (mappedFields.length > 0) {
    return false
  }
  return true
}

export const mailsterAuthentication = (confTmp, setError, setIsAuthorized, loading, setLoading) => {
  if (!confTmp.name) {
    setError({ name: !confTmp.name ? __('Name can\'t be empty', 'bit-integrations') : '' })
    return
  }

  setLoading({ ...loading, auth: true })
  bitsFetch({}, 'mailster_authentication')
    .then(result => {
      if (result.success) {
        setIsAuthorized(true)
        toast.success(__('Connected Successfully', 'bit-integrations'))
        setLoading({ ...loading, auth: false })
        return
      }
      setLoading({ ...loading, auth: false })
      toast.error(__('Connection failed: install and active Mailster plugin first!', 'bit-integrations'))
    })
}

export const staticFields = [
  { key: 'email', label: 'Email', required: true },
  { key: 'name', label: 'First Name', required: false },
  { key: 'surname', label: 'Last Name', required: false },
  { key: 'status', label: 'Status', required: false },
  { key: 'gender', label: 'Gender', required: false },
  { key: 'country', label: 'Country Code', required: false },
  { key: 'region', label: 'Region', required: false },
  { key: 'city', label: 'City', required: false },
  { key: 'profile_1', label: 'Custom Field 1', required: false },
  { key: 'profile_2', label: 'Custom Field 2', required: false },
  { key: 'profile_3', label: 'Custom Field 3', required: false },
  { key: 'profile_4', label: 'Custom Field 4', required: false },
  { key: 'profile_5', label: 'Custom Field 5', required: false },
  { key: 'profile_6', label: 'Custom Field 6', required: false },
  { key: 'profile_7', label: 'Custom Field 7', required: false },
  { key: 'profile_8', label: 'Custom Field 8', required: false },
  { key: 'profile_9', label: 'Custom Field 9', required: false },
  { key: 'profile_10', label: 'Custom Field 10', required: false },
  { key: 'profile_11', label: 'Custom Field 11', required: false },
  { key: 'profile_12', label: 'Custom Field 12', required: false },
  { key: 'profile_13', label: 'Custom Field 13', required: false },
  { key: 'profile_14', label: 'Custom Field 14', required: false },
  { key: 'profile_15', label: 'Custom Field 15', required: false },
  { key: 'profile_16', label: 'Custom Field 16', required: false },
  { key: 'profile_17', label: 'Custom Field 17', required: false },
  { key: 'profile_18', label: 'Custom Field 18', required: false },
  { key: 'profile_19', label: 'Custom Field 19', required: false },
  { key: 'profile_20', label: 'Custom Field 20', required: false },
]

export const listsOptions = () => {
  const options = []

  for (let i = 1; i <= 40; i++) {
    options.push({ label: 'List ' + i, value: i.toString() })
  }

  return options
}
