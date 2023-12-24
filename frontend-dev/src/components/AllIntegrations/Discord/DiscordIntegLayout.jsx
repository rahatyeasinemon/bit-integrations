import MultiSelect from 'react-multiple-select-dropdown-lite'
import { useParams } from 'react-router-dom'
import { __ } from '../../../Utils/i18nwrap'
import CheckBox from '../../Utilities/CheckBox'
import Loader from '../../Loaders/Loader'
import TinyMCE from '../../Utilities/TinyMCE'
import DiscordActions from './DiscordActions'

export default function DiscordIntegLayout({ formFields,
  discordConf,
  setDiscordConf,
  isLoading }) {
  const { id } = useParams()
  const handleInput = (e) => {
    const newConf = { ...discordConf }
    newConf[e.target.name] = e.target.value
    setDiscordConf(newConf)
  }

  const setMessageBody = (val) => {
    const newConf = { ...discordConf }
    newConf.body = val
    setDiscordConf(newConf)
  }
  const changeActionRun = (e) => {
    const newConf = { ...discordConf }
    if (newConf?.body) {
      newConf.body = ''
    }
    newConf.parse_mode = e.target.value
    setDiscordConf(newConf)
  }

  return (
    <>
      <br />
      <div className="flx">
        <b className="wdt-200 d-in-b">
          {__('Servers List: ', 'bit-integrations')}
        </b>
        <select
          onChange={handleInput}
          name="server_id"
          value={discordConf.server_id}
          className="btcd-paper-inp w-5"
        >
          <option value="">
            {__('Select Server List', 'bit-integrations')}
          </option>
          {
            discordConf?.tokenDetails?.servers
            && discordConf?.tokenDetails?.servers.map(({ id, name }) => (
              <option key={id} value={id}>
                {name}
              </option>
            ))
          }
        </select>
      </div>
      <br />
      <div className="flx">
        <b className="wdt-200 d-in-b">
          {__('Channels List: ', 'bit-integrations')}
        </b>
        <select
          onChange={handleInput}
          name="channel_id"
          value={discordConf.channel_id}
          className="btcd-paper-inp w-5"
        >
          <option value="">
            {__('Select Channel List', 'bit-integrations')}
          </option>
          {
            discordConf?.tokenDetails?.channels
            && discordConf?.tokenDetails?.channels.map(({ id, name }) => (
              <option key={id} value={id}>
                {name}
              </option>
            ))
          }
        </select>
      </div>
      {isLoading && (
        <Loader
          style={{
            display: 'flex',
            justifyContent: 'center',
            alignItems: 'center',
            height: 100,
            transform: 'scale(0.7)',
          }}
        />
      )}
      {discordConf?.channel_id && (
        <>
          <div className="flx mt-4">
            <b className="wdt-200 d-in-b mr-16">
              {__('Messages: ', 'bit-integrations')}
            </b>
            <textarea
              className="w-7"
              onChange={handleInput}
              name="body"
              rows="5"
              value={discordConf.body}
            />
            <MultiSelect
              options={formFields
                .filter((f) => f.type !== 'file')
                .map((f) => ({ label: f.label, value: `\${${f.name}}` }))}
              className="btcd-paper-drpdwn wdt-600 ml-2"
              onChange={(val) => setMessageBody(val)}
            />
          </div>
          <div className="mt-4">
            <b className="wdt-100">{__('Actions', 'bit-integrations')}</b>
          </div>
          <div className="btcd-hr mt-1" />
          <DiscordActions
            discordConf={discordConf}
            setDiscordConf={setDiscordConf}
            formFields={formFields}
          />
        </>
      )}
    </>
  )
}
