import { __, sprintf } from '../../../Utils/i18nwrap'
import bitsFetch from '../../../Utils/bitsFetch'
import { deepCopy } from '../../../Utils/Helpers'

export const handleInput = (e, keapConf, setKeapConf, formID, setIsLoading, setSnackbar, isNew, error, setError) => {
  let newConf = { ...keapConf }
  if (isNew) {
    const rmError = { ...error }
    rmError[e.target.name] = ''
    setError({ ...rmError })
  }
  newConf[e.target.name] = e.target.value
  switch (e.target.name) {
    case 'spreadsheetId':
      newConf = spreadSheetChange(newConf, formID, setKeapConf, setIsLoading, setSnackbar)
      break
    case 'worksheetName':
      newConf = worksheetChange(newConf, formID, setKeapConf, setIsLoading, setSnackbar)
      break
    default:
      break
  }
  setKeapConf({ ...newConf })
}

export const spreadSheetChange = (keapConf, formID, setKeapConf, setIsLoading, setSnackbar) => {
  const newConf = deepCopy(keapConf)
  newConf.worksheetName = ''
  newConf.headerRow = 'A1'
  newConf.field_map = [{ formField: '', googleSheetField: '' }]

  if (!newConf?.default?.worksheets?.[keapConf.spreadsheetId]) {
    refreshWorksheets(formID, newConf, setKeapConf, setIsLoading, setSnackbar)
  } else if (Object.keys(newConf?.default?.worksheets?.[keapConf.spreadsheetId]).length === 1) {
    newConf.worksheetName = newConf?.default?.worksheets?.[keapConf.spreadsheetId][0].properties.title

    if (!newConf?.default?.worksheets?.headers?.[newConf.worksheetName]) {
      refreshWorksheetHeaders(formID, newConf, setKeapConf, setIsLoading, setSnackbar)
    }
  }

  return newConf
}

export const worksheetChange = (keapConf, formID, setKeapConf, setIsLoading, setSnackbar) => {
  const newConf = { ...keapConf }
  newConf.headerRow = 'A1'
  newConf.field_map = [{ formField: '', googleSheetField: '' }]

  if (!newConf?.default?.worksheets?.headers?.[keapConf.worksheetName]) {
    refreshWorksheetHeaders(formID, newConf, setKeapConf, setIsLoading, setSnackbar)
  }

  return newConf
}

export const refreshSpreadsheets = (formID, keapConf, setKeapConf, setIsLoading, setSnackbar) => {
  setIsLoading(true)
  const refreshModulesRequestParams = {
    formID,
    id: keapConf.id,
    clientId: keapConf.clientId,
    clientSecret: keapConf.clientSecret,
    tokenDetails: keapConf.tokenDetails,
    ownerEmail: keapConf.ownerEmail,
  }
  bitsFetch(refreshModulesRequestParams, 'gsheet_refresh_spreadsheets')
    .then(result => {
      if (result && result.success) {
        const newConf = { ...keapConf }
        if (!newConf.default) {
          newConf.default = {}
        }
        if (result.data.spreadsheets) {
          newConf.default.spreadsheets = result.data.spreadsheets
        }
        if (result.data.tokenDetails) {
          newConf.tokenDetails = result.data.tokenDetails
        }
        setSnackbar({ show: true, msg: __('Keap refreshed', 'bit-integrations') })
        setKeapConf({ ...newConf })
      } else if ((result && result.data && result.data.data) || (!result.success && typeof result.data === 'string')) {
        setSnackbar({ show: true, msg: sprintf(__('Keap refresh failed Cause: %s. please try again', 'bit-integrations'), result.data.data || result.data) })
      } else {
        setSnackbar({ show: true, msg: __('Keap refresh failed. please try again', 'bit-integrations') })
      }
      setIsLoading(false)
    })
    .catch(() => setIsLoading(false))
}

export const refreshWorksheets = (formID, keapConf, setKeapConf, setIsLoading, setSnackbar) => {
  const { spreadsheetId } = keapConf
  if (!spreadsheetId) {
    return
  }
  setIsLoading(true)
  const refreshSpreadsheetsRequestParams = {
    formID,
    spreadsheetId,
    clientId: keapConf.clientId,
    clientSecret: keapConf.clientSecret,
    tokenDetails: keapConf.tokenDetails,
  }
  bitsFetch(refreshSpreadsheetsRequestParams, 'gsheet_refresh_worksheets')
    .then(result => {
      if (result && result.success) {
        const newConf = { ...keapConf }
        if (result.data.worksheets) {
          if (!newConf.default.worksheets) {
            newConf.default.worksheets = {}
          }
          newConf.default.worksheets[spreadsheetId] = result.data.worksheets
        }

        if (result.data.tokenDetails) {
          newConf.tokenDetails = result.data.tokenDetails
        }
        setSnackbar({ show: true, msg: __('Worksheets refreshed', 'bit-integrations') })
        setKeapConf({ ...newConf })
      } else {
        setSnackbar({ show: true, msg: __('Worksheets refresh failed. please try again', 'bit-integrations') })
      }
      setIsLoading(false)
    })
    .catch(() => setIsLoading(false))
}

