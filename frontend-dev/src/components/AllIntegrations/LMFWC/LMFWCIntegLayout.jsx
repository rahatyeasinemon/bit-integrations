/* eslint-disable no-unused-vars */
import MultiSelect from 'react-multiple-select-dropdown-lite'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import { __ } from '../../../Utils/i18nwrap'
import Loader from '../../Loaders/Loader'
import { generateMappedField, getAllEvents, getAllSessions } from './LMFWCCommonFunc'
import LMFWCFieldMap from './LMFWCFieldMap'
import { addFieldMap } from './IntegrationHelpers'
import { useRecoilValue } from 'recoil'
import { $btcbi } from '../../../GlobalStates'
import { checkIsPro, getProLabel } from '../../Utilities/ProUtilHelpers'
import { create } from 'mutative'
import Note from '../../Utilities/Note'

export default function LMFWCIntegLayout({
  formFields,
  licenseManagerConf,
  setLicenseManagerConf,
  loading,
  setLoading,
  isLoading,
  setIsLoading,
  setSnackbar
}) {
  const btcbi = useRecoilValue($btcbi)
  const { isPro } = btcbi

  const setChanges = (val, name) => {
    setLicenseManagerConf(prevConf => create(prevConf, draftConf => {
      draftConf[name] = val

      if (name === 'module' && val === 'create_license') {
        draftConf.lmfwcFields = draftConf.licenseFields
        draftConf.field_map = generateMappedField(draftConf.licenseFields)

        draftConf.module_note = `<p><b>${__('Note', 'bit-integrations')}</b>: ${__('You can also use Valid for (the number of days) instead of Expires at', 'bit-integrations')}, <b>${__('please do not use both at a time', 'bit-integrations')}</b></p>`
      }
    }))
  }


  return (
    <>
      <br />
      <div className="flx">
        <b className="wdt-200 d-in-b">{__('Select Action:', 'bit-integrations')}</b>
        <MultiSelect
          title={"Action"}
          defaultValue={licenseManagerConf?.module}
          className="mt-2 w-5"
          onChange={(val) => setChanges(val, 'module')}
          options={licenseManagerConf?.modules?.map((action) => ({
            label: checkIsPro(isPro, action.is_pro)
              ? action.label
              : getProLabel(action.label),
            value: action.name,
            disabled: checkIsPro(isPro, action.is_pro) ? false : true
          }))}
          singleSelect
          closeOnSelect
        />
      </div>

      {(isLoading || loading.event || loading.session) && (
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

      {licenseManagerConf?.module && licenseManagerConf.module === "create_license" && !loading.event && (
        <>
          <br />
          <br />
          <div className="flx">
            <b className="wdt-200 d-in-b">{__('Select Status:', 'bit-integrations')}</b>
            <MultiSelect
              options={
                [
                  { id: 'sold', name: 'Sold' },
                  { id: 'delivered', name: 'Delivered' },
                  { id: 'active', name: 'Active' },
                  { id: 'inactive', name: 'Inactive' }
                ].map((event) => ({ label: event.name, value: `${event.id}` }))
              }
              className="msl-wrp-options dropdown-custom-width"
              defaultValue={licenseManagerConf?.selectedStatus}
              onChange={(val) => setChanges(val, 'selectedStatus')}
              singleSelect
              closeOnSelect
            />
          </div>
        </>
      )}

      {/* {licenseManagerConf.module && licenseManagerConf.selectedEvent && !loading.session && (
        <>
          <br />
          <br />
          <div className="flx">
            <b className="wdt-200 d-in-b">{__('Select Session:', 'bit-integrations')}</b>
            <MultiSelect
              options={
                licenseManagerConf?.sessions &&
                licenseManagerConf.sessions.map((session) => ({
                  label: session.datetime,
                  value: `${session.date_id}`
                }))
              }
              className="msl-wrp-options dropdown-custom-width"
              defaultValue={licenseManagerConf?.selectedSession}
              onChange={(val) => setChanges(val, 'selectedSession')}
              singleSelect
              closeOnSelect
            />
            <button
              onClick={() => getAllSessions(licenseManagerConf, setLicenseManagerConf, setLoading)}
              className="icn-btn sh-sm ml-2 mr-2 tooltip"
              style={{ '--tooltip-txt': `'${__('Refresh Sessions', 'bit-integrations')}'` }}
              type="button"
              disabled={loading.event}>
              &#x21BB;
            </button>
          </div>
        </>
      )} */}
      {licenseManagerConf.module && !isLoading && (
        <div>
          <br />
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
              <b>{__('LMFWC Fields', 'bit-integrations')}</b>
            </div>
          </div>

          {licenseManagerConf.field_map.map((itm, i) => (
            <LMFWCFieldMap
              key={`rp-m-${i + 9}`}
              i={i}
              field={itm}
              licenseManagerConf={licenseManagerConf}
              formFields={formFields}
              setLicenseManagerConf={setLicenseManagerConf}
              setSnackbar={setSnackbar}
            />
          ))}
          <div className="txt-center btcbi-field-map-button mt-2">
            <button
              onClick={() =>
                addFieldMap(licenseManagerConf.field_map.length, licenseManagerConf, setLicenseManagerConf, false)
              }
              className="icn-btn sh-sm"
              type="button">
              +
            </button>
          </div>
          <br />
          <br />
          {licenseManagerConf?.module_note && <Note note={licenseManagerConf?.module_note} />}
        </div>
      )}
    </>
  )
}