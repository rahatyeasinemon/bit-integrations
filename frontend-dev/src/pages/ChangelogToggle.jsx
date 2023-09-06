
import { useState } from 'react'
import { useRecoilValue } from 'recoil'
import { $btcbi } from '../GlobalStates'
import ChangelogIcn from '../Icons/ChangeLogIcn'
import ExternalLinkIcn from '../Icons/ExternalLinkIcn'
import { __ } from '../Utils/i18nwrap'
import Modal from '../components/Utilities/Modal'

export default function ChangelogToggle() {
    const btcbi = useRecoilValue($btcbi)
    const [show, setShow] = useState(btcbi.changelogVersion !== btcbi.version)

    return (
        <div className="changelog-toggle">
            <button
                title={__('What\'s New')}
                type="button"
                className="changelog-btn"
                onClick={() => setShow(true)}
            >
                {/* <QuestionIcn size={25} /> */}
                <ChangelogIcn size={25} />
            </button>
            <Modal sm show={show} setModal={setShow} >
                <div className='changelog'>
                    <h4 className='changelog-notif'> From 1.4.1 update,To use pro plugin free version is required. </h4>
                    <div className="flx flx-col flx-center whats-new">
                        <h3>{__(`What\'s New in ${btcbi.isPro ? '(1.4.1)' : '(1.4.3)'}?`)}</h3>
                        <small>06 September 2023</small>
                    </div>
                    <div className='changelog-content'>
                        {/* <h4>New Integration</h4> */}
                        {/* <p>New Integration</p> */}
                        {/* <span className='new-integration'><b>New Integrations</b></span> */}

                        {/* <div className='integration-list'>
                            <ul>
                                <li>Perfex CRM</li>
                                <li>OneHash CRM</li>
                                <li>Salesflare CRM</li>
                            </ul>
                        </div> */}

                        <span className='fixes'><b>Fixed</b></span>

                        <div className='fixes-list'>
                            <ul>
                                <li>FluentSupport business inbox issue fixed</li>
                                <li>Brevo(Sendinblue) integration edit & data submit issue fixed</li>
                                <li>Make(Integromat) integration edit & data submit issue fixed</li>
                                <li>Sarbacane(Mailify) integration edit & data submit issue fixed</li>
                                <li>Elementor fields fetching issue fixed</li>
                                <li>ZohoCRM pro modules & sub modules fetching issue fixed</li>
                                <li>Custom field blank screen issue fixed</li>
                            </ul>
                        </div>
                    </div>
                    <div>
                        <span className='footer'>{__('For more details,')}</span>
                        <a href="https://bitapps.pro/docs/bit-integrations/free-changelogs/" target="_blank" rel="noreferrer">
                            {__('Click here ')}
                            <ExternalLinkIcn size="14" />
                        </a>
                    </div>
                </div>
            </Modal >
        </div >
    )
}