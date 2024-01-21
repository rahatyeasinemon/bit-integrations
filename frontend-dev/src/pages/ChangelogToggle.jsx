
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
    const version = btcbi.isPro ? '1.4.3' : '1.5.9'
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
                        <small>21 January 2023</small>
                    </div>
                    <div className='changelog-content'>
                        {/* <h4>New Integration</h4> */}
                        {/* <p>New Integration</p> */}
                        <span className='new-integration' style={{ background: "cornflowerblue" }}><b>New Triggers</b></span>

                        <div className='integration-list'>
                            <ul>
                                <li>Action Hook</li>
                            </ul>
                        </div>

                        {/* <span className='new-integration'><b>New Actions</b></span>

                        <div className='integration-list'>
                            <ul>
                                <li>Zagomail</li>
                            </ul>
                        </div> */}

                        <span className='new-feature'><b>New Features</b></span>

                        <div className='feature-list'>
                            <ul>
                                <li>MailMint: Contact Fields</li>
                                <li>Airtable: Attachment field</li>
                            </ul>
                        </div>

                        <span className='fixes'><b>Fixed</b></span>

                        <div className='fixes-list'>
                            <ul>
                                <li>Zoom webinar: list fetch issue fixed</li>
                                <li>Integration Timeline: Log pagination</li>
                                <li>Mautic: Authorization</li>
                                <li>MailMint: contact status & select field mapping</li>
                                <li>ARMember: edit integration form fields</li>
                                <li>ZohoBigin: CheckMapped field</li>
                                <li>Forminator: year field submit</li>
                                <li>FreshSales: authorization & integration</li>
                                <li>WooCommerce: order-create: product acf field </li>
                                <li>Google Drive: single folder multiple file upload</li>
                                <li>Goggle Sheet: checkbox array data</li>
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