/* eslint-disable no-unused-vars */
import { create } from 'mutative'
import MultiSelect from 'react-multiple-select-dropdown-lite'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import { __ } from '../../../Utils/i18nwrap'
import Loader from '../../Loaders/Loader'
import { addFieldMap } from './IntegrationHelpers'
import WoodpeckerActions from './WoodpeckerActions'
import { getallAccounts, getallPipelines, woodpeckerFields } from './WoodpeckerCommonFunc'
import WoodpeckerFieldMap from './WoodpeckerFieldMap'

export default function WoodpeckerIntegLayout({ formFields, handleInput, woodpeckerConf, setWoodpeckerConf, loading, setLoading, isLoading, setIsLoading, setSnackbar }) {
  const handleActionInput = (e) => {
    setWoodpeckerConf(prevConf => create(prevConf, (draftConf) => {
      const { name } = e.target

      if (e.target.value !== '') {
        draftConf.actionName = e.target.value

        if (draftConf.actionName === "accounts") {
          draftConf.woodpeckerFields = draftConf.accountFields
        } else if (draftConf.actionName === "contacts") {
          draftConf.woodpeckerFields = draftConf.contactFields
        } else if (draftConf.actionName === "opportunities") {
          draftConf.woodpeckerFields = draftConf.opportunitiyFields
          getallAccounts(draftConf, setWoodpeckerConf, loading, setLoading)
          getallPipelines(draftConf, setWoodpeckerConf, loading, setLoading)
        }
        woodpeckerFields(draftConf, setWoodpeckerConf, setIsLoading, setSnackbar)

      } else {
        delete draftConf[name]
      }
    }))
  }

  const setChanges = (val, name) => {
    const newConf = { ...woodpeckerConf }
    newConf[name] = val

    if (name === 'selectedPipeline') {
      woodpeckerConf?.pipelines.forEach(pipeline => {
        if (pipeline.id.toString() === val) {
          newConf.stages = pipeline.stages
        }
      })
    }

    setWoodpeckerConf({ ...newConf })
  }

  return (
    <>
      <br />

      <b className="wdt-200 d-in-b">{__('Select Action:', 'bit-integrations')}</b>
      <select onChange={handleActionInput} name="actionName" value={woodpeckerConf.actionName} className="btcd-paper-inp w-5">
        <option value="">{__('Select an action', 'bit-integrations')}</option>
        <option value="accounts" data-action_name="accounts">{__('Create Account', 'bit-integrations')}</option>
        <option value="contacts" data-action_name="contacts">{__('Create Contact', 'bit-integrations')}</option>
        <option value="opportunities" data-action_name="opportunities">{__('Create Opportunity', 'bit-integrations')}</option>
      </select>
      <br />
      {(loading.account || loading.pipeline) && (
        <Loader style={{
          display: 'flex',
          justifyContent: 'center',
          alignItems: 'center',
          height: 100,
          transform: 'scale(0.7)',
        }}
        />
      )}
      {woodpeckerConf.actionName === 'opportunities' && woodpeckerConf?.accounts
        && (
          <>
            <br />
            <div className="flx">
              <b className="wdt-200 d-in-b">{__('Select Account:', 'bit-integrations')}</b>
              <MultiSelect
                options={woodpeckerConf?.accounts.map(account => ({ label: account.name, value: account.id.toString() }))}
                className="msl-wrp-options dropdown-custom-width"
                defaultValue={woodpeckerConf?.selectedAccount}
                onChange={val => setChanges(val, 'selectedAccount')}
                disabled={loading.account}
                singleSelect
                closeOnSelect
              />
              <button
                onClick={() => getallAccounts(woodpeckerConf, setWoodpeckerConf, loading, setLoading)}
                className="icn-btn sh-sm ml-2 mr-2 tooltip"
                style={{ '--tooltip-txt': `'${__('Refresh Stages', 'bit-integrations')}'` }}
                type="button"
                disabled={loading.account}
              >
                &#x21BB;
              </button>
            </div>
          </>
        )}
      {woodpeckerConf.actionName === 'opportunities' && woodpeckerConf?.pipelines
        && (
          <>
            <br />
            <div className="flx">
              <b className="wdt-200 d-in-b">{__('Select Pipeline:', 'bit-integrations')}</b>
              <MultiSelect
                options={woodpeckerConf?.pipelines.map(pipeline => ({ label: pipeline.name, value: pipeline.id.toString() }))}
                className="msl-wrp-options dropdown-custom-width"
                defaultValue={woodpeckerConf?.selectedPipeline}
                onChange={val => setChanges(val, 'selectedPipeline')}
                disabled={loading.pipeline}
                singleSelect
                closeOnSelect
              />
              <button
                onClick={() => getallPipelines(woodpeckerConf, setWoodpeckerConf, loading, setLoading)}
                className="icn-btn sh-sm ml-2 mr-2 tooltip"
                style={{ '--tooltip-txt': `'${__('Refresh Pipelines', 'bit-integrations')}'` }}
                type="button"
                disabled={loading.pipeline}
              >
                &#x21BB;
              </button>
            </div>
          </>
        )}
      {woodpeckerConf.actionName === 'opportunities' && woodpeckerConf?.stages
        && (
          <>
            <br />
            <div className="flx">
              <b className="wdt-200 d-in-b">{__('Select Stage:', 'bit-integrations')}</b>
              <MultiSelect
                options={woodpeckerConf?.stages.map(stage => ({ label: stage.name, value: stage.id.toString() }))}
                className="msl-wrp-options dropdown-custom-width"
                defaultValue={woodpeckerConf?.selectedStage}
                onChange={val => setChanges(val, 'selectedStage')}
                disabled={loading.stage}
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
      {woodpeckerConf.actionName && !isLoading && (
        <div>
          <br />
          <div className="mt-5">
            <b className="wdt-100">
              {__('Field Map', 'bit-integrations')}
            </b>
            <button
              onClick={() => woodpeckerFields(woodpeckerConf, setWoodpeckerConf, setIsLoading, setSnackbar)}
              className="icn-btn sh-sm ml-2 mr-2 tooltip"
              style={{ '--tooltip-txt': `'${__('Refresh Custom Fields', 'bit-integrations')}'` }}
              type="button"
              disabled={isLoading}
            >
              &#x21BB;
            </button>
          </div>

          <br />
          <div className="btcd-hr mt-1" />
          <div className="flx flx-around mt-2 mb-2 btcbi-field-map-label">
            <div className="txt-dp"><b>{__('Form Fields', 'bit-integrations')}</b></div>
            <div className="txt-dp"><b>{__('Woodpecker Fields', 'bit-integrations')}</b></div>
          </div>

          {woodpeckerConf?.field_map.map((itm, i) => (
            <WoodpeckerFieldMap
              key={`rp-m-${i + 9}`}
              i={i}
              field={itm}
              woodpeckerConf={woodpeckerConf}
              formFields={formFields}
              setWoodpeckerConf={setWoodpeckerConf}
              setSnackbar={setSnackbar}
            />
          ))}
          <div className="txt-center btcbi-field-map-button mt-2"><button onClick={() => addFieldMap(woodpeckerConf.field_map.length, woodpeckerConf, setWoodpeckerConf, false)} className="icn-btn sh-sm" type="button">+</button></div>
          <br />
          <br />
          <div className="mt-4"><b className="wdt-100">{__('Actions', 'bit-integrations')}</b></div>
          <div className="btcd-hr mt-1" />
          <WoodpeckerActions
            woodpeckerConf={woodpeckerConf}
            setWoodpeckerConf={setWoodpeckerConf}
            formFields={formFields}
            loading={loading}
            setLoading={setLoading}
          />
        </div>
      )}
    </>
  )
}

