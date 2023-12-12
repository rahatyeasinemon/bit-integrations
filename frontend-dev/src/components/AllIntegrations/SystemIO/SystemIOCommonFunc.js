// eslint-disable-next-line import/no-extraneous-dependencies
import { __ } from '@wordpress/i18n'
import bitsFetch from '../../../Utils/bitsFetch'

export const handleInput = (e, systemIOConf, setSystemIOConf) => {
  const newConf = { ...systemIOConf }
  newConf.name = e.target.value
  setSystemIOConf({ ...newConf })
}

export const refreshSystemIOForm = (
  systemIOConf,
  setSystemIOConf,
  setIsLoading,
  setSnackbar,
) => {
  const refreshFormsRequestParams = {
    api_secret: systemIOConf.api_secret,
  }
  bitsFetch(refreshFormsRequestParams, 'systemIO_forms')
    .then((result) => {
      if (result && result.success) {
        const newConf = { ...systemIOConf }
        if (result.data.systemIOForms) {
          if (!newConf.default) {
            newConf.default = {}
          }
          newConf.default.systemIOForms = result.data.systemIOForms
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

        setSystemIOConf({ ...newConf })
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
// refreshSystemIOTags
export const refreshSystemIOTags = (
  systemIOConf,
  setSystemIOConf,
  setIsLoading,
  setSnackbar,
) => {
  const refreshFormsRequestParams = {
    api_secret: systemIOConf.api_secret,
  }
  bitsFetch(refreshFormsRequestParams, 'systemIO_tags')
    .then((result) => {
      if (result && result.success) {
        const newConf = { ...systemIOConf }
        if (result.data.systemIOTags) {
          if (!newConf.default) {
            newConf.default = {}
          }
          newConf.default.systemIOTags = result.data.systemIOTags
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

        setSystemIOConf({ ...newConf })
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
export const refreshSystemIOHeader = (
  systemIOConf,
  setSystemIOConf,
  setIsLoading,
  setSnackbar,
) => {
  const refreshFormsRequestParams = {
    api_secret: systemIOConf.api_secret,
  }
  bitsFetch(refreshFormsRequestParams, 'systemIO_headers')
    .then((result) => {
      if (result && result.success) {
        const newConf = { ...systemIOConf }
        if (result.data.systemIOField) {
          if (!newConf.default) {
            newConf.default = {}
          }
          newConf.default.fields = result.data.systemIOField
          const { fields } = newConf.default
          newConf.field_map = Object.values(fields)
            .filter((f) => f.required)
            .map((f) => ({
              formField: '',
              systemIOField: f.fieldId,
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

        setSystemIOConf({ ...newConf })
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

export const checkMappedFields = (systemIOConf) => {
  const mappedFields = systemIOConf?.field_map
    ? systemIOConf.field_map.filter(
      (mappedField) => !mappedField.formField
          && mappedField.systemIOField
          && mappedField.required,
    )
    : []
  if (mappedFields.length > 0) {
    return false
  }
  return true
}
