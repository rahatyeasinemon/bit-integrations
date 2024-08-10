/* eslint-disable react-hooks/exhaustive-deps */
import MultiSelect from 'react-multiple-select-dropdown-lite'
import { __ } from '../../../Utils/i18nwrap'
import DokanActions from './DokanActions'
import 'react-multiple-select-dropdown-lite/dist/index.css'

import DokanFieldMap from './DokanFieldMap'
import { addFieldMap } from './IntegrationHelpers'
import { getDokanTopics, dokanStaticFields } from './dokanCommonFunctions'
import { TASK_LIST, TASK_LIST_VALUES } from './dokanConstants'
import Loader from '../../Loaders/Loader'
import TableCheckBox from '../../Utilities/TableCheckBox'

export default function DokanIntegLayout({
  formFields,
  dokanConf,
  setDokanConf,
  loading,
  setLoading,
  setSnackbar
}) {
  const setChanges = (val) => {
    const newConf = { ...dokanConf }
    newConf.selectedTask = val

    if (val) {
      if (val === TASK_LIST_VALUES.DELETE_TOPIC) {
        getDokanTopics(newConf, setDokanConf, loading, setLoading)
      }
      const fieldsAndFieldMap = dokanStaticFields(val)
      newConf.staticFields = fieldsAndFieldMap.staticFields
      newConf.field_map = fieldsAndFieldMap.fieldMap
    } else {
      newConf.staticFields = []
      newConf.field_map = []
    }

    setDokanConf({ ...newConf })
  }

  const handleTopicChange = (val) => {
    const newConf = { ...dokanConf }
    newConf.selectedTopic = val
    setDokanConf({ ...newConf })
  }

  const handleDeleteTopicFieldMapCheck = (event) => {
    const newConf = { ...dokanConf }

    if (event.target.checked) {
      newConf.deleteTopicFieldMap = true
    } else {
      newConf.deleteTopicFieldMap = false
    }

    setDokanConf({ ...newConf })
  }

  return (
    <>
      <div>
        <div className="flx mt-3">
          <b className="wdt-200 d-in-b">{__('Select Task:', 'bit-integrations')}</b>
          <MultiSelect
            style={{ width: '450px' }}
            options={TASK_LIST}
            className="msl-wrp-options"
            defaultValue={dokanConf?.selectedTask}
            onChange={(val) => setChanges(val)}
            singleSelect
          />
        </div>
        <br />
        {dokanConf.selectedTask === TASK_LIST_VALUES.DELETE_TOPIC && (
          <>
            {loading.topics && (
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
            <div className="flx mt-3 mb-4">
              <b className="wdt-200 d-in-b">{__('Select Topic:', 'bit-integrations')}</b>
              <MultiSelect
                style={{ width: '450px' }}
                options={dokanConf?.topics}
                className="msl-wrp-options"
                defaultValue={dokanConf?.selectedTopic}
                onChange={(val) => handleTopicChange(val)}
                singleSelect
              />
              <button
                onClick={() => getDokanTopics(dokanConf, setDokanConf, loading, setLoading)}
                className="icn-btn sh-sm ml-2 mr-2 tooltip"
                style={{ '--tooltip-txt': `${__('Refresh topics', 'bit-integrations')}'` }}
                type="button">
                &#x21BB;
              </button>
            </div>
            <br />
            <div className="flx">
              <span className="dokan-delete-topic-note">
                To delete a topic, you can select a topic from the list above, or you can map
                fields.
              </span>
              <TableCheckBox
                checked={dokanConf.deleteTopicFieldMap}
                onChange={(e) => handleDeleteTopicFieldMapCheck(e)}
                className=" ml-2"
                value="select_group"
                title={__('Map Fields', 'bit-integrations')}
              />
            </div>
            <br />
          </>
        )}

        {(dokanConf.selectedTask !== TASK_LIST_VALUES.DELETE_TOPIC ||
          (dokanConf.selectedTask === TASK_LIST_VALUES.DELETE_TOPIC &&
            dokanConf.deleteTopicFieldMap)) && (
          <>
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
                <b>{__('Dokan Fields', 'bit-integrations')}</b>
              </div>
            </div>
          </>
        )}

        {((dokanConf?.selectedTask && dokanConf?.selectedTask !== TASK_LIST_VALUES.DELETE_TOPIC) ||
          (dokanConf.selectedTask === TASK_LIST_VALUES.DELETE_TOPIC &&
            dokanConf.deleteTopicFieldMap)) &&
          dokanConf?.field_map.map((itm, i) => (
            <DokanFieldMap
              key={`rp-m-${i + 9}`}
              i={i}
              field={itm}
              dokanConf={dokanConf}
              formFields={formFields}
              setDokanConf={setDokanConf}
              setSnackbar={setSnackbar}
            />
          ))}

        {((dokanConf?.selectedTask && dokanConf?.selectedTask !== TASK_LIST_VALUES.DELETE_TOPIC) ||
          (dokanConf.selectedTask === TASK_LIST_VALUES.DELETE_TOPIC &&
            dokanConf.deleteTopicFieldMap)) && (
          <div>
            <div className="txt-center btcbi-field-map-button mt-2">
              <button
                onClick={() =>
                  addFieldMap(dokanConf.field_map.length, dokanConf, setDokanConf, false)
                }
                className="icn-btn sh-sm"
                type="button">
                +
              </button>
            </div>
            <br />
            <br />
            <div className="mt-4">
              <b className="wdt-100">{__('Actions', 'bit-integrations')}</b>
            </div>
            <div className="btcd-hr mt-1" />
            <DokanActions
              dokanConf={dokanConf}
              setDokanConf={setDokanConf}
              loading={loading}
              setLoading={setLoading}
            />
          </div>
        )}
      </div>
    </>
  )
}
