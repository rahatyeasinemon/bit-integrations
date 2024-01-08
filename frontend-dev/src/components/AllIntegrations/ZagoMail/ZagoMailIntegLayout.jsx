// eslint-disable-next-line import/no-extraneous-dependencies
import { __ } from '@wordpress/i18n'
import { useEffect } from 'react'
import MultiSelect from 'react-multiple-select-dropdown-lite'
import Loader from '../../Loaders/Loader'
import { addFieldMap } from '../IntegrationHelpers/IntegrationHelpers'
import ZagoMailActions from './ZagoMailActions'
import { refreshZagoMailHeader, refreshZagoMailForm, refreshZagoMailTags } from './ZagoMailCommonFunc'
import ZagoMailFieldMap from './ZagoMailFieldMap'

export default function ZagoMailIntegLayout({ formID, formFields, zagoMailConf, setZagoMailConf, isLoading, setIsLoading, setSnackbar }) {

  const setTags = (val) => {
    const newConf = { ...zagoMailConf }
    if (val) {
      newConf.tagIds = val ? val.split(',') : []
    } else {
      delete newConf.tagIds
    }
    setZagoMailConf({ ...newConf })
  }

  const handleInput = (e) => {
    const formid = e.target.value
    const newConf = { ...zagoMailConf }
    if (formid) {
      newConf.formId = formid
    } else {
      delete newConf.formId
    }
    refreshZagoMailHeader(newConf, setZagoMailConf, setIsLoading, setSnackbar)
  }

  const zagoMailForms = zagoMailConf?.default?.zagoMailForms

  useEffect(() => {
    zagoMailForms && refreshZagoMailTags(zagoMailConf, setZagoMailConf, setIsLoading, setSnackbar)
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [zagoMailForms])

  return (
    <>
      <br />
      <b className="wdt-200 d-in-b">{__('Form:', 'bit-integrations')}</b>
      <select value={zagoMailConf?.formId} name="formId" id="" className="btcd-paper-inp w-5" onChange={handleInput}>
        <option value="">{__('Select Form', 'bit-integrations')}</option>
        {
          zagoMailConf?.default?.zagoMailForms && Object.keys(zagoMailConf.default.zagoMailForms).map(formname => (
            <option key={`${formname + 1}`} value={zagoMailConf.default.zagoMailForms[formname].formId}>
              {zagoMailConf.default.zagoMailForms[formname].formName}
            </option>
          ))
        }
      </select>
      <button onClick={() => refreshZagoMailForm(zagoMailConf, setZagoMailConf, setIsLoading, setSnackbar)} className="icn-btn sh-sm ml-2 mr-2 tooltip" style={{ '--tooltip-txt': '"Refresh ZagoMail form"' }} type="button" disabled={isLoading}>&#x21BB;</button>
      <br />
      <br />
      <div className="d-flx">
        <b style={{ marginTop: '15px' }} className="wdt-200 d-in-b">{__('Tags: ', 'bit-integrations')}</b>
        <MultiSelect
          defaultValue={zagoMailConf?.tagIds?.toString()}
          className="btcd-paper-drpdwn w-5"
          options={zagoMailConf?.default?.zagoMailTags && Object.keys(zagoMailConf.default.zagoMailTags).map(tag => ({ label: zagoMailConf.default.zagoMailTags[tag].tagName, value: zagoMailConf.default.zagoMailTags[tag].tagId.toString() }))}
          onChange={val => setTags(val)}
        />
        <button onClick={() => refreshZagoMailTags(zagoMailConf, setZagoMailConf, setIsLoading, setSnackbar)} className="icn-btn sh-sm ml-2 mr-2 tooltip" style={{ '--tooltip-txt': `'${__('Refresh ZagoMail Tags', 'bit-integrations')}'` }} type="button" disabled={isLoading}>&#x21BB;</button>
      </div>
      <br />
      {isLoading && (
        <Loader style={{
          display: 'flex',
          justifyContent: 'center',
          alignItems: 'center',
          height: 100,
          transform: 'scale(0.7)',
        }}
        />
      )}

      <div className="mt-4">
        <b className="wdt-100">{__('Map Fields', 'bit-integrations')}</b>
        <button onClick={() => refreshZagoMailHeader(zagoMailConf, setZagoMailConf, setIsLoading, setSnackbar)} className="icn-btn sh-sm ml-2 mr-2 tooltip" style={{ '--tooltip-txt': `'${__('Refresh ZagoMail Field', 'bit-integrations')}'` }} type="button" disabled={isLoading}>&#x21BB;</button>
      </div>
      {

        (zagoMailConf?.formId || zagoMailConf?.default?.fields) && (
          <>
            <div className="btcd-hr mt-1" />
            <div className="flx flx-around mt-2 mb-2 btcbi-field-map-label">
              <div className="txt-dp"><b>{__('Form Fields', 'bit-integrations')}</b></div>
              <div className="txt-dp"><b>{__('ZagoMail Fields', 'bit-integrations')}</b></div>
            </div>

            {zagoMailConf.field_map.map((itm, i) => (
              <ZagoMailFieldMap
                key={`ZagoMail-m-${i + 9}`}
                i={i}
                field={itm}
                zagoMailConf={zagoMailConf}
                formFields={formFields}
                setZagoMailConf={setZagoMailConf}
              />
            ))}
            <div className="txt-center btcbi-field-map-button mt-2"><button onClick={() => addFieldMap(zagoMailConf.field_map.length, zagoMailConf, setZagoMailConf)} className="icn-btn sh-sm" type="button">+</button></div>
            <br />
            <br />
            <div className="mt-4"><b className="wdt-100">{__('Actions', 'bit-integrations')}</b></div>
            <div className="btcd-hr mt-1" />
            <ZagoMailActions
              zagoMailConf={zagoMailConf}
              setZagoMailConf={setZagoMailConf}
            />
          </>
        )
      }
    </>
  )
}
