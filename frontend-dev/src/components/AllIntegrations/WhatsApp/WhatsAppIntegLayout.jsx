import { create } from 'mutative'
import MultiSelect from 'react-multiple-select-dropdown-lite'
import { useParams } from 'react-router-dom'
import { __ } from '../../../Utils/i18nwrap'
import Loader from '../../Loaders/Loader'
import TinyMCE from '../../Utilities/TinyMCE'
import { getallTemplates } from './WhatsAppCommonFunc'
import WhatsAppFieldMap from './WhatsAppFieldMap'

export default function WhatsAppIntegLayout({
  formFields,
  handleInput,
  whatsAppConf,
  setWhatsAppConf,
  isLoading,
  setIsLoading,
  setSnackbar
}) {
  const { id } = useParams()

  const setMessageBody = (val) => {
    const newConf = { ...whatsAppConf }
    newConf.body = val
    setWhatsAppConf(newConf)
  }

  const setChange = (value, name) => {
    setWhatsAppConf((prevConf) =>
      create(prevConf, (draftConf) => {
        draftConf[name] = value
      })
    )

    if (name === 'messageType' && value === 'template') {
      getallTemplates(whatsAppConf, setWhatsAppConf, setIsLoading, setSnackbar)
    }
  }

  return (
    <>
      <br />
      <div className="flx">
        <b className="wdt-200 d-in-b">{__('Message Type: ', 'bit-integrations')}</b>
        <select
          onChange={(e) => setChange(e.target.value, 'messageType')}
          name="messageType"
          value={whatsAppConf?.messageType}
          className="btcd-paper-inp w-5">
          <option value="">{__('Select Message Type', 'bit-integrations')}</option>
          {whatsAppConf?.messageTypes.map(({ name, label }) => (
            <option key={name} value={name}>
              {label}
            </option>
          ))}
        </select>
      </div>
      {whatsAppConf.messageType === 'template' && whatsAppConf?.allTemplates && (
        <>
          <br />
          <div className="flx">
            <b className="wdt-200 d-in-b">{__('Select Template:', 'bit-integrations')}</b>
            <select
              onChange={(e) => setChange(e.target.value, 'templateName')}
              name="templateName"
              value={whatsAppConf?.templateName}
              className="btcd-paper-inp w-5">
              <option value="">{__('Select Template', 'bit-integrations')}</option>
              {whatsAppConf?.allTemplates.map((templateName, key) => (
                <option key={key} value={templateName}>
                  {templateName}
                </option>
              ))}
            </select>
            <button
              onClick={() =>
                getallTemplates(whatsAppConf, setWhatsAppConf, setIsLoading, setSnackbar)
              }
              className="icn-btn sh-sm ml-2 mr-2 tooltip"
              style={{ '--tooltip-txt': `'${__('Refresh Template', 'bit-integrations')}'` }}
              type="button"
              disabled={isLoading}>
              &#x21BB;
            </button>
          </div>
        </>
      )}

      {isLoading && (
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
      <br />
      <div className="mt-5">
        <b className="wdt-100">{__('Field Map', 'bit-integrations')}</b>
      </div>
      <div className="btcd-hr mt-1" />
      <div className="flx flx-around mt-2 mb-2 btcbi-field-map-label">
        <div className="txt-dp">
          <b>{__('Form Fields', 'bit-integrations')}</b>
        </div>
        <div className="txt-dp">
          <b>{__('WhatsApp Fields', 'bit-integrations')}</b>
        </div>
      </div>
      <br />

      {whatsAppConf?.field_map.map((itm, i) => (
        <WhatsAppFieldMap
          key={`rp-m-${i + 9}`}
          i={i}
          field={itm}
          whatsAppConf={whatsAppConf}
          formFields={formFields}
          setWhatsAppConf={setWhatsAppConf}
          setSnackbar={setSnackbar}
        />
      ))}
      {whatsAppConf?.messageTypeId === '2' && (
        <div>
          <b className="wdt-200 d-in-b mr-16 mb-4 mt-4">{__('Messages: ', 'bit-integrations')}</b>
          {/* <textarea
              className="w-7"
              onChange={handleInput}
              name="body"
              rows="5"
              value={whatsAppConf.body}
            />
            <MultiSelect
              options={formFields
                .filter((f) => f.type !== 'file')
                .map((f) => ({ label: f.label, value: `\${${f.name}}` }))}
              className="btcd-paper-drpdwn wdt-600 ml-2"
              onChange={(val) => setMessageBody(val)}
            /> */}

          <TinyMCE
            formFields={formFields}
            id={`telegram-message-${id}`}
            value={whatsAppConf.body}
            onChangeHandler={setMessageBody}
            width="100%"
            toolbarMnu="bold italic underline strikethrough | link | code | addFormField | toggleCode"
          />
        </div>
      )}
      <br />
      <br />
      {whatsAppConf?.messageTypeId === '2' && (
        <div className="flx mt-4">
          <b className="wdt-200 d-in-b">{__('Template Name: ', 'bit-integrations')}</b>
          <textarea
            className="w-5"
            onChange={handleInput}
            name="templateName"
            rows="5"
            value={whatsAppConf.templateName}
          />
        </div>
      )}
    </>
  )
}
