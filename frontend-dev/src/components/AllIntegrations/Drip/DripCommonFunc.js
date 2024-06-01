// eslint-disable-next-line import/no-extraneous-dependencies
import { __ } from "@wordpress/i18n";
import bitsFetch from "../../../Utils/bitsFetch";
import toast from "react-hot-toast";

export const handleInput = (e, dripConf, setDripConf) => {
  const newConf = { ...dripConf };
  newConf.name = e.target.value;
  setDripConf({ ...newConf });
};

export const dripAuthentication = (dripConf, setDripConf, setError, setisAuthorized, loading, setLoading, type = 'authentication') => {
  const newConf = { ...dripConf }

  if (!newConf.name || !newConf.api_token) {
    setError({
      name: !newConf.name ? __('Integration name cann\'t be empty', 'bit-integrations') : '',
      api_token: !newConf.api_token ? __('Access Api Token Key cann\'t be empty', 'bit-integrations') : '',
    })
    return
  }

  let responseErrorMsg

  if (type === 'authentication') {
    setLoading({ ...loading, auth: true })
    responseErrorMsg = 'Authorization Failed'
  } else if (type === 'accounts') {
    setLoading({ ...loading, accounts: true })
    responseErrorMsg = 'Accounts fetching failed'
  }

  const data = {
    api_token: newConf.api_token,
  }

  bitsFetch(data, 'drip_authorize')
    .then(result => {
      if (result?.success) {
        newConf.accounts = result.data
        if (type === 'authentication') {
          setisAuthorized(true)
          toast.success('Authorized Successfully')
        } else if (type === 'accounts') {
          toast.success('Accounts fetched Successfully')
        }
      } else {
        toast.error(responseErrorMsg)
      }

      setDripConf({ ...newConf })
      setLoading({ ...loading, auth: false, accounts: false })
    })
}

export const checkMappedFields = (dripConf) => {
  const mappedFields = dripConf?.field_map
    ? dripConf.field_map.filter(
      (mappedField) =>
        !mappedField.formField ||
        !mappedField.dripField ||
        (!mappedField.formField === 'custom' && !mappedField.customValue)
    )
    : [];
  if (mappedFields.length > 0) {
    return false;
  }
  return true;
};

export const generateMappedField = (dripConf) => {
  const requiredFlds = dripConf?.dripFormFields.filter(fld => fld.required === true)
  return requiredFlds.length > 0 ? requiredFlds.map(field => ({ formField: '', dripField: field.key })) : [{ formField: '', dripField: '' }]
}

export const getCustomFields = (confTmp, setConf, setLoading) => {
  setLoading({ ...setLoading, customFields: true })

  const requestParams = { apiToken: confTmp.api_token, selectedAccountId: confTmp.selectedAccountId }

  bitsFetch(requestParams, 'drip_fetch_all_custom_fields')
    .then(result => {
      if (result && result.success) {
        const newConf = { ...confTmp }
        if (result.data) {
          newConf.dripFormFields = [...staticFields, ...result.data]
        }
        setConf(newConf)
        setLoading({ ...setLoading, customFields: false })
        toast.success(__('Custom fields fetch successfully', 'bit-integrations'))
        return
      }
      setLoading({ ...setLoading, customFields: false })
      toast.error(__('Custom fields fetch failed', 'bit-integrations'))
    })
}
export const getAllTags = (confTmp, setConf, setLoading) => {
  setLoading({ ...setLoading, tags: true })

  const requestParams = { apiToken: confTmp.api_token, selectedAccountId: confTmp.selectedAccountId }

  bitsFetch(requestParams, 'drip_fetch_all_tags')
    .then(result => {
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

export const staticFields = [
  { key: 'email', label: 'Email', required: true },
  { key: 'first_name', label: 'First Name', required: false },
  { key: 'last_name', label: 'Last Name', required: false },
  { key: 'address1', label: 'Address 1', required: false },
  { key: 'address2', label: 'Address 2', required: false },
  { key: 'city', label: 'City', required: false },
  { key: 'state', label: 'State', required: false },
  { key: 'zip', label: 'Zip', required: false },
  { key: 'country', label: 'Country', required: false },
  { key: 'phone', label: 'Phone', required: false },
  { key: 'time_zone', label: 'Time Zone', required: false },
  { key: 'ip_address', label: 'IP Address', required: false },
]
