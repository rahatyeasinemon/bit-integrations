import { Suspense, useEffect, useState } from 'react'
import { useRecoilState } from 'recoil'
import { $btcbi } from '../GlobalStates'
import ChangelogIcn from '../Icons/ChangeLogIcn'
import { __, sprintf } from '../Utils/i18nwrap'
import Modal from '../components/Utilities/Modal'
import changelogInfo from '../Utils/StaticData/changelogInfo'
import bitsFetch from '../Utils/bitsFetch'
import Loader from '../components/Loaders/Loader'
import ExternalLinkIcn from '../Icons/ExternalLinkIcn'

export default function ChangelogToggle() {
  const [btcbi, setBtcbi] = useRecoilState($btcbi)
  const [show, setShow] = useState(btcbi.changelogVersion !== btcbi.version)
  const [showAnalyticsOptin, setShowAnalyticsOptin] = useState([])
  const currentChangelog = '2.2.2'
  const currenChangelog = changelogInfo[currentChangelog]
  const [loading, setLoading] = useState('')

  const setChangeLogVersion = (val) => {
    setShow(val)
    if (!val) {
      bitsFetch(
        {
          version: btcbi.version
        },
        'changelog_version'
      ).then(() => {
        setBtcbi((prevBtcbi) => ({ ...prevBtcbi, changelogVersion: prevBtcbi.version }))
      })
    }
  }

  const handleSubmit = () => {
    bitsFetch({ isChecked: true }, 'analytics/optIn')
    setShow(false)
  }

  const [isChecked, setIsChecked] = useState(true)

  const closeModal = () => {
    setShow(false)
    setChangeLogVersion()
  }

  useEffect(() => {
    if (show) {
      setLoading(true)
      bitsFetch({}, 'analytics/check', '', 'GET').then((res) => {
        setShowAnalyticsOptin(res.data)
        setLoading(false)
      })
    }
  }, [show])

  return (
    <div className="changelog-toggle">
      <button
        title={__("What's New", 'bit-integrations')}
        type="button"
        className="changelog-btn"
        onClick={() => setShow(true)}>
        <ChangelogIcn size={25} />
      </button>
      <Modal
        sm
        show={show}
        setModal={setChangeLogVersion}
        closeIcon={showAnalyticsOptin}
        style={{ position: `absolute`, top: `60px`, left: `50%`, transform: `translateX(-50%)` }}>
        {loading ? (
          <Loader
            style={{
              display: 'flex',
              justifyContent: 'center',
              alignItems: 'center',
              height: 45,
              transform: 'scale(0.5)'
            }}
          />
        ) : (
          <div className="changelog">
            <div className="flx flx-col flx-center whats-new">
              <h3>{sprintf(__("What's New in %s", 'bit-integrations'), btcbi.version)}?</h3>
              <small
                className="date"
                dangerouslySetInnerHTML={{
                  __html: sprintf(
                    __('Updated at:  <b>%s</b>', 'bit-integrations'),
                    '28th August 2024'
                  )
                }}></small>
            </div>
            <div className="changelog-content">
              <span className="new-integration">
                <b>{__('New Trigger', 'bit-integrations')}</b>
              </span>

              <div className="integration-list">
                <ul>
                  <li> WP Job Manager (Pro) </li>
                </ul>
              </div>

              <span className="new-feature">
                <b>{__('New Features', 'bit-integrations')}</b>
              </span>

              <div className="feature-list">
                <ul>
                  <li>GetResponse: Added the AutoResponder day of cycle feature (Pro)</li>
                  <li>SendPulse: Introduced custom field functionality (Pro)</li>
                </ul>
              </div>
              <div>
                <span className="footer">{__('For more details,')}</span>
                <a
                  href="https://bitapps.pro/docs/bit-integrations/free-changelogs/"
                  target="_blank"
                  rel="noreferrer">
                  {__('Click here')}&nbsp;
                  <ExternalLinkIcn size="14" />
                </a>
              </div>
            </div>
            {!showAnalyticsOptin && (
              <div>
                <div className="btcd-hr mt-2"></div>
                <div className="flx flx-col flx-center">
                  <h4 className="mt-2 mb-0">
                    {__('Opt-In For Plugin Improvement', 'bit-integrations')}
                  </h4>
                </div>
                <div className="m-2 txt-sm">
                  {__(
                    'Accept and continue to share usage data to help us improve the plugin, the plugin will still function if you skip.',
                    'bit-integrations'
                  )}
                  <br />
                  <a
                    className="app-link-active"
                    target="blank"
                    href="https://bitapps.pro/terms-of-service/">
                    {__('Click here to see terms', 'bit-integrations')}
                  </a>
                </div>
                <button
                  type="button"
                  className="btn round btn-md gray gray-sh"
                  onClick={() => closeModal()}>
                  Skip
                </button>
                <button
                  type="button"
                  className="btn round btcd-btn-lg purple purple-sh submit-btn"
                  onClick={() => handleSubmit()}>
                  {__('Accept and continue', 'bit-integrations')}
                </button>
              </div>
            )}
          </div>
        )}
      </Modal>
    </div>
  )
}
