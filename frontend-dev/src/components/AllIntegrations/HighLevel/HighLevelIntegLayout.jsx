// eslint-disable-next-line import/no-extraneous-dependencies
import { __ } from '../../../Utils/i18nwrap'
import Loader from '../../Loaders/Loader'
import { addFieldMap } from '../IntegrationHelpers/IntegrationHelpers'
import {
  highLevelAuthentication,
  getCustomFields,
  highLevelStaticFields
} from './HighLevelCommonFunc'
import HighLevelFieldMap from './HighLevelFieldMap'
import { useState } from 'react'
import HighLevelActions from './HighLevelAction'
import MultiSelect from 'react-multiple-select-dropdown-lite'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import { TASK_LIST, TASK_LIST_VALUES } from './highlevelConstants'

export default function HighLevelIntegLayout({
  formFields,
  highLevelConf,
  setHighLevelConf,
  loading,
  setLoading
}) {
  const [error, setError] = useState({ name: '', api_key: '' })
  const [isAuthorized, setisAuthorized] = useState(false)

  const handleInput = (e) => {
    const accountId = e.target.value
    const newConf = { ...highLevelConf }

    if (accountId) {
      newConf.selectedAccountId = accountId
      getCustomFields(newConf, setHighLevelConf, setLoading)
    } else {
      newConf.selectedAccountId = accountId
      // newConf.highLevelFields = staticFields
    }

    setHighLevelConf({ ...newConf })
  }

  const setChanges = (val) => {
    console.log(val)
    const newConf = highLevelConf
    newConf.selectedTask = val

    if (val) {
      const fieldsAndFieldMap = highLevelStaticFields(val)
      newConf.highLevelFields = fieldsAndFieldMap.staticFields
      newConf.field_map = fieldsAndFieldMap.fieldMap

      if (val === TASK_LIST_VALUES.CREATE_CONTACT) {
        getCustomFields(newConf, setHighLevelConf, loading, setLoading)
      }
    } else {
      newConf.highLevelFields = []
      newConf.field_map = []
    }

    setHighLevelConf({ ...newConf })
  }

  return (
    <>
      <br />
      <div className="flx mt-3 mb-4">
        <b className="wdt-200 d-in-b">{__('Select Task:', 'bit-integrations')}</b>
        <MultiSelect
          style={{ width: '450px' }}
          options={TASK_LIST}
          className="msl-wrp-options"
          defaultValue={highLevelConf?.selectedTask}
          onChange={(val) => setChanges(val)}
          singleSelect
        />
      </div>
      <br />
      <br />

      {(loading.accounts || loading.customFields) && (
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

      <div className="mt-4">
        <b className="wdt-100">{__('Map Fields', 'bit-integrations')}</b>
        {highLevelConf?.selectedTask === TASK_LIST_VALUES.CREATE_CONTACT && (
          <button
            onClick={() => getCustomFields(highLevelConf, setHighLevelConf, loading, setLoading)}
            className="icn-btn sh-sm ml-2 mr-2 tooltip"
            style={{ '--tooltip-txt': `'${__('Refresh custom fields', 'bit-integrations')}'` }}
            type="button"
            disabled={loading?.customFields}>
            &#x21BB;
          </button>
        )}
      </div>
      {highLevelConf?.selectedTask && (
        <>
          <div className="btcd-hr mt-1" />
          <div className="flx flx-around mt-2 mb-2 btcbi-field-map-label">
            <div className="txt-dp">
              <b>{__('Form Fields', 'bit-integrations')}</b>
            </div>
            <div className="txt-dp">
              <b>{__('HighLevel Fields', 'bit-integrations')}</b>
            </div>
          </div>

          {highLevelConf.field_map.map((itm, i) => (
            <HighLevelFieldMap
              key={`HighLevel-m-${i + 9}`}
              i={i}
              field={itm}
              highLevelConf={highLevelConf}
              formFields={formFields}
              setHighLevelConf={setHighLevelConf}
            />
          ))}
          <div className="txt-center btcbi-field-map-button mt-2">
            <button
              onClick={() =>
                addFieldMap(highLevelConf.field_map.length, highLevelConf, setHighLevelConf)
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
          <HighLevelActions
            highLevelConf={highLevelConf}
            setHighLevelConf={setHighLevelConf}
            loading={loading}
            setLoading={setLoading}
          />
        </>
      )}
    </>
  )
}
