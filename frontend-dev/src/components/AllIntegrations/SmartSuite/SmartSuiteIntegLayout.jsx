/* eslint-disable no-unused-vars */
import 'react-multiple-select-dropdown-lite/dist/index.css'
import { useRecoilValue } from 'recoil'
import { $btcbi } from '../../../GlobalStates'
import { __ } from '../../../Utils/i18nwrap'
import Loader from '../../Loaders/Loader'
import { addFieldMap } from './IntegrationHelpers'
import SmartSuiteActions from './SmartSuiteActions'
//import { getCustomFields } from './SmartSuiteCommonFunc'
import SmartSuiteFieldMap from './SmartSuiteFieldMap'

export default function SmartSuiteIntegLayout({
  formFields,
  handleInput,
  smartSuiteConf,
  setSmartSuiteConf,
  loading,
  setLoading,
  isLoading,
  setIsLoading,
  setSnackbar
}) {
  const btcbi = useRecoilValue($btcbi)
  const handleActionInput = (e) => {
    const newConf = { ...smartSuiteConf }
    const { name } = e.target
    newConf.field_map = [{ formField: '', smartSuiteFormField: '' }]

    if (e.target.value != '') {
      newConf[name] = e.target.value
      if (e.target.value === 'contact' || e.target.value === 'campaign') {
        //   getCustomFields(newConf, setSmartSuiteConf, setIsLoading, btcbi)
      }
    } else {
      delete newConf[name]
    }
    console.error('newconf')
    console.error(newConf.field_map)
    setSmartSuiteConf({ ...newConf })
  }

  return (
    <>
      <br />

      <b className="wdt-200 d-in-b">{__('Select Action:', 'bit-integrations')}</b>
      <select
        onChange={handleActionInput}
        name="actionName"
        value={smartSuiteConf.actionName}
        className="btcd-paper-inp w-5">
        <option value="">{__('Select an action', 'bit-integrations')}</option>
        <option value="contact" data-action_name="contact">
          {__('Create Solution', 'bit-integrations')}
        </option>
        <option value="campaign" data-action_name="campaign">
          {__('Create Table', 'bit-integrations')}
        </option>
        <option value="campaign" data-action_name="campaign">
          {__('Create Record', 'bit-integrations')}
        </option>
      </select>
      {(loading.CRMPipelines || loading.CRMOwners || loading.CRMContacts) && (
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

      {isLoading && (
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
      {smartSuiteConf.actionName && !isLoading && (
        <div>
          <br />
          <div className="mt-5">
            <b className="wdt-100">{__('Field Map', 'bit-integrations')}</b>
            {smartSuiteConf.actionName === 'contact' && (
              <button
                //   onClick={() => getCustomFields(smartSuiteConf, setSmartSuiteConf, setIsLoading, btcbi)}
                className="icn-btn sh-sm ml-2 mr-2 tooltip"
                style={{ '--tooltip-txt': `'${__('Refresh fields', 'bit-integrations')}'` }}
                type="button"
                disabled={loading.CRMPipelines}>
                &#x21BB;
              </button>
            )}
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
          <div className="mt-4">
            <b className="wdt-100">{__('Utilities', 'bit-integrations')}</b>
          </div>
          <div className="btcd-hr mt-1" />
          <SmartSuiteActions
            smartSuiteConf={smartSuiteConf}
            setSmartSuiteConf={setSmartSuiteConf}
            formFields={formFields}
            loading={loading}
            setLoading={setLoading}
          />
        </div>
      )}
    </>
  )
}
