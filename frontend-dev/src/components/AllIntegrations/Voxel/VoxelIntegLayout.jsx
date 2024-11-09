/* eslint-disable react-hooks/exhaustive-deps */
import MultiSelect from 'react-multiple-select-dropdown-lite'
import { __ } from '../../../Utils/i18nwrap'
import VoxelActions from './VoxelActions'
import 'react-multiple-select-dropdown-lite/dist/index.css'

import VoxelFieldMap from './VoxelCalendarFieldMap'
import { addFieldMap } from './IntegrationHelpers'
import { getAllEvents, voxelStaticFields } from './VoxelCommonFunctions'
import { TASK_LIST, TASK_LIST_VALUES } from './voxelConstants'
import Loader from '../../Loaders/Loader'

export default function VoxelIntegLayout({
  formFields,
  voxelConf,
  setVoxelConf,
  loading,
  setLoading,
  setSnackbar
}) {
  const setChanges = (val) => {
    const newConf = { ...voxelConf }
    newConf.selectedTask = val
    newConf.selectedEvent = ''

    if (val) {
      const fieldsAndFieldMap = voxelStaticFields(val)
      newConf.staticFields = fieldsAndFieldMap.staticFields
      newConf.field_map = fieldsAndFieldMap.fieldMap
      if (val === TASK_LIST_VALUES.NEW_ATTENDEE) {
        getAllEvents(newConf, setVoxelConf, loading, setLoading)
      }
    } else {
      newConf.staticFields = []
      newConf.field_map = []
    }

    setVoxelConf({ ...newConf })
  }

  const handleMultiSelectChange = (val, type) => {
    const newConf = { ...voxelConf }
    newConf[type] = val
    setVoxelConf({ ...newConf })
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
            defaultValue={voxelConf?.selectedTask}
            onChange={(val) => setChanges(val)}
            singleSelect
          />
        </div>

        {voxelConf.selectedTask === TASK_LIST_VALUES.NEW_ATTENDEE && (
          <div className="flx mt-3 mb-4">
            <b className="wdt-200 d-in-b">{__('Select Event:', 'bit-integrations')}</b>
            <MultiSelect
              style={{ width: '450px' }}
              options={voxelConf?.events}
              className="msl-wrp-options"
              defaultValue={voxelConf?.selectedEvent}
              onChange={(val) => handleMultiSelectChange(val, 'selectedEvent')}
              disabled={loading.events}
              singleSelect
            />
            <button
              onClick={() =>
                getAllEvents(voxelConf, setVoxelConf, loading, setLoading)
              }
              className="icn-btn sh-sm ml-2 mr-2 tooltip"
              style={{ '--tooltip-txt': `'${__('Refresh events', 'bit-integrations')}'` }}
              disabled={loading.events}
              type="button">
              &#x21BB;
            </button>
          </div>
        )}

        {loading.events && (
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
            <b>{__('The Events Calendar Fields', 'bit-integrations')}</b>
          </div>
        </div>

        {voxelConf.selectedTask &&
          voxelConf?.field_map.map((itm, i) => (
            <VoxelFieldMap
              key={`rp-m-${i + 9}`}
              i={i}
              field={itm}
              voxelConf={voxelConf}
              formFields={formFields}
              setVoxelConf={setVoxelConf}
              setSnackbar={setSnackbar}
            />
          ))}

        {voxelConf?.selectedTask && (
          <div className="txt-center btcbi-field-map-button mt-2">
            <button
              onClick={() =>
                addFieldMap(
                  voxelConf.field_map.length,
                  voxelConf,
                  setVoxelConf,
                  false
                )
              }
              className="icn-btn sh-sm"
              type="button">
              +
            </button>
          </div>
        )}

        {/* <div>
          <br />
          <br />
          <div className="mt-4">
            <b className="wdt-100">{__('Utilities', 'bit-integrations')}</b>
          </div>
          <div className="btcd-hr mt-1" />
          <VoxelActions
            voxelConf={voxelConf}
            setVoxelConf={setVoxelConf}
            loading={loading}
            setLoading={setLoading}
          />
        </div> */}
      </div>
    </>
  )
}
