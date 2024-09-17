// eslint-disable-next-line import/no-extraneous-dependencies
import { __ } from '../../../Utils/i18nwrap'
import Loader from '../../Loaders/Loader'
import { addFieldMap } from '../IntegrationHelpers/IntegrationHelpers'
import { getCustomFields, highLevelStaticFields, getContacts } from './HighLevelCommonFunc'
import HighLevelFieldMap from './HighLevelFieldMap'
import { useState } from 'react'
import HighLevelActions from './HighLevelAction'
import MultiSelect from 'react-multiple-select-dropdown-lite'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import { OPTIONAL_FIELD_MAP_ARRAY, TASK_LIST, TASK_LIST_VALUES } from './highlevelConstants'

export default function HighLevelIntegLayout({
  formFields,
  highLevelConf,
  setHighLevelConf,
  loading,
  setLoading
}) {
  const setChanges = (val) => {
    const newConf = highLevelConf
    newConf.selectedTask = val

    if (val) {
      const fieldsAndFieldMap = highLevelStaticFields(val)
      newConf.highLevelFields = fieldsAndFieldMap.staticFields
      newConf.field_map = fieldsAndFieldMap.fieldMap

      if (val === TASK_LIST_VALUES.CREATE_CONTACT) {
        getCustomFields(newConf, setHighLevelConf, loading, setLoading)
      } else if (val === TASK_LIST_VALUES.UPDATE_CONTACT) {
        getContacts(newConf, setHighLevelConf, loading, setLoading)
      }
    } else {
      newConf.highLevelFields = []
      newConf.field_map = []
    }

    setHighLevelConf({ ...newConf })
  }

  const handleMultiSelectChange = (val, type) => {
    const newConf = { ...highLevelConf }
    newConf[type] = val
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
      {highLevelConf.selectedTask === TASK_LIST_VALUES.UPDATE_CONTACT && (
        <div className="flx mt-3 mb-4">
          <b className="wdt-200 d-in-b">{__('Select Contact:', 'bit-integrations')}</b>
          <MultiSelect
            style={{ width: '450px' }}
            options={highLevelConf.contacts}
            className="msl-wrp-options"
            defaultValue={highLevelConf?.selectedContact}
            onChange={(val) => handleMultiSelectChange(val, 'selectedContact')}
            singleSelect
          />
        </div>
      )}

      {(loading.accounts || loading.customFields || loading.contacts) && (
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
      {OPTIONAL_FIELD_MAP_ARRAY.includes(highLevelConf.selectedTask) && (
        <>
          <div className="flx">
            <span className="action-delete-task-note">
              {__(
                'To update, you can select from the list above, or you can map fields. If not selected, ID field mapping is required.',
                'bit-integrations'
              )}
            </span>
          </div>
        </>
      )}
      {highLevelConf?.selectedTask && (
        <>
          <div className="mt-4">
            <b className="wdt-100">{__('Map Fields', 'bit-integrations')}</b>
            {(highLevelConf?.selectedTask === TASK_LIST_VALUES.CREATE_CONTACT ||
              highLevelConf?.selectedTask === TASK_LIST_VALUES.UPDATE_CONTACT) && (
              <button
                onClick={() =>
                  getCustomFields(highLevelConf, setHighLevelConf, loading, setLoading)
                }
                className="icn-btn sh-sm ml-2 mr-2 tooltip"
                style={{ '--tooltip-txt': `'${__('Refresh custom fields', 'bit-integrations')}'` }}
                type="button"
                disabled={loading.customFields || loading.contacts}>
                &#x21BB;
              </button>
            )}
          </div>

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
        </>
      )}
      {(highLevelConf.selectedTask === TASK_LIST_VALUES.CREATE_CONTACT ||
        highLevelConf.selectedTask === TASK_LIST_VALUES.UPDATE_CONTACT) && (
        <>
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
