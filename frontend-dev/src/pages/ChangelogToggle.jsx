
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
    const version = btcbi.isPro ? '2.0.0' : '2.0.0'
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
                        <small className='date'> <b>25th April 2024</b></small>
                    </div>
                    <div className='changelog-content'>
                        {/* <h4>New Integration</h4> */}
                        {/* <p>New Integration</p> */}
                        {/* <span className='new-integration' style={{ background: "cornflowerblue" }}><b>New Triggers</b></span>

                        <div className='integration-list'>
                            <ul>
                                <li>Mailpoet</li>
                            </ul>
                        </div> */}

                        <span className='new-feature'><b>Compatibility Update:</b></span>

                        <div className='feature-list'>
                            <ul>
                                <li><b>Existing Integrations:</b> Users who have created integrations using triggers other than those listed above in the previous version of the plugin need not worry. This update will not affect their existing integrations, and they will continue to run properly. However, they will be unable to edit these integrations unless they are using one of the newly added triggers.</li>
                            </ul>
                        </div>
                        <span className='new-integration' style={{ background: "cornflowerblue" }}><b>New Improvements</b></span>

                        <div className='integration-list'>
                            <ul>
                                <li> <b> Expanded Integration Options:</b> In compliance with WordPress guidelines, we have updated the plugin to offer unlimited integration possibilities. Previously, users were limited to creating only one integration, but now they can create unlimited integrations using a wide range of triggers.</li>
                                <li> <b> New Trigger Options:</b> Users can now create integrations using the following triggers:
                                    <ul style={{ listStyleType: "disc", margin: "20px" }}>
                                        <li>WooCommerce</li>
                                        <li>Elementor</li>
                                        <li>Contact Form 7</li>
                                        <li>Bit Form</li>
                                        <li>WP Forms</li>
                                        <li>Webhook</li>
                                        <li>Custom Trigger</li>
                                        <li>Actionhook</li>
                                    </ul>
                                </li>
                            </ul>
                        </div>


                        {/* <span className='fixes'><b>Fixed</b></span>

                        <div className='fixes-list'>
                            <ul>
                                <li>MailChimp: Field Mapping visibility </li>
                            </ul>
                        </div> */}
                    </div>
                    <div>
                        <span className='footer'>{__('For more details,')}</span>
                        <a href="https://bitapps.pro/docs/bit-integrations/free-changelogs/" target="_blank" rel="noreferrer">
                            {__('Click here ')}
                            <ExternalLinkIcn size="14" />
                        </a>
                    </div>
                </div >
            </Modal >
        </div >
    )
}