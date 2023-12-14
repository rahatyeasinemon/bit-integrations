/* eslint-disable jsx-a11y/anchor-is-valid */
/* eslint-disable no-unused-expressions */
import { useState } from 'react'
import { __ } from '../../../Utils/i18nwrap'
import LoaderSm from '../../Loaders/LoaderSm'
import Note from '../../Utilities/Note'
import { systemIOAuthentication, getAllTags } from './SystemIOCommonFunc'
import tutorialLinks from '../../../Utils/StaticData/tutorialLinks'
import TutorialLink from '../../Utilities/TutorialLink'

export default function SystemIOAuthorization({ systemIOConf, setSystemIOConf, step, setStep, loading, setLoading, isInfo }) {
  const [isAuthorized, setIsAuthorized] = useState(false)
  const { systemIO } = tutorialLinks
  const [error, setError] = useState({ api_key: '' })

  const nextPage = () => {
    setTimeout(() => {
      document.getElementById('btcd-settings-wrp').scrollTop = 0
    }, 300)

    !systemIOConf?.default
    setStep(2)
    getAllTags(systemIOConf, setSystemIOConf, setLoading)
  }

  const handleInput = e => {
    const newConf = { ...systemIOConf }
    const rmError = { ...error }
    rmError[e.target.name] = ''
    newConf[e.target.name] = e.target.value
    setError(rmError)
    setSystemIOConf(newConf)
  }

  const ActiveInstructions = `
            <h4>To Get API Key & API Secret</h4>
            <ul>
                <li>First go to your SystemIO dashboard.</li>
                <li>Click go to "Settings" from Right Top corner</li>
                <li>Then Click "API" from the "Settings Menu"</li>
                <li>Then Click "Generate Api Secret"</li>
                <li>Then copy "API Authorization Credentials"</li>
            </ul>`

  return (
    <div className="btcd-stp-page" style={{ ...{ width: step === 1 && 900 }, ...{ height: step === 1 && 'auto' } }}>
      {systemIO?.youTubeLink && (
        <TutorialLink
          title={systemIO?.title}
          youTubeLink={systemIO?.youTubeLink}
        />
      )}
      {systemIO?.docLink && (
        <TutorialLink
          title={systemIO?.title}
          docLink={systemIO?.docLink}
        />
      )}

      <div className="mt-3"><b>{__('Integration Name:', 'bit-integrations')}</b></div>
      <input className="btcd-paper-inp w-6 mt-1" onChange={handleInput} name="name" value={systemIOConf.name} type="text" placeholder={__('Integration Name...', 'bit-integrations')} disabled={isInfo} />

      <div className="mt-3"><b>{__('API Key:', 'bit-integrations')}</b></div>
      <input className="btcd-paper-inp w-6 mt-1" onChange={handleInput} name="api_key" value={systemIOConf.api_key} type="text" placeholder={__('API Key...', 'bit-integrations')} disabled={isInfo} />
      <div style={{ color: 'red', fontSize: '15px' }}>{error.api_key}</div>

      <small className="d-blk mt-3">
        {__('To Get API Key & API Secret, Please Visit', 'bit-integrations')}
        &nbsp;
        <a className="btcd-link" href='https://my.systemIO.com/manage/settings/api-details' target='_blank'>{__('SystemIO API Key & Secret', 'bit-integrations')}</a>
      </small>
      <br />
      <br />

      {!isInfo && (
        <div>
          <button onClick={() => systemIOAuthentication(systemIOConf, setSystemIOConf, setError, setIsAuthorized, loading, setLoading)} className="btn btcd-btn-lg green sh-sm flx" type="button" disabled={isAuthorized || loading.auth}>
            {isAuthorized ? __('Authorized ✔', 'bit-integrations') : __('Authorize', 'bit-integrations')}
            {loading.auth && <LoaderSm size="20" clr="#022217" className="ml-2" />}
          </button>
          <br />
          <button onClick={nextPage} className="btn ml-auto btcd-btn-lg green sh-sm flx" type="button" disabled={!isAuthorized}>
            {__('Next', 'bit-integrations')}
            <div className="btcd-icn icn-arrow_back rev-icn d-in-b" />
          </button>
        </div>
      )}
      <Note note={ActiveInstructions} />
    </div>
  )
}