export const refreshWorksheetHeaders = (formID, keapConf, setKeapConf, setIsLoading, setSnackbar) => {
  const { spreadsheetId, worksheetName, header, headerRow } = keapConf
  if (!spreadsheetId && !worksheetName && !header && !headerRow) {
    return
  }

  setIsLoading(true)
  const refreshWorksheetHeadersRequestParams = {
    formID,
    spreadsheetId,
    worksheetName,
    header,
    headerRow,
    clientId: keapConf.clientId,
    clientSecret: keapConf.clientSecret,
    tokenDetails: keapConf.tokenDetails,
  }
  bitsFetch(refreshWorksheetHeadersRequestParams, 'gsheet_refresh_worksheet_headers')
    .then(result => {
      if (result && result.success) {
        const newConf = { ...keapConf }
        if (result.data.worksheet_headers?.length > 0) {
          if (!newConf.default.headers) {
            newConf.default.headers = {}
          }
          if (!newConf.default.headers[spreadsheetId]) {
            newConf.default.headers[spreadsheetId] = {}
          }
          if (!newConf.default.headers[spreadsheetId][worksheetName]) {
            newConf.default.headers[spreadsheetId][worksheetName] = {}
          }
          newConf.default.headers[spreadsheetId][worksheetName][headerRow] = result.data.worksheet_headers
          if (result.data.tokenDetails) {
            newConf.tokenDetails = result.data.tokenDetails
          }
          setSnackbar({ show: true, msg: __('Worksheet Headers refreshed', 'bit-integrations') })
        } else {
          setSnackbar({ show: true, msg: __('No Worksheet headers found. Try changing the header row number or try again', 'bit-integrations') })
        }

        if (result.data.tokenDetails) {
          newConf.tokenDetails = result.data.tokenDetails
        }
        setKeapConf({ ...newConf })
      } else {
        setSnackbar({ show: true, msg: __('Worksheet Headers refresh failed. please try again', 'bit-integrations') })
      }
      setIsLoading(false)
    })
    .catch(() => setIsLoading(false))
}

export const handleAuthorize = (confTmp, setConf, setError, setisAuthorized, setIsLoading, setSnackbar) => {
  if (!confTmp.clientId || !confTmp.clientSecret) {
    setError({
      clientId: !confTmp.clientId ? __('Client ID cann\'t be empty', 'bit-integrations') : '',
      clientSecret: !confTmp.clientSecret ? __('Secret key cann\'t be empty', 'bit-integrations') : '',
    })
    return
  }
  setIsLoading(true)
  const apiEndpoint = `https://accounts.infusionsoft.com/app/oauth/authorize?scope=full&access_type=offline&prompt=consent&response_type=code&state=${encodeURIComponent(window.location.href)}/redirect&redirect_uri=${encodeURIComponent(`${btcbi.api.base}/redirect`)}&client_id=${confTmp.clientId}`
  const authWindow = window.open(apiEndpoint, 'keap', 'width=400,height=609,toolbar=off')
  const popupURLCheckTimer = setInterval(() => {
    if (authWindow.closed) {
      clearInterval(popupURLCheckTimer)
      let grantTokenResponse = {}
      let isauthRedirectLocation = false
      const bitsGoogleSheet = localStorage.getItem('__keap')
      if (bitsGoogleSheet) {
        isauthRedirectLocation = true
        grantTokenResponse = JSON.parse(bitsGoogleSheet)
        localStorage.removeItem('__keap')
      }
      if (!grantTokenResponse.code || grantTokenResponse.error || !grantTokenResponse || !isauthRedirectLocation) {
        const errorCause = grantTokenResponse.error ? `Cause: ${grantTokenResponse.error}` : ''
        setSnackbar({ show: true, msg: `${__('Authorization failed', 'bit-integrations')} ${errorCause}. ${__('please try again', 'bit-integrations')}` })
        setIsLoading(false)
      } else {
        const newConf = { ...confTmp }
        newConf.accountServer = grantTokenResponse['accounts-server']
        tokenHelper(grantTokenResponse, newConf, setConf, setisAuthorized, setIsLoading, setSnackbar)
      }
    }
  }, 500)
}

const tokenHelper = (grantToken, confTmp, setConf, setisAuthorized, setIsLoading, setSnackbar) => {
  const tokenRequestParams = { ...grantToken }
  tokenRequestParams.clientId = confTmp.clientId
  tokenRequestParams.clientSecret = confTmp.clientSecret
  // eslint-disable-next-line no-undef
  tokenRequestParams.redirectURI = `${btcbi.api.base}/redirect`
  bitsFetch(tokenRequestParams, 'keap_generate_token')
    .then(result => result)
    .then(result => {
      if (result && result.success) {
        const newConf = { ...confTmp }
        newConf.tokenDetails = result.data
        setConf(newConf)
        setisAuthorized(true)
        setSnackbar({ show: true, msg: __('Authorized Successfully', 'bit-integrations') })
      } else if ((result && result.data && result.data.data) || (!result.success && typeof result.data === 'string')) {
        setSnackbar({ show: true, msg: `${__('Authorization failed Cause:', 'bit-integrations')}${result.data.data || result.data}. ${__('please try again', 'bit-integrations')}` })
      } else {
        setSnackbar({ show: true, msg: __('Authorization failed. please try again', 'bit-integrations') })
      }
      setIsLoading(false)
    })
}

export const checkMappedFields = (keapConf) => {
  const mappedFleld = keapConf.field_map ? keapConf.field_map.filter(mapped => (!mapped.formField && !mapped.keapField)) : []
  if (mappedFleld.length > 0) {
    return false
  }
  return true
}

export const generateMappedField = (keapConf) => {
  const requiredFlds = keapConf?.contactFields.filter(fld => fld.required === true)
  return requiredFlds.length > 0 ? requiredFlds.map(field => ({ formField: '', keapField: field.key })) : [{ formField: '', keapField: '' }]
}
