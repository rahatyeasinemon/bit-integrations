import { useEffect, useState } from 'react'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import { useNavigate, useParams } from 'react-router-dom'
import { useRecoilValue } from 'recoil'
import BackIcn from '../../../Icons/BackIcn'
import { __ } from '../../../Utils/i18nwrap'
import SnackMsg from '../../Utilities/SnackMsg'
import Steps from '../../Utilities/Steps'
import { saveActionConf } from '../IntegrationHelpers/IntegrationHelpers'
import IntegrationStepThree from '../IntegrationHelpers/IntegrationStepThree'
import { handleInput, checkMappedFields } from './BuddyBossCommonFunc'
import BuddyBossAuthorization from './BuddyBossAuthorization'
import BuddyBossIntegLayout from './BuddyBossIntegLayout'

function BuddyBoss({ formFields, setFlow, flow, allIntegURL, isInfo, edit }) {
  const navigate = useNavigate()
  const { formID } = useParams()
  const [isLoading, setIsLoading] = useState(false)
  const [step, setStep] = useState(1)
  const [snack, setSnackbar] = useState({ show: false })

  const allActions = [
    { key: '1', label: __('Create Group Pro', 'bit-integrations') },
    { key: '2', label: __('Add the user to a group', 'bit-integrations') },
    { key: '3', label: __('End friendship with a user Pro', 'bit-integrations') },
    { key: '4', label: __('Follow a user Pro', 'bit-integrations') },
    { key: '5', label: __('Post a topic in a forum Pro', 'bit-integrations') },
    { key: '6', label: __('Remove user from a group Pro', 'bit-integrations') },
    { key: '7', label: __('Send a friendship request to a user Pro', 'bit-integrations') },
    {
      key: '8',
      label: __('Send a notification to all members of a group Pro', 'bit-integrations')
    },
    {
      key: '9',
      label: __('Send a private message to all members of a group Pro', 'bit-integrations')
    },
    { key: '10', label: __('Send a private message to a user Pro', 'bit-integrations') },
    { key: '11', label: __('Send a notification to a user Pro', 'bit-integrations') },
    { key: '12', label: __('Stop following a user Pro', 'bit-integrations') },
    { key: '13', label: __('Subscribe the user to a forum Pro', 'bit-integrations') },
    {
      key: '14',
      label: __('Add a post to the activity stream of a group Pro', 'bit-integrations')
    },
    { key: '15', label: __('Add a post to the site wide activity stream Pro', 'bit-integrations') },
    { key: '16', label: __("Add a post to the user's activity stream Pro", 'bit-integrations') },
    { key: '17', label: __('Post a reply to a topic in a forum', 'bit-integrations') },
    { key: '18', label: __("Set the user's status to a specific status", 'bit-integrations') }
  ]

  // for action 1
  const createGroupFields = [
    { key: 'group_name', label: __('Group Name', 'bit-integrations'), required: true }
  ]

  // for action 5
  const topicInForumFields = [
    { key: 'topic_content', label: __('Topic Content', 'bit-integrations'), required: true },
    { key: 'topic_title', label: __('Topic Title', 'bit-integrations'), required: false }
  ]

  // for action 8

  const sendAllUserNotificationFields = [
    {
      key: 'notification_content',
      label: __('Notification Content', 'bit-integrations'),
      required: true
    },
    {
      key: 'notification_link',
      label: __('Notification Link', 'bit-integrations'),
      required: false
    }
  ]

  // for action 9
  const sendAllGroupPrivateMessageFields = [
    { key: 'message_content', label: __('Message Content', 'bit-integrations'), required: true },
    { key: 'message_subject', label: __('Message subject', 'bit-integrations'), required: false }
  ]

  // for action 14

  const addPostToGroupFields = [
    { key: 'activity_action', label: __('activity_action', 'bit-integrations'), required: true },
    { key: 'activity_content', label: __('activity_content', 'bit-integrations'), required: true }
  ]

  // for action 15

  const addPostSiteWideActivityStreamFields = [
    { key: 'activity_action', label: __('activity_action', 'bit-integrations'), required: false },
    {
      key: 'activity_action_link',
      label: __('activity_action_link', 'bit-integrations'),
      required: false
    },
    { key: 'activity_content', label: __('activity_content', 'bit-integrations'), required: true }
  ]

  // for action 17
  const postReplyTopicForumFields = [
    { key: 'reply_content', label: __('Reply Content', 'bit-integrations'), required: true }
  ]

  const groupPrivacyOptions = [
    { key: '1', label: __('Public', 'bit-integrations') },
    { key: '2', label: __('Private', 'bit-integrations') },
    { key: '3', label: __('Hidden', 'bit-integrations') }
  ]

  // for action 18
  const userStatusOptions = [
    { key: '1', label: __('Active', 'bit-integrations') },
    { key: '2', label: __('Suspend', 'bit-integrations') }
  ]

  const [buddyBossConf, setBuddyBossConf] = useState({
    name: 'BuddyBoss',
    type: 'BuddyBoss',
    mainAction: '',
    field_map: [{ formField: '', buddyBossFormField: '' }],
    allActions,
    groupPrivacyOptions,
    userStatusOptions,
    createGroupFields,
    topicInForumFields,
    sendAllUserNotificationFields,
    sendAllGroupPrivateMessageFields,
    addPostToGroupFields,
    addPostSiteWideActivityStreamFields,
    postReplyTopicForumFields,
    actions: {}
  })
  const nextPage = () => {
    setTimeout(() => {
      document.getElementById('btcd-settings-wrp').scrollTop = 0
    }, 300)
    if (
      ['1', '5', '8', '9', '10', '11', '14', '15', '16', '17'].includes(buddyBossConf.mainAction) &&
      !checkMappedFields(buddyBossConf)
    ) {
      setSnackbar({ show: true, msg: __('Please map fields to continue.', 'bit-integrations') })
      return
    }
    if (buddyBossConf.mainAction !== '') {
      setStep(3)
    }
  }

  function isDisabled() {
    switch (buddyBossConf.mainAction) {
      case '1':
        return buddyBossConf.privacyId === undefined
      case '2':
        return buddyBossConf.groupId === undefined
      case '3':
        return buddyBossConf.friendId === undefined
      case '4':
        return buddyBossConf.friendId === undefined
      case '5':
        return buddyBossConf.forumId === undefined
      case '6':
        return buddyBossConf.groupId === undefined
      case '7':
        return buddyBossConf.friendId === undefined
      case '8':
        return buddyBossConf.groupId === undefined || buddyBossConf.friendId === undefined
      case '9':
        return buddyBossConf.groupId === undefined || buddyBossConf.friendId === undefined
      case '10':
        return buddyBossConf.friendId === undefined
      case '12':
        return buddyBossConf.friendId === undefined
      case '13':
        return buddyBossConf.forumId === undefined
      case '14':
        return buddyBossConf.groupId === undefined || buddyBossConf.friendId === undefined
      case '15':
        return buddyBossConf.friendId === undefined
      case '16':
        return buddyBossConf.friendId === undefined
      case '17':
        return buddyBossConf.topicId === undefined || buddyBossConf.forumId === undefined
      case '18':
        return buddyBossConf.userStatusId === undefined
      default:
        return false
    }
  }

  return (
    <div>
      <SnackMsg snack={snack} setSnackbar={setSnackbar} />
      <div className="txt-center mt-2">
        <Steps step={3} active={step} />
      </div>

      {/* STEP 1 */}
      <BuddyBossAuthorization
        formID={formID}
        buddyBossConf={buddyBossConf}
        setBuddyBossConf={setBuddyBossConf}
        step={step}
        setStep={setStep}
        isLoading={isLoading}
        setIsLoading={setIsLoading}
        setSnackbar={setSnackbar}
      />

      {/* STEP 2 */}
      <div
        className="btcd-stp-page"
        style={{ ...(step === 2 && { width: 900, height: 'auto', overflow: 'visible' }) }}>
        <BuddyBossIntegLayout
          formFields={formFields}
          handleInput={(e) =>
            handleInput(e, buddyBossConf, setBuddyBossConf, setIsLoading, setSnackbar, formID)
          }
          buddyBossConf={buddyBossConf}
          setBuddyBossConf={setBuddyBossConf}
          isLoading={isLoading}
          setIsLoading={setIsLoading}
          setSnackbar={setSnackbar}
          allIntegURL={allIntegURL}
          isInfo={isInfo}
          edit={edit}
        />

        <button
          onClick={() => nextPage(3)}
          disabled={!buddyBossConf.mainAction || isLoading || isDisabled()}
          className="btn f-right btcd-btn-lg purple sh-sm flx"
          type="button">
          {__('Next', 'bit-integrations')}
          &nbsp;
          <div className="btcd-icn icn-arrow_back rev-icn d-in-b" />
        </button>
      </div>
      {/* STEP 3 */}

      <IntegrationStepThree
        step={step}
        saveConfig={() =>
          saveActionConf({
            flow,
            setFlow,
            allIntegURL,
            navigate,
            conf: buddyBossConf,
            setIsLoading,
            setSnackbar
          })
        }
        isLoading={isLoading}
        dataConf={buddyBossConf}
        setDataConf={setBuddyBossConf}
        formFields={formFields}
      />
    </div>
  )
}

export default BuddyBoss
