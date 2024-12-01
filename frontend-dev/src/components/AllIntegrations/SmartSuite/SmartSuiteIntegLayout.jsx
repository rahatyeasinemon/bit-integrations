/* eslint-disable no-unused-vars */
import MultiSelect from 'react-multiple-select-dropdown-lite'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import { __ } from '../../../Utils/i18nwrap'
import Loader from '../../Loaders/Loader'
import { getAllEvents, getAllSessions } from './SmartSuiteCommonFunc'
import SmartSuiteFieldMap from './SmartSuiteFieldMap'
import { addFieldMap } from './IntegrationHelpers'

export default function SmartSuiteIntegLayout({
  formFields,
  smartSuiteConf,
  setSmartSuiteConf,
  loading,
  setLoading,
  isLoading,
  setIsLoading,
  setSnackbar
}) {
  const setChanges = (val, name) => {
    if (name === 'selectedEvent' && val !== '') {
      getAllSessions(smartSuiteConf, setSmartSuiteConf, val, setLoading)
    }

    setSmartSuiteConf((prevConf) => {
      const newConf = { ...prevConf }
      newConf[name] = val

      if (name === 'selectedEvent') {
        delete newConf.selectedSession
        delete newConf.sessions
      }
      return newConf
    })
  }

  return (
    <>
      {(isLoading || loading.event || loading.session) && (
        <Loader
          style={{
            display: 'flex',
            justifyContent: 'center',
            alignItems: 'center',
            height: 100,
            transform: 'scale(0.7)'
          }}
        />
      )}

      {smartSuiteConf.actionName && !loading.event && (
        <>
          <br />
          <br />
          <div className="flx">
            <b className="wdt-200 d-in-b">{__('Select Event:', 'bit-integrations')}</b>
            <MultiSelect
              options={
                smartSuiteConf?.events &&
                smartSuiteConf.events.map((event) => ({ label: event.name, value: `${event.id}` }))
              }
              className="msl-wrp-options dropdown-custom-width"
              defaultValue={smartSuiteConf?.selectedEvent}
              onChange={(val) => setChanges(val, 'selectedEvent')}
              singleSelect
              closeOnSelect
            />
            <button
              onClick={() => getAllEvents(smartSuiteConf, setSmartSuiteConf, setLoading)}
              className="icn-btn sh-sm ml-2 mr-2 tooltip"
              style={{ '--tooltip-txt': `'${__('Refresh Events', 'bit-integrations')}'` }}
              type="button"
              disabled={loading.event}>
              &#x21BB;
            </button>
          </div>
        </>
      )}

      {smartSuiteConf.actionName && smartSuiteConf.selectedEvent && !loading.session && (
        <>
          <br />
          <br />
          <div className="flx">
            <b className="wdt-200 d-in-b">{__('Select Session:', 'bit-integrations')}</b>
            <MultiSelect
              options={
                smartSuiteConf?.sessions &&
                smartSuiteConf.sessions.map((session) => ({
                  label: session.datetime,
                  value: `${session.date_id}`
                }))
              }
              className="msl-wrp-options dropdown-custom-width"
              defaultValue={smartSuiteConf?.selectedSession}
              onChange={(val) => setChanges(val, 'selectedSession')}
              singleSelect
              closeOnSelect
            />
            <button
              onClick={() => getAllSessions(smartSuiteConf, setSmartSuiteConf, setLoading)}
              className="icn-btn sh-sm ml-2 mr-2 tooltip"
              style={{ '--tooltip-txt': `'${__('Refresh Sessions', 'bit-integrations')}'` }}
              type="button"
              disabled={loading.event}>
              &#x21BB;
            </button>
          </div>
        </>
      )}
      {smartSuiteConf.actionName && !isLoading && (
        <div>
          <br />
          <div className="mt-5">
            <b className="wdt-100">{__('Field Map', 'bit-integrations')}</b>
            <button
              className="icn-btn sh-sm ml-2 mr-2 tooltip"
              style={{ '--tooltip-txt': `'${__('Refresh fields', 'bit-integrations')}'` }}
              type="button">
              &#x21BB;
            </button>
          </div>

          <br />
          <div className="btcd-hr mt-1" />
          <div className="flx flx-around mt-2 mb-2 btcbi-field-map-label">
            <div className="txt-dp">
              <b>{__('Form Fields', 'bit-integrations')}</b>
            </div>
            <div className="txt-dp">
              <b>{__('SmartSuite Fields', 'bit-integrations')}</b>
            </div>
          </div>

          {smartSuiteConf?.field_map.map((itm, i) => (
            <SmartSuiteFieldMap
              key={`rp-m-${i + 9}`}
              i={i}
              field={itm}
              smartSuiteConf={smartSuiteConf}
              formFields={formFields}
              setSmartSuiteConf={setSmartSuiteConf}
              setSnackbar={setSnackbar}
            />
          ))}
          <div className="txt-center btcbi-field-map-button mt-2">
            <button
              onClick={() =>
                addFieldMap(smartSuiteConf.field_map.length, smartSuiteConf, setSmartSuiteConf, false)
              }
              className="icn-btn sh-sm"
              type="button">
              +
            </button>
          </div>
          <br />
          <br />
        </div>
      )}
    </>
  )
}
