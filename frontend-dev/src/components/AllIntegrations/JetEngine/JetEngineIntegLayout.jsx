/* eslint-disable react-hooks/exhaustive-deps */
import MultiSelect from 'react-multiple-select-dropdown-lite'
import { __ } from '../../../Utils/i18nwrap'
import JetEngineActions from './JetEngineActions'
import 'react-multiple-select-dropdown-lite/dist/index.css'

import JetEngineFieldMap from './JetEngineFieldMap'
import { addFieldMap } from './IntegrationHelpers'
import { jetEngineStaticFields, getJetEngineEUFields, getAllVendors } from './jetEngineCommonFunctions'
import { TASK_LIST, TASK_LIST_VALUES } from './jetEngineConstants'
import Loader from '../../Loaders/Loader'
import TableCheckBox from '../../Utilities/TableCheckBox'

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

      if (val === TASK_LIST_VALUES.CREATE_VENDOR || val === TASK_LIST_VALUES.UPDATE_VENDOR) {
        getJetEngineEUFields(newConf, setJetEngineConf, loading, setLoading)
      } else if (
        val === TASK_LIST_VALUES.DELETE_VENDOR ||
        val === TASK_LIST_VALUES.WITHDRAW_REQUEST
      ) {
        getAllVendors(newConf, setJetEngineConf, loading, setLoading)
      }
    } else {
      newConf.staticFields = []
      newConf.field_map = []
    }

    setJetEngineConf({ ...newConf })
  }

  const handleMultiSelectChange = (val, type) => {
    const newConf = { ...jetEngineConf }
    newConf[type] = val
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

        {jetEngineConf.selectedTask === TASK_LIST_VALUES.REFUND_REQUEST && (
          <span className="action-delete-task-note">
            The JetEngine Pro plugin needs to be installed and activated to use this (Refund Request)
            task.
          </span>
        )}
        {(loading.euFields || loading.vendors) && (
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

        {(jetEngineConf.selectedTask === TASK_LIST_VALUES.UPDATE_VENDOR ||
          jetEngineConf.selectedTask === TASK_LIST_VALUES.DELETE_VENDOR ||
          jetEngineConf.selectedTask === TASK_LIST_VALUES.WITHDRAW_REQUEST) && (
          <>
            <div className="flx mt-3 mb-4">
              <b className="wdt-200 d-in-b">{__('Select Vendor:', 'bit-integrations')}</b>
              <MultiSelect
                style={{ width: '450px' }}
                options={jetEngineConf?.vendors}
                className="msl-wrp-options"
                defaultValue={jetEngineConf?.selectedVendor}
                onChange={(val) => handleMultiSelectChange(val, 'selectedVendor')}
                singleSelect
              />
              <button
                onClick={() => getAllVendors(jetEngineConf, setJetEngineConf, loading, setLoading)}
                className="icn-btn sh-sm ml-2 mr-2 tooltip"
                style={{ '--tooltip-txt': `${__('Refresh Vendors', 'bit-integrations')}'` }}
                type="button">
                &#x21BB;
              </button>
            </div>
            {jetEngineConf.selectedTask === TASK_LIST_VALUES.DELETE_VENDOR && (
              <>
                <br />
                <div className="flx">
                  <span className="action-delete-task-note">
                    To delete a vendor, you can select a vendor from the list above, or you can map
                    fields.
                  </span>
                  <TableCheckBox
                    checked={jetEngineConf.deleteVendorFieldMap}
                    onChange={(e) => handleDeleteTopicFieldMapCheck(e)}
                    className=" ml-2"
                    value="select_group"
                    title={__('Map Fields', 'bit-integrations')}
                  />
                </div>
              </>
            )}
          </>
        )}

        {jetEngineConf.selectedTask === TASK_LIST_VALUES.WITHDRAW_REQUEST && (
          <div className="flx mt-3 mb-4">
            <b className="wdt-200 d-in-b">{__('Select Payment Method:', 'bit-integrations')}</b>
            <MultiSelect
              style={{ width: '450px' }}
              options={[
                { label: 'PayPal', value: 'paypal' },
                { label: 'Bank Transfer', value: 'bank' },
                { label: 'Skrill', value: 'skrill' }
              ]}
              className="msl-wrp-options"
              defaultValue={jetEngineConf?.selectedPaymentMethod}
              onChange={(val) => handleMultiSelectChange(val, 'selectedPaymentMethod')}
              singleSelect
            />
          </div>
        )}

        {(jetEngineConf.selectedTask !== TASK_LIST_VALUES.DELETE_VENDOR ||
          (jetEngineConf.selectedTask === TASK_LIST_VALUES.DELETE_VENDOR &&
            jetEngineConf.deleteVendorFieldMap)) && (
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
                <b>{__('JetEngine Fields', 'bit-integrations')}</b>
              </div>
            </div>
          </>
        )}

        {((jetEngineConf?.selectedTask && jetEngineConf?.selectedTask !== TASK_LIST_VALUES.DELETE_VENDOR) ||
          (jetEngineConf.selectedTask === TASK_LIST_VALUES.DELETE_VENDOR &&
            jetEngineConf.deleteVendorFieldMap)) &&
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

        {((jetEngineConf?.selectedTask && jetEngineConf?.selectedTask !== TASK_LIST_VALUES.DELETE_VENDOR) ||
          (jetEngineConf.selectedTask === TASK_LIST_VALUES.DELETE_VENDOR &&
            jetEngineConf.deleteVendorFieldMap)) && (
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

        {(jetEngineConf.selectedTask === TASK_LIST_VALUES.CREATE_VENDOR ||
          jetEngineConf.selectedTask === TASK_LIST_VALUES.UPDATE_VENDOR) && (
          <div>
            <br />
            <br />
            <div className="mt-4">
              <b className="wdt-100">{__('Actions', 'bit-integrations')}</b>
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
