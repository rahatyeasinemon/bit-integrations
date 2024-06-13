
import { Suspense, useEffect, useState } from 'react'
import { useRecoilState } from 'recoil'
import { $btcbi } from '../GlobalStates'
import ChangelogIcn from '../Icons/ChangeLogIcn'
import { __ } from '../Utils/i18nwrap'
import Modal from '../components/Utilities/Modal'
import changelogInfo from '../Utils/StaticData/changelogInfo'
import bitsFetch from '../Utils/bitsFetch'
import CheckBox from '../components/Utilities/CheckBox'
import Loader from '../components/Loaders/Loader'

export default function ChangelogToggle() {
    const [btcbi, setBtcbi] = useRecoilState($btcbi)
    const [show, setShow] = useState(btcbi.changelogVersion !== btcbi.version)
    const [showAnalyticsOptin, setShowAnalyticsOptin] = useState([])
    const currentChangelog = '2.0.7'
    const currenChangelog = changelogInfo[currentChangelog]
    const [loading, setLoading] = useState('')

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

    const handleSubmit = () => {
        bitsFetch({ isChecked: true }, 'analytics/optIn')
        setShow(false)
    }

    const [isChecked, setIsChecked] = useState(true)

    const handleCheckboxChange = e => {
        setIsChecked(!isChecked);
    };

    useEffect(() => {
        if (show) {
            setLoading(true)
            bitsFetch(
                {},
                'analytics/check',
                '',
                'GET',
            ).then((res) => {
                setShowAnalyticsOptin(res.data)
                setLoading(false)
            })
        }
    }, [show])


    return (

        <div className="changelog-toggle">
            <button
                title={('What\'s New')}
                type="button"
                className="changelog-btn"
                onClick={() => setShow(true)}
            >
                <ChangelogIcn size={25} />
            </button>
            <Modal sm show={show} setModal={setChangeLogVersion} closeIcon={showAnalyticsOptin}>
                {
                    loading
                        ? (
                            <Loader style={{
                                display: 'flex',
                                justifyContent: 'center',
                                alignItems: 'center',
                                height: 45,
                                transform: 'scale(0.5)',
                            }}
                            />
                        ) : (
                            <div className='changelog'>
                                <div className="flx flx-col flx-center whats-new">
                                    <h3>What's New in {btcbi.version}?</h3>
                                    <small className='date'> <b>10th June 2024</b></small>
                                </div>
                                <div className='changelog-content'>
                                    <span className='new-integration' ><b>New Integration</b></span>

                                    <div className='integration-list'>
                                        <ul>
                                            <li>Gutena Forms – Contact Forms Block </li>
                                            <li>SiteOrigin Widgets Bundle (Form only) </li>
                                        </ul>
                                    </div>

                                    <span className='new-feature' ><b>New Features</b></span>

                                    <div className='integration-list'>
                                        <ul>
                                            <li>Action Hook: Edit Integration page added </li>
                                        </ul>
                                    </div>

                                    <span className='fixes'><b>Fixed</b></span>

                                    <div className='fixes-list'>
                                        <ul>
                                            <li>Sendy: Custom Form Fields Value </li>
                                            <li>Google Calender: TimeZone  </li>
                                            <li>Tutor LMS: Quiz attempt status (Pro) </li>
                                            <a href="https://bitapps.pro/docs/bit-integrations/free-changelogs/">Click here to see all</a>
                                        </ul>
                                    </div>
                                </div>
                                {!showAnalyticsOptin &&
                                    <div>
                                        <div className='m-2 txt-body'>
                                            Opt-in to share usage data for improvements, or skip and continue using the plugin.
                                            <br />
                                            <a className='app-link-active' href='https://bitapps.pro/terms-of-service/'>Click here to see terms</a>
                                        </div>
                                        <button
                                            type="button"
                                            className="btn round btn-md gray gray-sh"
                                            onClick={() => setShow(false)
                                            }
                                        >
                                            Skip
                                        </button>
                                        <button
                                            type="button"
                                            className="btn round btcd-btn-lg purple purple-sh submit-btn"
                                            onClick={() => handleSubmit()
                                            }
                                        >
                                            Accept and continue
                                        </button>
                                    </div>
                                }
                                {/* <div>
                                    <span className='footer'>{__('For more details,')}</span>
                                    <a href="https://bitapps.pro/docs/bit-integrations/free-changelogs/" target="_blank" rel="noreferrer">
                                        {__('Click here ')}
                                        <ExternalLinkIcn size="14" />
                                    </a>
                                </div> */}
                            </div >)}
            </Modal >
        </div >
    )
}