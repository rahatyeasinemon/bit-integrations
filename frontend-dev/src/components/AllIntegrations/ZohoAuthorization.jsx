import { useState } from 'react'
import { useRecoilValue } from 'recoil'
import { $btcbi } from '../../GlobalStates'
import { __ } from '../../Utils/i18nwrap'
import LoaderSm from '../Loaders/LoaderSm'
import CopyText from '../Utilities/CopyText'
import TutorialLink from '../Utilities/TutorialLink'
import { handleAuthorize } from './IntegrationHelpers/IntegrationHelpers'


export default function ZohoAuthorization({ integ, tutorialLink, scopes, nextPage, formID, crmConf, setCrmConf, step, setstep, isLoading, setIsLoading, setSnackbar, redirectLocation, isInfo }) {
    const [isAuthorized, setisAuthorized] = useState(false)
    const [error, setError] = useState({ dataCenter: '', clientId: '', clientSecret: '' })
    const btcbi = useRecoilValue($btcbi)

    const handleInput = e => {
        const newConf = { ...crmConf }
        const rmError = { ...error }
        rmError[e.target.name] = ''
        newConf[e.target.name] = e.target.value
        setError(rmError)
        setCrmConf(newConf)
    }

    return (
        <div className="btcd-stp-page" style={{ ...{ width: step === 1 && 900 }, ...{ height: step === 1 && 'auto' } }}>
            {tutorialLink?.youTubeLink && (
                <TutorialLink
                    title={tutorialLink?.title}
                    youTubeLink={tutorialLink?.youTubeLink}
                />
            )}
            {tutorialLink?.docLink && (
                <TutorialLink
                    title={tutorialLink?.title}
                    docLink={tutorialLink?.docLink}
                />
            )}

            <div className="mt-3"><b>{__('Integration Name:', 'bit-integrations')}</b></div>
            <input className="btcd-paper-inp w-6 mt-1" onChange={handleInput} name="name" value={crmConf.name} type="text" placeholder={__('Integration Name...', 'bit-integrations')} disabled={isInfo} />

            <div className="mt-3"><b>{__('Data Center:', 'bit-integrations')}</b></div>
            <select onChange={handleInput} name="dataCenter" value={crmConf.dataCenter} className="btcd-paper-inp w-6 mt-1" disabled={isInfo}>
                <option value="">{__('--Select a data center--', 'bit-integrations')}</option>
                <option value="zoho.com">zoho.com</option>
                <option value="zoho.eu">zoho.eu</option>
                <option value="zoho.com.cn">zoho.com.cn</option>
                <option value="zoho.in">zoho.in</option>
                <option value="zoho.com.au">zoho.com.au</option>
                <option value="zoho.jp">zoho.jp</option>
                <option value="zoho.uk">zoho.uk</option>
                <option value="zoho.sa">zoho.sa</option>
                <option value="zohocloud.ca">zohocloud.ca</option>
            </select>
            <div style={{ color: 'red' }}>{error.dataCenter}</div>

            <div className="mt-3"><b>{__('Homepage URL:', 'bit-integrations')}</b></div>
            <CopyText value={`${window.location.origin}`} setSnackbar={setSnackbar} className="field-key-cpy w-6 ml-0" readOnly={isInfo} />

            <div className="mt-3"><b>{__('Authorized Redirect URIs:', 'bit-integrations')}</b></div>
            <CopyText value={redirectLocation || `${btcbi.api.base}/redirect`} setSnackbar={setSnackbar} className="field-key-cpy w-6 ml-0" readOnly={isInfo} />

            <small className="d-blk mt-5">
                {__('To get Client ID and SECRET , Please Visit', 'bit-integrations')}
                {' '}
                <a className="btcd-link" href={`https://api-console.${crmConf?.dataCenter || 'zoho.com'}/`} target="_blank" rel="noreferrer">{__('Zoho API Console', 'bit-integrations')}</a>
            </small>

            <div className="mt-3"><b>{__('Client id:', 'bit-integrations')}</b></div>
            <input className="btcd-paper-inp w-6 mt-1" onChange={handleInput} name="clientId" value={crmConf.clientId} type="text" placeholder={__('Client id...', 'bit-integrations')} disabled={isInfo} />
            <div style={{ color: 'red' }}>{error.clientId}</div>

            <div className="mt-3"><b>{__('Client secret:', 'bit-integrations')}</b></div>
            <input className="btcd-paper-inp w-6 mt-1" onChange={handleInput} name="clientSecret" value={crmConf.clientSecret} type="text" placeholder={__('Client secret...', 'bit-integrations')} disabled={isInfo} />
            <div style={{ color: 'red' }}>{error.clientSecret}</div>

            {!isInfo && (
                <>
                    <button onClick={() => handleAuthorize(integ, scopes, crmConf, setCrmConf, setError, setisAuthorized, setIsLoading, setSnackbar, btcbi)} className="btn btcd-btn-lg green sh-sm flx" type="button" disabled={isAuthorized || isLoading}>
                        {isAuthorized ? __('Authorized ✔', 'bit-integrations') : __('Authorize', 'bit-integrations')}
                        {isLoading && <LoaderSm size="20" clr="#022217" className="ml-2" />}
                    </button>
                    <br />
                    <button onClick={() => nextPage(2)} className="btn f-right btcd-btn-lg green sh-sm flx" type="button" disabled={!isAuthorized}>
                        {__('Next', 'bit-integrations')}
                        <div className="btcd-icn icn-arrow_back rev-icn d-in-b" />
                    </button>
                </>
            )}
        </div>
    )
}
