
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
    const version = btcbi.isPro ? '1.4.4' : '1.6.2'
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
                        <small className='date'> <b>19th March 2023</b></small>
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
                                <li>Brekdance: UI & functionality upgraded</li>
                                <li>Klaviyo: API version upgraded</li>
                            </ul>
                        </div>

                        <span className='new-feature'><b>New Features</b></span>

                        <div className='feature-list'>
                            <ul>
                                <li>WooCommerce: Order Attribution Tracking fields</li>
                                <li>PerfexCRM: lead module: status & source fields</li>
                                <li>Zoom: Generall fields & custom question's</li>
                                <li>OmniSend: Tag action</li>
                                <li>Salesforce: account & Case module:  missing fields</li>

                            </ul>
                        </div>

                        <span className='fixes'><b>Fixed</b></span>

                        <div className='fixes-list'>
                            <ul>
                                <li>Mailjet: Contact list limitation</li>
                                <li>Keap: data submit</li>
                                <li>Clickup: phone number</li>
                                <li>Zoom: access token expired</li>
                                <li>Zoho Bigin: note content</li>
                                <li>WPForms: file attachment path</li>
                                <li>MailChimp: custom fields</li>
                                <li>Custom Action: function file location missing</li>
                                <li>FreshSales: Deal module: custom field</li>
                                <li>WooCommerce: order-create module: checkout handling</li>
                                <li>Freshsales: integration name update</li>
                                <li>Telegram: file attachment upload</li>
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