
import { useState } from 'react'
import { useRecoilState } from 'recoil'
import { $btcbi } from '../GlobalStates'
import ChangelogIcn from '../Icons/ChangeLogIcn'
import ExternalLinkIcn from '../Icons/ExternalLinkIcn'
import { __ } from '../Utils/i18nwrap'
import Modal from '../components/Utilities/Modal'
import changelogInfo from '../Utils/StaticData/changelogInfo'

import bitsFetch from '../Utils/bitsFetch'

export default function ChangelogToggle() {
    const [btcbi, setBtcbi] = useRecoilState($btcbi)
    const [show, setShow] = useState(btcbi.changelogVersion !== btcbi.version)
    const currentChangelog = '2.0.6'
    const currenChangelog = changelogInfo[currentChangelog]

    const setChangeLogVersion = (val) => {
        setShow(val)
        if (!val) {
            bitsFetch({
                version: btcbi.version,
            }, 'changelog_version')
                .then(() => {
                    setBtcbi(prevBtcbi => ({ ...prevBtcbi, changelogVersion: prevBtcbi.version }))
                })
        }
    }
    // if (!currenChangelog) return

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
            <Modal sm show={show} setModal={setChangeLogVersion}>
                <div className='changelog'>
                    {/* <h4 className='changelog-notif'> From 1.4.1 update,To use pro plugin free version is required. </h4> */}
                    <div className="flx flx-col flx-center whats-new">
                        <h3>What's New in {btcbi.version}?</h3>
                        <small className='date'> <b>1st June 2024</b></small>
                    </div>
                    <div className='changelog-content'>
                        <span className='new-integration' ><b>New Integration</b></span>

                        <div className='integration-list'>
                            <ul>
                                <li>CoBlocks (Trigger) </li>
                                <li>Drip (Action) </li>
                            </ul>
                        </div>

                        <span className='new-feature' ><b>New Features</b></span>

                        <div className='integration-list'>
                            <ul>
                                <li>Sendy: Custom Field: Key added </li>
                                <li>Autonami: Store new list & tag with existing record feature added </li>
                                <li>Salesforce: Custom Module added </li>
                            </ul>
                        </div>

                        {/* <span className='new-feature'><b>Compatibility Update:</b></span>

                        <div className='feature-list'>
                            <ul>
                                <li><b>Existing Integrations:</b> Users who have created integrations using triggers other than those listed above in the previous version of the plugin need not worry. This update will not affect their existing integrations, and they will continue to run properly. However, they will be unable to edit these integrations unless they are using one of the newly added triggers.</li>
                            </ul>
                        </div> */}
                        {/* <span className='new-integration' style={{ background: "cornflowerblue" }}><b>New Improvements</b></span>

                        <div className='integration-list'>
                            <ul>
                                <li>Contact Form 7: converted file path into file URL</li>

                            </ul>
                        </div> */}


                        <span className='fixes'><b>Fixed</b></span>

                        <div className='fixes-list'>
                            <ul>
                                <li>ClickUp: Custom Field Key </li>
                                <li>Mailchimp: Audience List: Limitation </li>
                                <li>ZagoMail: Tag explode </li>
                                <li>GoogleCalender: Authorization </li>
                                <li>SystemIO: Api Response </li>
                                <li>Breakdance: Double Form Submission </li>
                                <li>WooCommerce: Downloadable attachment </li>
                                <li>WhatsApp: Info Page Blank </li>
                                <li>Custom Form Submission: Edit state immutable </li>
                                <li>Breakdance: Field Label (Pro) </li>
                                <li>Licence Expired Value (Pro) </li>
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
                </div >
            </Modal >
        </div >
    )
}