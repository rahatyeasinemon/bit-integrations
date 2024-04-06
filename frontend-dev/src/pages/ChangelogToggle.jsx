
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
    const version = btcbi.isPro ? '1.4.4' : '1.6.3'
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
                        <small className='date'> <b>6th April 2024</b></small>
                    </div>
                    <div className='changelog-content'>
                        {/* <h4>New Integration</h4> */}
                        {/* <p>New Integration</p> */}
                        {/* <span className='new-integration' style={{ background: "cornflowerblue" }}><b>New Triggers</b></span>

                        <div className='integration-list'>
                            <ul>
                                <li>Spectra</li>
                                <li>Essential Blocks</li>
                            </ul>
                        </div> */}

                        <span className='new-integration' style={{ background: "cornflowerblue" }}><b>New Improvements</b></span>

                        <div className='integration-list'>
                            <ul>
                                <li>Elementor: UI & functionality upgraded</li>
                            </ul>
                        </div>

                        <span className='new-feature'><b>New Features</b></span>

                        <div className='feature-list'>
                            <ul>
                                <li>Keap: Custom Fields Added</li>
                            </ul>
                        </div>

                        <span className='fixes'><b>Fixed</b></span>

                        <div className='fixes-list'>
                            <ul>
                                <li>Mailup: Lists & Groups limitation</li>
                                <li>Sendinblue (Brevo): Lists limitation</li>
                                <li>SendGrid: Authorization error response</li>
                                <li>ConvertKit: Existing subscriber</li>
                                <li>MailChimp: Required field missing</li>
                                <li>Divi: Theme active check</li>
                                <li>Freshsales: Contact Module: Account</li>
                                <li>SendFox: Integration name edit/update</li>
                                <li>FluentCRM: Tag added Contact Module: tag json format</li>
                                <li>FluentCRM: Dependency form list update</li>
                                <li>eForm: Single dropdown array</li>
                                <li>Zagomail: Error response</li>
                                <li>Keap: Refresh token expired</li>
                                <li>ActionHook: Remove test option hook</li>
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