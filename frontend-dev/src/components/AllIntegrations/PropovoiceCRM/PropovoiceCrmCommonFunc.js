/* eslint-disable no-else-return */
import toast from 'react-hot-toast'
import { __ } from '../../../Utils/i18nwrap'
import bitsFetch from '../../../Utils/bitsFetch'

export const handleInput = (e, slackConf, setSlackConf) => {
  const newConf = { ...slackConf }
  const { name } = e.target
  if (e.target.value !== '') {
    newConf[name] = e.target.value
  } else {
    delete newConf[name]
  }
  setSlackConf({ ...newConf })
}

export const checkMappedFields = fieldsMapped => {
  const checkedField = fieldsMapped
    ? fieldsMapped?.filter(item => (!item.formField || !item.propovoiceCrmFormField))
    : []
  if (checkedField.length > 0) return false
  return true
}

export const generateMappedField = (propovoiceCrmConf) => {
  const requiredFlds = propovoiceCrmConf?.leadFields.filter(fld => fld.required === true)
  return requiredFlds.length > 0 ? requiredFlds.map(field => ({ formField: '', propovoiceCrmFormField: field.key })) : [{ formField: '', propovoiceCrmFormField: '' }]
}

export const getALLPropovoiceFields = (propovoiceCrmConf, setPropovoiceCrmConf, setIsLoading, setSnackbar) => {
  setIsLoading(true)
  bitsFetch(null, 'propovoice_crm_fetch_all_fields')
    .then((result) => {
      if (result && result.success) {
        const newConf = { ...propovoiceCrmConf }
        if (!newConf.default) {
          newConf.default = {}
        }
        if (result.data) {
          newConf.default.allFields = result.data
        }
        setPropovoiceCrmConf({ ...newConf })
        setIsLoading(false)
        toast.success(__('All fields fetched successfully', 'bit-integrations'))
        return
      }
      setIsLoading(false)
      toast.error(__('Propovoice Crm fields fetch failed. please try again', 'bit-integrations'))
    })
    .catch(() => setIsLoading(false))
}

export const getAllLeadTags = (propovoiceCrmConf, setPropovoiceCrmConf, isLoading, setIsLoading) => {
  setIsLoading(true)
  bitsFetch(null, 'propovoice_crm_lead_tags')
    .then((result) => {
      if (result && result.success) {
        setPropovoiceCrmConf((prevState) => {
          const newConf = { ...prevState }
          if (!newConf.default) {
            newConf.default = {}
          }
          if (result.data) {
            newConf.default.allTags = result.data
          }
          return newConf
        })
        setIsLoading(false)
        toast.success(__('All tags fetched successfully', 'bit-integrations'))
        return
      }
      setIsLoading(false)
      toast.error(__('Propovoice Crm tags fetch failed. please try again', 'bit-integrations'))
    })
    .catch(() => setIsLoading(false))
}

export const getAllLeadLabel = (propovoiceCrmConf, setPropovoiceCrmConf, isLoading, setIsLoading) => {
  setIsLoading(true)
  bitsFetch(null, 'propovoice_crm_lead_label')
    .then((result) => {
      if (result && result.success) {
        setPropovoiceCrmConf((prev) => {
          const newConf = { ...prev }
          if (!newConf.default) {
            newConf.default = {}
          }
          if (result.data) {
            newConf.default.allLabels = result.data
          }
          return newConf
        })
        setIsLoading(false)
        toast.success(__('All label fetched successfully', 'bit-integrations'))
        return
      }
      setIsLoading(false)
      toast.error(__('Propovoice Crm label fetch failed. please try again', 'bit-integrations'))
    })
    .catch(() => setIsLoading(false))
}





// export const getAllLeadLabel = (propovoiceCrmConf, setPropovoiceCrmConf, isLoading, setIsLoading) => {
//   setIsLoading(true)
//   bitsFetch(null, 'propovoice_crm_lead_label')
//     .then((result) => {
//       if (result && result.success) {
//         const newConf = { ...propovoiceCrmConf }
//         if (!newConf.default) {
//           newConf.default = {}
//         }
//         if (result.data) {
//           newConf.default.allLabels = result.data
//         }
//         setPropovoiceCrmConf({ ...newConf })
//         setIsLoading(false)
//         toast.success(__('All label fetched successfully', 'bit-integrations'))
//         return
//       }
//       setIsLoading(false)
//       toast.error(__('Propovoice Crm label fetch failed. please try again', 'bit-integrations'))
//     })
//     .catch(() => setIsLoading(false))
// }
