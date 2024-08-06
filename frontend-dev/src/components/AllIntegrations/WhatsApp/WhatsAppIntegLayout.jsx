import { create } from 'mutative'
import MultiSelect from 'react-multiple-select-dropdown-lite'
import { useParams } from 'react-router-dom'
import { __ } from '../../../Utils/i18nwrap'
import Loader from '../../Loaders/Loader'
import TinyMCE from '../../Utilities/TinyMCE'
import { getallTemplates } from './WhatsAppCommonFunc'
import WhatsAppFieldMap from './WhatsAppFieldMap'
import Note from '../../Utilities/Note'
import { useRecoilValue } from 'recoil'
import { $btcbi } from '../../../GlobalStates'

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
  const btcbi = useRecoilValue($btcbi)
  const { isPro } = btcbi

  const setMessageBody = (val) => {
    const newConf = { ...whatsAppConf }
    newConf.body = val
    setWhatsAppConf(newConf)
  }

  const setChange = (value, name) => {
    setWhatsAppConf((prevConf) =>
      create(prevConf, (draftConf) => {
        draftConf[name] = value

        if (name === 'messageType' && value === 'text') {
          draftConf['taskNote'] = textMsgNote
        } else if (name === 'messageType' && value === 'media') {
          draftConf['taskNote'] = textMediaNote
          draftConf['media_field_map'] = [{ formField: '', whatsAppFormField: '' }]
          draftConf['media_fields'] = [
            { key: 'caption', label: 'Caption', required: false },
            { key: 'filename', label: 'FileName', required: false }
          ]
        } else if (name === 'messageType') {
          delete draftConf?.taskNote
        }
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
        <MultiSelect
          defaultValue={whatsAppConf?.messageType}
          className="mt-2 w-5"
          onChange={(val) => setChange(val, 'messageType')}
          options={whatsAppConf?.messageTypes?.map((messageType) => ({
            label:
              isPro || (isPro && messageType.is_pro)
                ? messageType.label
                : `${messageType.label} (Pro)`,
            value: messageType.name,
            disabled: isPro || (isPro && messageType.is_pro) ? false : true
          }))}
          singleSelect
          closeOnSelect
        />
      </div>
      {whatsAppConf.messageType === 'template' && whatsAppConf?.allTemplates && (
        <>
          <br />
          <div className="flx">
            <b className="wdt-200 d-in-b">{__('Select Template:', 'bit-integrations')}</b>
            <MultiSelect
              defaultValue={whatsAppConf?.templateName}
              className="mt-2 w-5"
              onChange={(val) => setChange(val, 'templateName')}
              options={whatsAppConf?.allTemplates?.map((templateName) => ({
                label: templateName,
                value: templateName
              }))}
              singleSelect
              closeOnSelect
            />
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

      {whatsAppConf.messageType === 'media' && isPro && (
        <>
          <br />
          <div className="flx">
            <b className="wdt-200 d-in-b">{__('Select upload fields:', 'bit-integrations')}</b>
            <MultiSelect
              defaultValue={whatsAppConf?.upload_field}
              className="mt-2 w-5"
              onChange={(val) => setChange(val, 'upload_field')}
              options={formFields
                .filter((itm) => itm.type === 'file')
                .map((itm) => ({ label: itm.label, value: itm.name }))}
              singleSelect
              closeOnSelect
            />
          </div>
          <div className="flx">
            <b className="wdt-200 d-in-b">{__('Select Type of media:', 'bit-integrations')}</b>
            <MultiSelect
              defaultValue={whatsAppConf?.mediaType}
              className="mt-2 w-5"
              onChange={(val) => setChange(val, 'mediaType')}
              options={whatsAppConf?.mediaTypes?.map((type) => ({ label: type, value: type }))}
              singleSelect
              closeOnSelect
            />
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
          whatsAppFields={whatsAppConf.whatsAppFields}
          formFields={formFields}
          setWhatsAppConf={setWhatsAppConf}
          setSnackbar={setSnackbar}
        />
      ))}

      {whatsAppConf.messageType === 'media' && isPro && whatsAppConf?.media_fields && (
        <>
          <br />
          <div className="mt-5">
            <b className="wdt-100">{__('Media Field Map', 'bit-integrations')}</b>
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

          {whatsAppConf?.media_field_map?.map((itm, i) => (
            <WhatsAppFieldMap
              key={`rp-m-${i + 9}`}
              i={i}
              field={itm}
              whatsAppConf={whatsAppConf}
              whatsAppFields={whatsAppConf.media_fields}
              formFields={formFields}
              setWhatsAppConf={setWhatsAppConf}
              setSnackbar={setSnackbar}
              mapKey="media_field_map"
            />
          ))}
        </>
      )}

      {whatsAppConf?.messageType === 'text' && isPro && (
        <div>
          <b className="wdt-200 d-in-b mr-16 mb-4 mt-4">{__('Message: ', 'bit-integrations')}</b>
          <TinyMCE
            formFields={formFields}
            id={`whatsapp-message-${id}`}
            value={whatsAppConf.body}
            onChangeHandler={setMessageBody}
            width="100%"
            toolbarMnu="bold italic underline strikethrough | link | code | addFormField | toggleCode"
          />
        </div>
      )}
      {whatsAppConf?.taskNote && <Note note={whatsAppConf?.taskNote} />}
    </>
  )
}

const textMsgNote = `<p>To ensure successful message delivery using the WhatsApp Business API:</p>
            <ul>
                <li><strong>The conversation must be initiated by the user.</strong></li>
                <li>To begin, <strong>send a message from your WhatsApp number to the recipient's number.</strong></li>
                <li>Once the user has started the conversation, you can continue to communicate with the recipient normally.</li>
            </ul>`

const textMediaNote = `<h5>Caption</h5>
            <ul>
                <li>Do not use with <strong>audio</strong> or <strong>sticker</strong> media.</li>
                <li>Media asset <strong>caption</strong>.</li>
                <li><strong>Captions</strong> are currently not supported for <strong>document</strong> media.</li>
            </ul>
            <h5>FileName</h5>
            <ul>
                <li>Use only with <strong>document media</strong>.</li>
                <li>Describes the <strong>FileName</strong> for the specific <strong>document</strong>.</li>
                <li>The extension of the filename will specify what format the document is displayed as in WhatsApp.</li>
            </ul>`
