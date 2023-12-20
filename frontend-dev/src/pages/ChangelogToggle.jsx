
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
    const version = btcbi.isPro ? '1.4.3' : '1.5.6'
    return (
        <div className="changelog-toggle">
            <button
                title={('What\'s New')}
                type="button"
                className="changelog-btn"
                onClick={() => setShow(true)}
            >
                {/* <QuestionIcn size={25} /> */}
                <ChangelogIcn size={25} />
            </button>
            <Modal sm show={show} setModal={setShow} >
                <div className='changelog'>
                    {/* <h4 className='changelog-notif'> From 1.4.1 update,To use pro plugin free version is required. </h4> */}
                    <div className="flx flx-col flx-center whats-new">
                        <h3>What's New in {version}?</h3>
                        <small>20th December 2023</small>
                    </div>
                    <div className='changelog-content'>
                        <h4>New Integration</h4>
                        {/* <p>New Integration</p> */}
                        {/* <span className='new-integration' style={{ background: "cornflowerblue" }}><b>New Triggers</b></span>

                        <div className='integration-list'>
                            <ul>
                                <li>BitForm</li>
                                <li>Academy LMS</li>
                            </ul>
                        </div> */}

                        <span className='new-integration'><b>New Actions</b></span>

                        <div className='integration-list'>
                            <ul>
                                <li>System.io</li>
                            </ul>
                        </div>

                        <span className='fixes'><b>Fixed</b></span>

                        <div className='fixes-list'>
                            <ul>
                                <li>SendFox lists limitation issue fixed</li>
                                <li>Rapidemail Double opt-in issue fixed</li>
                                <li>Formidable multiiple file upload issue fixed</li>
                                <li>PropovoiceCRM tags issue fixed</li>
                                <li>Affiliate blank page issue fixed</li>
                                <li>FluentCRM custom field issue fixed</li>
                                <li>Forminator multiple address issue fixed </li>
                                <li>Mailchimp update record issue fixed</li>
                                <li>Elementor integration issue fixed</li>
                                <li>Hubspot custom field issue fixed</li>
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