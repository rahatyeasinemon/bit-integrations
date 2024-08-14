/* eslint-disable no-console */
/* eslint-disable no-else-return */
import toast from 'react-hot-toast'
import bitsFetch from '../../../Utils/bitsFetch'
import { __ } from '../../../Utils/i18nwrap'
import { create } from 'mutative'
import { TASK_LIST_VALUES } from './dokanConstants'

export const handleInput = (e, dokanConf, setDokanConf) => {
  const newConf = create(dokanConf, draftConf => {
    const { name } = e.target
    if (e.target.value !== '') {
      draftConf[name] = e.target.value
    } else {
      delete draftConf[name]
    }
  })

  setDokanConf(newConf)
}

export const checkMappedFields = (dokanConf) => {
  const mappedFields = dokanConf?.field_map ? dokanConf.field_map.filter(mappedField => (!mappedField.formField || !mappedField.dokanField || (!mappedField.formField === 'custom' && !mappedField.customValue))) : []
  if (mappedFields.length > 0) {
    return false
  }
  return true
}

export const dokanAuthentication = (confTmp, setError, setIsAuthorized, loading, setLoading) => {
  if (!confTmp.name) {
    setError({ name: !confTmp.name ? __('Name can\'t be empty', 'bit-integrations') : '' })
    return
  }

  setLoading({ ...loading, auth: true })
  bitsFetch({}, 'dokan_authentication')
    .then(result => {
      if (result.success) {
        setIsAuthorized(true)
        toast.success(__('Connected Successfully', 'bit-integrations'))
        setLoading({ ...loading, auth: false })
        return
      }
      setLoading({ ...loading, auth: false })
      toast.error(__('Connection failed: install and active Dokan plugin first!', 'bit-integrations'))
    })
}

export const getAllVendors = (confTmp, setConf, loading, setLoading) => {
  setLoading({ ...loading, vendors: true })

  bitsFetch({}, 'dokan_fetch_vendors')
    .then(result => {
      if (result.success && result.data) {
        const newConf = { ...confTmp }
        newConf.vendors = result.data
        setConf(newConf)
        setLoading({ ...loading, vendors: false })
        toast.success(__('Vendors fetched successfully', 'bit-integrations'))
        return
      }
      setLoading({ ...loading, vendors: false })
      toast.error(__(result?.data ? result.data : 'Something went wrong!', 'bit-integrations'))
    })
}

export const getDokanEUFields = (confTmp, setConf, loading, setLoading) => {
  toast.success('Fields fetched successfully.')
  setLoading({ ...loading, euFields: true })

  bitsFetch({}, 'dokan_fetch_eu_fields')
    .then(result => {
      if (result.success && result.data) {
        const newConf = { ...confTmp }

        if (newConf.staticFields) {
          const defaultFields = newConf.staticFields
          const mergedFields = defaultFields.concat(result.data)
          newConf.staticFields = mergedFields
        }

        if (confTmp.selectedTask === TASK_LIST_VALUES.UPDATE_VENDOR) {
          getAllVendors(newConf, setConf, loading, setLoading)
        }

        setConf(newConf)
        setLoading({ ...loading, euFields: false })
        toast.success(__('EU Compliance Fields fetched successfully', 'bit-integrations'))
        return
      }

      if (confTmp.selectedTask === TASK_LIST_VALUES.UPDATE_VENDOR) {
        getAllVendors(confTmp, setConf, loading, setLoading)
      }
      setLoading({ ...loading, euFields: false })
      toast.error(__(result?.data ? result.data : 'Something went wrong!', 'bit-integrations'))
    })
}

export const dokanStaticFields = (selectedTask) => {
  if (selectedTask === TASK_LIST_VALUES.CREATE_VENDOR || selectedTask === TASK_LIST_VALUES.UPDATE_VENDOR) {
    return {
      staticFields: [
        { key: 'email', label: 'Email', required: selectedTask === TASK_LIST_VALUES.CREATE_VENDOR ? true : false },
        { key: 'user_login', label: 'Username', required: selectedTask === TASK_LIST_VALUES.CREATE_VENDOR ? true : false },
        { key: 'store_name', label: 'Store Name', required: selectedTask === TASK_LIST_VALUES.CREATE_VENDOR ? true : false },
        { key: 'first_name', label: 'First Name', required: false },
        { key: 'last_name', label: 'Last Name', required: false },
        { key: 'phone', label: 'Phone', required: false },
        { key: 'payment_bank_ac_name', label: 'Account Name', required: false },
        { key: 'payment_bank_ac_type', label: 'Account Type', required: false },
        { key: 'payment_bank_ac_number', label: 'Account Number', required: false },
        { key: 'payment_bank_bank_name', label: 'Bank Name', required: false },
        { key: 'payment_bank_bank_addr', label: 'Bank Address', required: false },
        { key: 'payment_bank_routing_number', label: 'Routing Number', required: false },
        { key: 'payment_bank_iban', label: 'IBAN', required: false },
        { key: 'payment_bank_swift', label: 'Swift', required: false },
        { key: 'payment_paypal_email', label: 'PayPal Email', required: false },
        { key: 'street_1', label: 'Street 1', required: false },
        { key: 'street_2', label: 'Street 2', required: false },
        { key: 'city', label: 'City', required: false },
        { key: 'zip', label: 'Zip', required: false },
        { key: 'state', label: 'State', required: false },
        { key: 'country', label: 'Country', required: false },
      ],
      fieldMap: selectedTask === TASK_LIST_VALUES.CREATE_VENDOR ? [
        { formField: '', dokanField: 'email' },
        { formField: '', dokanField: 'user_login' },
        { formField: '', dokanField: 'store_name' },
      ] : [{ formField: '', dokanField: '' }]
    }
  } else if (selectedTask === TASK_LIST_VALUES.DELETE_VENDOR) {
    return {
      staticFields: [
        { key: 'vendor_email', label: 'Vendor Email', required: true },
        { key: 'reassign_email', label: 'Reassign (Email)', required: false },
      ],
      fieldMap: [{ formField: '', dokanField: 'vendor_email' }]
    }
  } else if (selectedTask === TASK_LIST_VALUES.WITHDRAW_REQUEST) {
    return {
      staticFields: [
        { key: 'amount', label: 'Amount', required: true },
        { key: 'note', label: 'Note', required: false },
      ],
      fieldMap: [{ formField: '', dokanField: 'amount' }]
    }
  }

  return { staticFields: [], fieldMap: [] }
}

