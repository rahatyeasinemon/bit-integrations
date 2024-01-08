// eslint-disable-next-line import/no-extraneous-dependencies
import { __ } from '@wordpress/i18n'
import bitsFetch from '../../../Utils/bitsFetch'

export const handleInput = (e, zagoMailConf, setZagoMailConf) => {
  const newConf = { ...zagoMailConf }
  newConf.name = e.target.value
  setZagoMailConf({ ...newConf })
}

export const refreshZagoMailForm = (
  zagoMailConf,
  setZagoMailConf,
  setIsLoading,
  setSnackbar,
) => {
  const refreshFormsRequestParams = {
    api_secret: zagoMailConf.api_secret,
  }
  bitsFetch(refreshFormsRequestParams, 'zagoMail_forms')
    .then((result) => {
      if (result && result.success) {
        const newConf = { ...zagoMailConf }
        if (result.data.zagoMailForms) {
          if (!newConf.default) {
            newConf.default = {}
          }
          newConf.default.zagoMailForms = result.data.zagoMailForms
          setSnackbar({
            show: true,
            msg: __('Convert Kit forms refreshed', 'bit-integrations'),
          })
        } else {
          setSnackbar({
            show: true,
            msg: __(
              'No Convert Kit forms found. Try changing the header row number or try again',
              'bit-integrations',
            ),
          })
        }

        setZagoMailConf({ ...newConf })
      } else {
        setSnackbar({
          show: true,
          msg: __(
            'Convert Kit forms refresh failed. please try again',
            'bit-integrations',
          ),
        })
      }
      setIsLoading(false)
    })
    .catch(() => setIsLoading(false))
}
// refreshZagoMailTags
export const refreshZagoMailTags = (
  zagoMailConf,
  setZagoMailConf,
  setIsLoading,
  setSnackbar,
) => {
  const refreshFormsRequestParams = {
    api_secret: zagoMailConf.api_secret,
  }
  bitsFetch(refreshFormsRequestParams, 'zagoMail_tags')
    .then((result) => {
      if (result && result.success) {
        const newConf = { ...zagoMailConf }
        if (result.data.zagoMailTags) {
          if (!newConf.default) {
            newConf.default = {}
          }
          newConf.default.zagoMailTags = result.data.zagoMailTags
          setSnackbar({
            show: true,
            msg: __('Convert Kit tags refreshed', 'bit-integrations'),
          })
        } else {
          setSnackbar({
            show: true,
            msg: __(
              'No Convert Kit tags found. Try changing the header row number or try again',
              'bit-integrations',
            ),
          })
        }

        setZagoMailConf({ ...newConf })
      } else {
        setSnackbar({
          show: true,
          msg: __(
            'Convert Kit tags refresh failed. please try again',
            'bit-integrations',
          ),
        })
      }
      setIsLoading(false)
    })
    .catch(() => setIsLoading(false))
}
// refreshMappedFields
export const refreshZagoMailHeader = (
  zagoMailConf,
  setZagoMailConf,
  setIsLoading,
  setSnackbar,
) => {
  const refreshFormsRequestParams = {
    api_secret: zagoMailConf.api_secret,
  }
  bitsFetch(refreshFormsRequestParams, 'zagoMail_headers')
    .then((result) => {
      if (result && result.success) {
        const newConf = { ...zagoMailConf }
        if (result.data.zagoMailField) {
          if (!newConf.default) {
            newConf.default = {}
          }
          newConf.default.fields = result.data.zagoMailField
          const { fields } = newConf.default
          newConf.field_map = Object.values(fields)
            .filter((f) => f.required)
            .map((f) => ({
              formField: '',
              zagoMailField: f.fieldId,
              required: true,
            }))
          setSnackbar({
            show: true,
            msg: __('Convert Kit fields refreshed', 'bit-integrations'),
          })
        } else {
          setSnackbar({
            show: true,
            msg: __(
              'No Convert Kit fields found. Try changing the header row number or try again',
              'bit-integrations',
            ),
          })
        }

        setZagoMailConf({ ...newConf })
      } else {
        setSnackbar({
          show: true,
          msg: __(
            'Convert Kit fields refresh failed. please try again',
            'bit-integrations',
          ),
        })
      }
      setIsLoading(false)
    })
    .catch(() => setIsLoading(false))
}

export const checkMappedFields = (zagoMailConf) => {
  const mappedFields = zagoMailConf?.field_map
    ? zagoMailConf.field_map.filter(
      (mappedField) => !mappedField.formField
          && mappedField.zagoMailField
          && mappedField.required,
    )
    : []
  if (mappedFields.length > 0) {
    return false
  }
  return true
}
