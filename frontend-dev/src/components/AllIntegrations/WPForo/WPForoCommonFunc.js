/* eslint-disable no-console */
/* eslint-disable no-else-return */
import toast from 'react-hot-toast'
import bitsFetch from '../../../Utils/bitsFetch'
import { __ } from '../../../Utils/i18nwrap'
import { create } from 'mutative'
import { TASK_LIST_VALUES } from './wpforoConstants'

export const handleInput = (e, wpforoConf, setWPForoConf) => {
  const newConf = create(wpforoConf, draftConf => {
    const { name } = e.target
    if (e.target.value !== '') {
      draftConf[name] = e.target.value
    } else {
      delete draftConf[name]
    }
  })

  setWPForoConf(newConf)
}

export const checkMappedFields = (wpforoConf) => {
  const mappedFields = wpforoConf?.field_map ? wpforoConf.field_map.filter(mappedField => (!mappedField.formField || !mappedField.wpforoField || (!mappedField.formField === 'custom' && !mappedField.customValue))) : []
  if (mappedFields.length > 0) {
    return false
  }
  return true
}

export const wpforoAuthentication = (confTmp, setError, setIsAuthorized, loading, setLoading) => {
  if (!confTmp.name) {
    setError({ name: !confTmp.name ? __('Name can\'t be empty', 'bit-integrations') : '' })
    return
  }

  setLoading({ ...loading, auth: true })
  bitsFetch({}, 'wpforo_authentication')
    .then(result => {
      if (result.success) {
        setIsAuthorized(true)
        toast.success(__('Connected Successfully', 'bit-integrations'))
        setLoading({ ...loading, auth: false })
        return
      }
      setLoading({ ...loading, auth: false })
      toast.error(__('Connection failed: install and active WPForo plugin first!', 'bit-integrations'))
    })
}

export const getWPForoReputations = (confTmp, setConf, setLoading) => {
  setLoading({ ...setLoading, reputation: true })

  bitsFetch({}, 'wpforo_fetch_reputations')
    .then(result => {
      if (result && result.success) {
        const newConf = { ...confTmp }
        if (result.data) {
          newConf.reputations = result.data
        }
        setConf(newConf)
        setLoading({ ...setLoading, reputation: false })

        toast.success(__('Reputations fetch successfully', 'bit-integrations'))
        return
      }
      setLoading({ ...setLoading, reputation: false })
      toast.error(__(result?.data ? result.data : 'Something went wrong!', 'bit-integrations'))
    })
}

export const getWPForoGroups = (confTmp, setConf, setLoading) => {
  setLoading({ ...setLoading, groups: true })

  bitsFetch({}, 'wpforo_fetch_groups')
    .then(result => {
      if (result && result.success) {
        const newConf = { ...confTmp }
        if (result.data) {
          newConf.groups = result.data
        }
        setConf(newConf)
        setLoading({ ...setLoading, groups: false })

        toast.success(__('groups fetch successfully', 'bit-integrations'))
        return
      }
      setLoading({ ...setLoading, groups: false })
      toast.error(__(result?.data ? result.data : 'Something went wrong!', 'bit-integrations'))
    })
}

export const wpforoStaticFields = (selectedTask) => {
  if (selectedTask === TASK_LIST_VALUES.USER_REPUTATION || selectedTask === TASK_LIST_VALUES.ADD_TO_GROUP || selectedTask === TASK_LIST_VALUES.REMOVE_FROM_GROUP) {
    return { staticFields: [{ key: 'email', label: 'User Email', required: true }], fieldMap: [{ formField: '', wpforoField: 'email' }] }
  }

  return { staticFields: [], fieldMap: [] }
}

