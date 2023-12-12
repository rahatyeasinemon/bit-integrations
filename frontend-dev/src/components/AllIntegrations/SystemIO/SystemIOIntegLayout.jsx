// eslint-disable-next-line import/no-extraneous-dependencies
import { __ } from '@wordpress/i18n'
import { useEffect } from 'react'
import MultiSelect from 'react-multiple-select-dropdown-lite'
import Loader from '../../Loaders/Loader'
import { addFieldMap } from '../IntegrationHelpers/IntegrationHelpers'
import SystemIOActions from './SystemIOActions'
import { refreshSystemIOHeader, refreshSystemIOForm, refreshSystemIOTags } from './SystemIOCommonFunc'
import SystemIOFieldMap from './SystemIOFieldMap'

export default function SystemIOIntegLayout({ formID, formFields, systemIOConf, setSystemIOConf, isLoading, setIsLoading, setSnackbar }) {

  const setTags = (val) => {
    const newConf = { ...systemIOConf }
    if (val) {
      newConf.tagIds = val ? val.split(',') : []
    } else {
      delete newConf.tagIds
    }
    setSystemIOConf({ ...newConf })
  }

  const handleInput = (e) => {
    const formid = e.target.value
    const newConf = { ...systemIOConf }
    if (formid) {
      newConf.formId = formid
    } else {
      delete newConf.formId
    }
    refreshSystemIOHeader(newConf, setSystemIOConf, setIsLoading, setSnackbar)
  }

  const systemIOForms = systemIOConf?.default?.systemIOForms

  useEffect(() => {
    systemIOForms && refreshSystemIOTags(systemIOConf, setSystemIOConf, setIsLoading, setSnackbar)
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [systemIOForms])

  return (
    <>
      <br />
      <b className="wdt-200 d-in-b">{__('Form:', 'bit-integrations')}</b>
      <select value={systemIOConf?.formId} name="formId" id="" className="btcd-paper-inp w-5" onChange={handleInput}>
        <option value="">{__('Select Form', 'bit-integrations')}</option>
        {
          systemIOConf?.default?.systemIOForms && Object.keys(systemIOConf.default.systemIOForms).map(formname => (
            <option key={`${formname + 1}`} value={systemIOConf.default.systemIOForms[formname].formId}>
              {systemIOConf.default.systemIOForms[formname].formName}
            </option>
          ))
        }
      </select>
      <button onClick={() => refreshSystemIOForm(systemIOConf, setSystemIOConf, setIsLoading, setSnackbar)} className="icn-btn sh-sm ml-2 mr-2 tooltip" style={{ '--tooltip-txt': '"Refresh SystemIO form"' }} type="button" disabled={isLoading}>&#x21BB;</button>
      <br />
      <br />
      <div className="d-flx">
        <b style={{ marginTop: '15px' }} className="wdt-200 d-in-b">{__('Tags: ', 'bit-integrations')}</b>
        <MultiSelect
          defaultValue={systemIOConf?.tagIds?.toString()}
          className="btcd-paper-drpdwn w-5"
          options={systemIOConf?.default?.systemIOTags && Object.keys(systemIOConf.default.systemIOTags).map(tag => ({ label: systemIOConf.default.systemIOTags[tag].tagName, value: systemIOConf.default.systemIOTags[tag].tagId.toString() }))}
          onChange={val => setTags(val)}
        />
        <button onClick={() => refreshSystemIOTags(systemIOConf, setSystemIOConf, setIsLoading, setSnackbar)} className="icn-btn sh-sm ml-2 mr-2 tooltip" style={{ '--tooltip-txt': `'${__('Refresh SystemIO Tags', 'bit-integrations')}'` }} type="button" disabled={isLoading}>&#x21BB;</button>
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
        <button onClick={() => refreshSystemIOHeader(systemIOConf, setSystemIOConf, setIsLoading, setSnackbar)} className="icn-btn sh-sm ml-2 mr-2 tooltip" style={{ '--tooltip-txt': `'${__('Refresh SystemIO Field', 'bit-integrations')}'` }} type="button" disabled={isLoading}>&#x21BB;</button>
      </div>
      {

        (systemIOConf?.formId || systemIOConf?.default?.fields) && (
          <>
            <div className="btcd-hr mt-1" />
            <div className="flx flx-around mt-2 mb-2 btcbi-field-map-label">
              <div className="txt-dp"><b>{__('Form Fields', 'bit-integrations')}</b></div>
              <div className="txt-dp"><b>{__('SystemIO Fields', 'bit-integrations')}</b></div>
            </div>

            {systemIOConf.field_map.map((itm, i) => (
              <SystemIOFieldMap
                key={`SystemIO-m-${i + 9}`}
                i={i}
                field={itm}
                systemIOConf={systemIOConf}
                formFields={formFields}
                setSystemIOConf={setSystemIOConf}
              />
            ))}
            <div className="txt-center btcbi-field-map-button mt-2"><button onClick={() => addFieldMap(systemIOConf.field_map.length, systemIOConf, setSystemIOConf)} className="icn-btn sh-sm" type="button">+</button></div>
            <br />
            <br />
            <div className="mt-4"><b className="wdt-100">{__('Actions', 'bit-integrations')}</b></div>
            <div className="btcd-hr mt-1" />
            <SystemIOActions
              systemIOConf={systemIOConf}
              setSystemIOConf={setSystemIOConf}
            />
          </>
        )
      }
    </>
  )
}
