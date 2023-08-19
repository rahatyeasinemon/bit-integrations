/* eslint-disable no-unused-vars */
import { create } from 'mutative'
import MultiSelect from 'react-multiple-select-dropdown-lite'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import { __ } from '../../../Utils/i18nwrap'
import Loader from '../../Loaders/Loader'
import { addFieldMap } from './IntegrationHelpers'
import SalesflareActions from './SalesflareActions'
import { generateMappedField, salesflareFields } from './SalesflareCommonFunc'
import SalesflareFieldMap from './SalesflareFieldMap'

export default function SalesflareIntegLayout({ formFields, handleInput, salesflareConf, setSalesflareConf, loading, setLoading, isLoading, setIsLoading, setSnackbar }) {
  const handleActionInput = (e) => {
    setSalesflareConf(prevConf => create(prevConf, (draftConf) => {
      const { name } = e.target

      if (e.target.value !== '') {
        draftConf.actionName = e.target.value

        if (draftConf.actionName === "accounts") {
          draftConf.salesflareFields = draftConf.accountFields
        } else if (draftConf.actionName === "contacts") {
          draftConf.salesflareFields = draftConf.contactFields
        } else if (draftConf.actionName === "opportunities") {
          draftConf.salesflareFields = draftConf.opportunitiyFields
        }
        salesflareFields(draftConf, setSalesflareConf, setIsLoading, setSnackbar)

      } else {
        delete draftConf[name]
      }
    }))
  }

  const setChanges = (val, name) => {
    const newConf = { ...salesflareConf }
    newConf[name] = val

    setSalesflareConf({ ...newConf })
  }

  return (
    <>
      <br />

      <b className="wdt-200 d-in-b">{__('Select Action:', 'bit-integrations')}</b>
      <select onChange={handleActionInput} name="actionName" value={salesflareConf.actionName} className="btcd-paper-inp w-5">
        <option value="">{__('Select an action', 'bit-integrations')}</option>
        <option value="accounts" data-action_name="accounts">{__('Create Account', 'bit-integrations')}</option>
        <option value="contacts" data-action_name="contacts">{__('Create Contact', 'bit-integrations')}</option>
        <option value="opportunities" data-action_name="opportunities">{__('Create Opportunity', 'bit-integrations')}</option>
      </select>
      <br />
      {salesflareConf.actionName === 'lead'
        && (
          <>
            <br />
            <div className="flx">
              <b className="wdt-200 d-in-b">{__('Lead Status:', 'bit-integrations')}</b>
              <MultiSelect
                options={salesflareConf?.leadStatus.map(status => ({ label: status, value: status }))}
                className="msl-wrp-options dropdown-custom-width"
                defaultValue={salesflareConf?.selectedLeadStatus}
                onChange={val => setChanges(val, 'selectedLeadStatus')}
                disabled={isLoading}
                singleSelect
                closeOnSelect
              />
            </div>
          </>
        )}
      {salesflareConf.actionName === 'customer'
        && (
          <>
            <br />
            <div className="flx">
              <b className="wdt-200 d-in-b">{__('Customer Type:', 'bit-integrations')}</b>
              <MultiSelect
                options={['Company', 'Individual']?.map(type => ({ label: type, value: type }))}
                className="msl-wrp-options dropdown-custom-width"
                defaultValue={salesflareConf?.selectedCustomerType}
                onChange={val => setChanges(val, 'selectedCustomerType')}
                disabled={isLoading}
                singleSelect
                closeOnSelect
              />
            </div>
          </>
        )}

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
      {salesflareConf.actionName && !isLoading && (
        <div>
          <br />
          <div className="mt-5">
            <b className="wdt-100">
              {__('Field Map', 'bit-integrations')}
            </b>
          </div>

          <br />
          <div className="btcd-hr mt-1" />
          <div className="flx flx-around mt-2 mb-2 btcbi-field-map-label">
            <div className="txt-dp"><b>{__('Form Fields', 'bit-integrations')}</b></div>
            <div className="txt-dp"><b>{__('Salesflare Fields', 'bit-integrations')}</b></div>
          </div>

          {salesflareConf?.field_map.map((itm, i) => (
            <SalesflareFieldMap
              key={`rp-m-${i + 9}`}
              i={i}
              field={itm}
              salesflareConf={salesflareConf}
              formFields={formFields}
              setSalesflareConf={setSalesflareConf}
              setSnackbar={setSnackbar}
            />
          ))}
          <div className="txt-center btcbi-field-map-button mt-2"><button onClick={() => addFieldMap(salesflareConf.field_map.length, salesflareConf, setSalesflareConf, false)} className="icn-btn sh-sm" type="button">+</button></div>
          <br />
          <br />
          <div className="mt-4"><b className="wdt-100">{__('Actions', 'bit-integrations')}</b></div>
          <div className="btcd-hr mt-1" />
          <SalesflareActions
            salesflareConf={salesflareConf}
            setSalesflareConf={setSalesflareConf}
            formFields={formFields}
            loading={loading}
            setLoading={setLoading}
          />
        </div>
      )}
    </>
  )
}

