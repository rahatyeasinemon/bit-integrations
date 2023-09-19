/* eslint-disable no-unused-vars */
import MultiSelect from 'react-multiple-select-dropdown-lite'
import { __ } from '../../../Utils/i18nwrap'
import Loader from '../../Loaders/Loader'
import MoxieCRMActions from './MoxieCRMActions'
import { getAllCRMPeoples, getAllCRMPipelines, getCustomFields } from './MoxieCRMCommonFunc'
import MoxieCRMFieldMap from './MoxieCRMFieldMap'
import { addFieldMap } from './IntegrationHelpers'
import 'react-multiple-select-dropdown-lite/dist/index.css'

export default function MoxieCRMIntegLayout({ formFields, handleInput, moxiecrmConf, setMoxieCRMConf, loading, setLoading, setSnackbar }) {
  const handleActionInput = (e) => {
    const newConf = { ...moxiecrmConf }
    newConf.field_map = [
      { formField: '', moxiecrmFormField: '' },
    ]
    const { name } = e.target
    if (e.target.value !== '') {
      newConf[name] = e.target.value
      getCustomFields(newConf, setMoxieCRMConf, setLoading)
      if (e.target.value === 'opportunity') {
        getAllCRMPeoples(newConf, setMoxieCRMConf, setLoading)
      }
    } else {
      delete newConf[name]
    }
    setMoxieCRMConf({ ...newConf })
  }

  const setChanges = (val, name) => {
    const newConf = { ...moxiecrmConf }
    newConf[name] = val
    if (name === 'selectedCRMPeople' && val !== '' && moxiecrmConf.actionName === 'opportunity') {
      newConf.selectedCRMPipelines = ''
      getAllCRMPipelines(newConf, setMoxieCRMConf, setLoading)
    }
    setMoxieCRMConf({ ...newConf })
  }

  return (
    <>
      <br />

      <b className="wdt-200 d-in-b">{__('Select Action:', 'bit-integrations')}</b>
      <select onChange={handleActionInput} name="actionName" value={moxiecrmConf.actionName} className="btcd-paper-inp w-5">
        <option value="">{__('Select an action', 'bit-integrations')}</option>
        <option value="company">{__('Create Company', 'bit-integrations')}</option>
        <option value="person">{__('Create Person', 'bit-integrations')}</option>
        <option value="opportunity">{__('Create Opportunity', 'bit-integrations')}</option>
        <option value="task">{__('Create Task', 'bit-integrations')}</option>
      </select>
      {(loading.CRMPeoples || loading.CRMPipelines) && (
        <Loader style={{
          display: 'flex',
          justifyContent: 'center',
          alignItems: 'center',
          height: 100,
          transform: 'scale(0.7)',
        }}
        />
      )}
      {(moxiecrmConf.actionName === 'opportunity')
        && (
          <>
            <br />
            <br />
            <div className="flx">
              <b className="wdt-200 d-in-b">{__('Select People:', 'bit-integrations')}</b>
              <MultiSelect
                options={moxiecrmConf?.CRMPeoples?.map(CRMPeople => ({ label: CRMPeople.name, value: CRMPeople.id }))}
                className="msl-wrp-options dropdown-custom-width"
                defaultValue={moxiecrmConf?.selectedCRMPeople}
                onChange={val => setChanges(val, 'selectedCRMPeople')}
                disabled={loading.CRMPeoples}
                singleSelect
              />
              <button
                onClick={() => getAllCRMPeoples(moxiecrmConf, setMoxieCRMConf, setLoading)}
                className="icn-btn sh-sm ml-2 mr-2 tooltip"
                style={{ '--tooltip-txt': `'${__('Refresh peoples', 'bit-integrations')}'` }}
                type="button"
                disabled={loading.CRMPeoples}
              >
                &#x21BB;
              </button>
            </div>
          </>
        )}
      {((moxiecrmConf.actionName === 'opportunity') && moxiecrmConf.selectedCRMPeople)
        && (
          <>
            <br />
            <br />
            <div className="flx">
              <b className="wdt-200 d-in-b">{__('Select Pipeline:', 'bit-integrations')}</b>
              <MultiSelect
                options={moxiecrmConf?.CRMPipelines?.map(CRMPipeline => ({ label: CRMPipeline.name, value: CRMPipeline.id }))}
                className="msl-wrp-options dropdown-custom-width"
                defaultValue={moxiecrmConf?.selectedCRMPipelines}
                onChange={val => setChanges(val, 'selectedCRMPipelines')}
                disabled={loading.CRMPipelines}
                singleSelect
              />
              <button
                onClick={() => getAllCRMPipelines(moxiecrmConf, setMoxieCRMConf, setLoading)}
                className="icn-btn sh-sm ml-2 mr-2 tooltip"
                style={{ '--tooltip-txt': `'${__('Refresh Pipelines', 'bit-integrations')}'` }}
                type="button"
                disabled={loading.CRMPipelines}
              >
                &#x21BB;
              </button>
            </div>
          </>
        )}
      {(loading.customFields) && (
        <Loader style={{
          display: 'flex',
          justifyContent: 'center',
          alignItems: 'center',
          height: 100,
          transform: 'scale(0.7)',
        }}
        />
      )}
      {moxiecrmConf.actionName && (
        <div>
          <br />
          <div className="mt-5">
            <b className="wdt-100">
              {__('Field Map', 'bit-integrations')}
            </b>
            <button
              onClick={() => getCustomFields(moxiecrmConf, setMoxieCRMConf, setLoading)}
              className="icn-btn sh-sm ml-2 mr-2 tooltip"
              style={{ '--tooltip-txt': `'${__('Refresh Fields', 'bit-integrations')}'` }}
              type="button"
              disabled={loading.CRMPipelines}
            >
              &#x21BB;
            </button>
          </div>

          <br />
          <div className="btcd-hr mt-1" />
          <div className="flx flx-around mt-2 mb-2 btcbi-field-map-label">
            <div className="txt-dp"><b>{__('Form Fields', 'bit-integrations')}</b></div>
            <div className="txt-dp"><b>{__('MoxieCRM Fields', 'bit-integrations')}</b></div>

          </div>

          {moxiecrmConf?.field_map.map((itm, i) => (
            <MoxieCRMFieldMap
              key={`rp-m-${i + 9}`}
              i={i}
              field={itm}
              moxiecrmConf={moxiecrmConf}
              formFields={formFields}
              setMoxieCRMConf={setMoxieCRMConf}
              setSnackbar={setSnackbar}
            />
          ))}
          <div className="txt-center btcbi-field-map-button mt-2"><button onClick={() => addFieldMap(moxiecrmConf.field_map.length, moxiecrmConf, setMoxieCRMConf, false)} className="icn-btn sh-sm" type="button">+</button></div>
          <br />
          <br />
          <div className="mt-4"><b className="wdt-100">{__('Actions', 'bit-integrations')}</b></div>
          <div className="btcd-hr mt-1" />
          <MoxieCRMActions
            moxiecrmConf={moxiecrmConf}
            setMoxieCRMConf={setMoxieCRMConf}
            formFields={formFields}
            loading={loading}
            setLoading={setLoading}
          />
        </div>
      )}
    </>
  )
}
