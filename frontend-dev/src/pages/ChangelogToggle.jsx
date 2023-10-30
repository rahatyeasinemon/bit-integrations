
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
    const version = btcbi.isPro ? '1.4.2' : '1.5.2'
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
                        <small>30th October 2023</small>
                    </div>
                    <div className='changelog-content'>
                        {/* <h4>New Integration</h4>
                        <p>New Integration</p> */}
                        {/* <span className='new-integration' style={{ background: "cornflowerblue" }}><b>New Triggers</b></span>

                        <div className='integration-list'>
                            <ul>
                                <li>BitForm</li>
                                <li>Academy LMS</li>
                            </ul>
                        </div>

                        <span className='new-integration'><b>New Integrations</b></span>

                        <div className='integration-list'>
                            <ul>
                                <li>Academy LMS</li>
                                <li>Moxie CRM</li>
                                <li>Woodpecker</li>
                                <li>WP Fusion</li>
                            </ul>
                        </div> */}

                        <span className='fixes'><b>Fixed</b></span>

                        <div className='fixes-list'>
                            <ul>
                                <li>Acumbamail data submission issue fixed</li>
                                <li>Elementor global form data submission issue fixed</li>
                                <li>Forminator forms limitation issue fixed</li>
                                <li>Google Sheets custom form-field issue fixed</li>
                                <li>Post trigger feature image issue fixed</li>
                                <li>Beaver Builder custom dashboard issue fixed</li>
                                <li>Doc & support page product design breaking issue fixed</li>
                                <li>Custom Action validation issue fixed</li>

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