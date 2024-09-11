/* eslint-disable react-hooks/exhaustive-deps */
import MultiSelect from 'react-multiple-select-dropdown-lite'
import { __ } from '../../../Utils/i18nwrap'
import JetEngineActions from './JetEngineActions'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import JetEngineFieldMap from './JetEngineFieldMap'
import { addFieldMap } from './IntegrationHelpers'
import { getJetEngineRelationTypes, jetEngineStaticFields } from './jetEngineCommonFunctions'
import { TASK_LIST, TASK_LIST_VALUES } from './jetEngineConstants'
import Loader from '../../Loaders/Loader'

export default function JetEngineIntegLayout({
  formFields,
  jetEngineConf,
  setJetEngineConf,
  loading,
  setLoading,
  setSnackbar
}) {
  const setChanges = (val) => {
    const newConf = { ...jetEngineConf }
    newConf.selectedTask = val

    if (val) {
      const fieldsAndFieldMap = jetEngineStaticFields(val)
      newConf.staticFields = fieldsAndFieldMap.staticFields
      newConf.field_map = fieldsAndFieldMap.fieldMap

      if (val === TASK_LIST_VALUES.CREATE_RELATION) {
        getJetEngineRelationTypes(newConf, setJetEngineConf, loading, setLoading)
      }
    } else {
      newConf.staticFields = []
      newConf.field_map = []
    }

    setJetEngineConf({ ...newConf })
  }

  const handleRelationTypeChange = (val, type) => {
    const newConf = { ...jetEngineConf }
    newConf.relOptions[type] = val
    setJetEngineConf({ ...newConf })
  }

  const handleDeleteTopicFieldMapCheck = (event) => {
    const newConf = { ...jetEngineConf }

    if (event.target.checked) {
      newConf.deleteVendorFieldMap = true
    } else {
      newConf.deleteVendorFieldMap = false
    }

    setJetEngineConf({ ...newConf })
  }

  return (
    <>
      <div>
        <div className="flx mt-3 mb-4">
          <b className="wdt-200 d-in-b">{__('Select Task:', 'bit-integrations')}</b>
          <MultiSelect
            style={{ width: '450px' }}
            options={TASK_LIST}
            className="msl-wrp-options"
            defaultValue={jetEngineConf?.selectedTask}
            onChange={(val) => setChanges(val)}
            singleSelect
          />
        </div>

        {jetEngineConf.selectedTask === TASK_LIST_VALUES.CREATE_RELATION && (
          <>
            <div className="flx mt-3 mb-4">
              <b className="wdt-200 d-in-b">{__('Parent object:', 'bit-integrations')}</b>
              <MultiSelect
                style={{ width: '450px' }}
                options={jetEngineConf?.allRelationTypes}
                className="msl-wrp-options"
                defaultValue={jetEngineConf?.relOptions?.parentObject}
                onChange={(val) => handleRelationTypeChange(val, 'parentObject')}
                singleSelect
              />
              <button
                onClick={() =>
                  getJetEngineRelationTypes(jetEngineConf, setJetEngineConf, loading, setLoading)
                }
                className="icn-btn sh-sm ml-2 mr-2 tooltip"
                style={{ '--tooltip-txt': `${__('Refresh parent objects', 'bit-integrations')}'` }}
                type="button">
                &#x21BB;
              </button>
            </div>
            <div className="flx mt-3 mb-4">
              <b className="wdt-200 d-in-b">{__('Child object:', 'bit-integrations')}</b>
              <MultiSelect
                style={{ width: '450px' }}
                options={jetEngineConf?.allRelationTypes}
                className="msl-wrp-options"
                defaultValue={jetEngineConf?.relOptions?.childObject}
                onChange={(val) => handleRelationTypeChange(val, 'childObject')}
                singleSelect
              />
              <button
                onClick={() =>
                  getJetEngineRelationTypes(jetEngineConf, setJetEngineConf, loading, setLoading)
                }
                className="icn-btn sh-sm ml-2 mr-2 tooltip"
                style={{ '--tooltip-txt': `${__('Refresh child objects', 'bit-integrations')}'` }}
                type="button">
                &#x21BB;
              </button>
            </div>
            <div className="flx mt-3 mb-4">
              <b className="wdt-200 d-in-b">{__('Relation type:', 'bit-integrations')}</b>
              <MultiSelect
                style={{ width: '450px' }}
                options={[
                  { label: __('One to one', 'bit-integrations'), value: 'one_to_one' },
                  { label: __('One to many', 'bit-integrations'), value: 'one_to_many' },
                  { label: __('Many to many', 'bit-integrations'), value: 'many_to_many' }
                ]}
                className="msl-wrp-options"
                defaultValue={jetEngineConf?.relOptions?.selectedRelationType}
                onChange={(val) => handleRelationTypeChange(val, 'selectedRelationType')}
                singleSelect
              />
            </div>
          </>
        )}

        {loading.relation_types && (
          <Loader
            style={{
              display: 'flex',
              justifyContent: 'center',
              alignItems: 'center',
              height: 45,
              transform: 'scale(0.5)'
            }}
          />
        )}

        <div className="mt-5">
          <b className="wdt-100">{__('Field Map', 'bit-integrations')}</b>
        </div>
        <br />
        <div className="btcd-hr mt-1" />
        <div className="flx flx-around mt-2 mb-2 btcbi-field-map-label">
          <div className="txt-dp">
            <b>{__('Form Fields', 'bit-integrations')}</b>
          </div>
          <div className="txt-dp">
            <b>{__('JetEngine Fields', 'bit-integrations')}</b>
          </div>
        </div>

        {jetEngineConf?.selectedTask &&
          jetEngineConf?.field_map.map((itm, i) => (
            <JetEngineFieldMap
              key={`rp-m-${i + 9}`}
              i={i}
              field={itm}
              jetEngineConf={jetEngineConf}
              formFields={formFields}
              setJetEngineConf={setJetEngineConf}
              setSnackbar={setSnackbar}
            />
          ))}

        {jetEngineConf?.selectedTask && (
          <div className="txt-center btcbi-field-map-button mt-2">
            <button
              onClick={() =>
                addFieldMap(jetEngineConf.field_map.length, jetEngineConf, setJetEngineConf, false)
              }
              className="icn-btn sh-sm"
              type="button">
              +
            </button>
          </div>
        )}

        {(jetEngineConf.selectedTask === TASK_LIST_VALUES.CREATE_POST_TYPE ||
          jetEngineConf.selectedTask === TASK_LIST_VALUES.CREATE_CONTENT_TYPE ||
          jetEngineConf.selectedTask === TASK_LIST_VALUES.CREATE_TAXONOMY ||
          jetEngineConf.selectedTask === TASK_LIST_VALUES.CREATE_RELATION) && (
          <div>
            <br />
            <br />
            <div className="mt-4">
              <b className="wdt-100">{__('Utilities', 'bit-integrations')}</b>
            </div>
            <div className="btcd-hr mt-1" />
            <JetEngineActions
              jetEngineConf={jetEngineConf}
              setJetEngineConf={setJetEngineConf}
              loading={loading}
              setLoading={setLoading}
            />
          </div>
        )}
      </div>
    </>
  )
}
