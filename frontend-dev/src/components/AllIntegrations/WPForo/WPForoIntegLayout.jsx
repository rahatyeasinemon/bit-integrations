/* eslint-disable react-hooks/exhaustive-deps */
import MultiSelect from 'react-multiple-select-dropdown-lite'
import { __ } from '../../../Utils/i18nwrap'
import WPForoActions from './WPForoActions'
import 'react-multiple-select-dropdown-lite/dist/index.css'

import WPForoFieldMap from './WPForoFieldMap'
import { addFieldMap } from './IntegrationHelpers'

export default function WPForoIntegLayout({ formFields, wpforoConf, setWPForoConf, loading, setLoading, setSnackbar }) {

  const setChanges = (val) => {
    const newConf = { ...wpforoConf }
    newConf.selectedTask = val
    setWPForoConf({ ...newConf })
  }

  return (
    <>
      <div>
        <div className="flx mt-3">
          <b className="wdt-200 d-in-b">{__('Select Task:', 'bit-integrations')}</b>
          <MultiSelect
            style={{ width: '450px' }}
            options={[{ label: 'Add User to Access Group', value: 'grantAccess' },
            { label: 'Remove User from Access Group', value: 'revokeAccess' }]}
            className="msl-wrp-options"
            defaultValue={wpforoConf?.selectedTask}
            onChange={val => setChanges(val)}
            singleSelect
          />
        </div>
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
          <div className="txt-dp"><b>{__('WPForo Fields', 'bit-integrations')}</b></div>
        </div>

        {wpforoConf?.selectedTask && wpforoConf?.field_map.map((itm, i) => (
          <WPForoFieldMap
            key={`rp-m-${i + 9}`}
            i={i}
            field={itm}
            wpforoConf={wpforoConf}
            formFields={formFields}
            setWPForoConf={setWPForoConf}
            setSnackbar={setSnackbar}
          />
        ))}
        {wpforoConf?.selectedTask && <div>
          <div className="txt-center btcbi-field-map-button mt-2"><button onClick={() => addFieldMap(wpforoConf.field_map.length, wpforoConf, setWPForoConf, false)} className="icn-btn sh-sm" type="button">+</button></div>
          <br />
          <br />
          <div className="mt-4"><b className="wdt-100">{__('Actions', 'bit-integrations')}</b></div>
          <div className="btcd-hr mt-1" />
          <WPForoActions
            wpforoConf={wpforoConf}
            setWPForoConf={setWPForoConf}
            loading={loading}
            setLoading={setLoading}
          />
        </div>}
      </div>
    </>
  )
}
