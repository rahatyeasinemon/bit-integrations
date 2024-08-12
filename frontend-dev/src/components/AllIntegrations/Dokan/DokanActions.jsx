/* eslint-disable no-param-reassign */

import { useState } from 'react'
import { __ } from '../../../Utils/i18nwrap'
import ConfirmModal from '../../Utilities/ConfirmModal'
import TableCheckBox from '../../Utilities/TableCheckBox'
import MultiSelect from 'react-multiple-select-dropdown-lite'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import Cooltip from '../../Utilities/Cooltip'
import Loader from '../../Loaders/Loader'
import { getDokanForums, getDokanGroups, getDokanReputations } from './dokanCommonFunctions'
import { TASK_LIST_VALUES } from './dokanConstants'
import { $btcbi } from '../../../GlobalStates'
import { useRecoilValue } from 'recoil'

export default function DokanActions({ dokanConf, setDokanConf, loading, setLoading }) {
  const [actionMdl, setActionMdl] = useState({ show: false })
  const btcbi = useRecoilValue($btcbi)
  const { isPro } = btcbi

  const actionHandler = (e, type) => {
    const newConf = { ...dokanConf }

    if (type === 'reputations') {
      getDokanReputations(dokanConf, setDokanConf, setLoading)
      setActionMdl({ show: 'reputation' })
    } else if (type === 'groups') {
      getDokanGroups(dokanConf, setDokanConf, setLoading)
      setActionMdl({ show: 'group' })
    } else if (type === 'forums') {
      getDokanForums(dokanConf, setDokanConf, setLoading)
      setActionMdl({ show: 'forum' })
    } else if (type === 'tags') {
      setActionMdl({ show: 'tags' })
    } else if (type === 'vendorCheckbox') {
      if (e.target.checked) {
        newConf.actions[e.target.value] = true
      } else {
        delete newConf.actions[e.target.value]
      }
    }

    setDokanConf({ ...newConf })
  }

  const clsActionMdl = () => {
    setActionMdl({ show: false })
  }

  const setChanges = (val, type) => {
    const newConf = { ...dokanConf }
    newConf[type] = val
    setDokanConf({ ...newConf })
  }

  return (
    <>
      {!isPro && (
        <div className="pt-2">
          <span className="dokan-actions-note">
            The Bit Integrations Pro plugin needs to be installed and activated to utilize these
            features.
          </span>
        </div>
      )}
      <div className="pos-rel d-flx w-8">
        {(dokanConf.selectedTask === TASK_LIST_VALUES.CREATE_VENDOR ||
          dokanConf.selectedTask === TASK_LIST_VALUES.UPDATE_VENDOR) && (
          <>
            <TableCheckBox
              checked={dokanConf.actions?.notifyVendor || false}
              onChange={(e) => actionHandler(e, 'vendorCheckbox')}
              className="wdt-200 mt-4 mr-2"
              value="notifyVendor"
              title={__('Notify Vendor', 'bit-integrations')}
              subTitle={__('Send the vendor an email about their account', 'bit-integrations')}
              isInfo={!isPro}
            />
            <TableCheckBox
              checked={dokanConf.actions?.enableSelling || false}
              onChange={(e) => actionHandler(e, 'vendorCheckbox')}
              className="wdt-200 mt-4 mr-2"
              value="enableSelling"
              title={__('Enable Selling', 'bit-integrations')}
              subTitle={__('Enable selling for this vendor', 'bit-integrations')}
              isInfo={!isPro}
            />
            <TableCheckBox
              checked={dokanConf.actions?.publishProduct || false}
              onChange={(e) => actionHandler(e, 'vendorCheckbox')}
              className="wdt-200 mt-4 mr-2"
              value="publishProduct"
              title={__('Publish Product Directly', 'bit-integrations')}
              subTitle={__('Publish product of this vendor directly', 'bit-integrations')}
              isInfo={!isPro}
            />
            <TableCheckBox
              checked={dokanConf.actions?.featureVendor || false}
              onChange={(e) => actionHandler(e, 'vendorCheckbox')}
              className="wdt-200 mt-4 mr-2"
              value="featureVendor"
              title={__(' Make Vendor Featured', 'bit-integrations')}
              subTitle={__('Make this vendor featured', 'bit-integrations')}
              isInfo={!isPro}
            />
          </>
        )}
        {dokanConf.selectedTask === TASK_LIST_VALUES.USER_REPUTATION && (
          <>
            <TableCheckBox
              checked={dokanConf.selectedReputation || false}
              onChange={(e) => actionHandler(e, 'reputations')}
              className="wdt-200 mt-4 mr-2"
              value="select_reputation"
              title={__('Select Reputation', 'bit-integrations')}
              subTitle={__('Select a reputation to set it for the user.', 'bit-integrations')}
            />
            <ConfirmModal
              className="custom-conf-mdl"
              mainMdlCls="o-v"
              btnClass="blue"
              btnTxt={__('Ok', 'bit-integrations')}
              show={actionMdl.show === 'reputation'}
              close={clsActionMdl}
              action={clsActionMdl}
              title={__('Reputations', 'bit-integrations')}>
              <div className="btcd-hr mt-2 mb-2" />
              <div className="mt-2 flx">
                {__('Select Reputation', 'bit-integrations')}
                <Cooltip width={250} icnSize={17} className="ml-1">
                  <div className="txt-body">
                    Selected reputation will be set as user new reputation.
                  </div>
                </Cooltip>
              </div>
              {loading.reputation ? (
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
                <div className="mt-2">
                  <MultiSelect
                    options={dokanConf.reputations}
                    className="msl-wrp-options"
                    defaultValue={dokanConf?.selectedReputation}
                    onChange={(val) => setChanges(val, 'selectedReputation')}
                    style={{ width: '100%' }}
                    singleSelect
                  />
                </div>
              )}
            </ConfirmModal>
          </>
        )}
        {(dokanConf.selectedTask === TASK_LIST_VALUES.ADD_TO_GROUP ||
          dokanConf.selectedTask === TASK_LIST_VALUES.REMOVE_FROM_GROUP) && (
          <>
            <TableCheckBox
              checked={dokanConf.selectedGroup || false}
              onChange={(e) => actionHandler(e, 'groups')}
              className="wdt-200 mt-4 mr-2"
              value="select_group"
              title={__('Select Group', 'bit-integrations')}
              subTitle={__(
                `Select a group to ${dokanConf.selectedTask === TASK_LIST_VALUES.ADD_TO_GROUP ? 'add a user to it' : 'remove a user from it'}.`,
                'bit-integrations'
              )}
            />
            <ConfirmModal
              className="custom-conf-mdl"
              mainMdlCls="o-v"
              btnClass="blue"
              btnTxt={__('Ok', 'bit-integrations')}
              show={actionMdl.show === 'group'}
              close={clsActionMdl}
              action={clsActionMdl}
              title={__('Groups', 'bit-integrations')}>
              <div className="btcd-hr mt-2 mb-2" />
              <div className="mt-2 flx">
                {__('Select Group', 'bit-integrations')}
                <Cooltip width={250} icnSize={17} className="ml-1">
                  <div className="txt-body">
                    The user will be{' '}
                    {dokanConf.selectedTask === TASK_LIST_VALUES.ADD_TO_GROUP
                      ? 'added to'
                      : 'removed from'}{' '}
                    the selected group.
                  </div>
                </Cooltip>
              </div>
              {loading.groups ? (
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
                <div className="mt-2">
                  <MultiSelect
                    options={dokanConf.groups}
                    className="msl-wrp-options"
                    defaultValue={dokanConf?.selectedGroup}
                    onChange={(val) => setChanges(val, 'selectedGroup')}
                    style={{ width: '100%' }}
                    singleSelect
                  />
                </div>
              )}
            </ConfirmModal>
          </>
        )}
        {dokanConf.selectedTask === TASK_LIST_VALUES.CREATE_TOPIC && (
          <>
            <TableCheckBox
              checked={dokanConf.selectedForum || false}
              onChange={(e) => actionHandler(e, 'forums')}
              className="wdt-200 mt-4 mr-2"
              value="select_forum"
              title={__('Select Forum', 'bit-integrations')}
              subTitle={__('Select a forum to create a topic in it', 'bit-integrations')}
            />
            <TableCheckBox
              checked={dokanConf.selectedTags || false}
              onChange={(e) => actionHandler(e, 'tags')}
              className="wdt-200 mt-4 mr-2"
              value="select_tags"
              title={__('Select Tags', 'bit-integrations')}
              subTitle={__('Add tags for the topic', 'bit-integrations')}
            />
            <TableCheckBox
              checked={dokanConf.actions?.privateTopic || false}
              onChange={(e) => actionHandler(e, 'privateTopic')}
              className="wdt-200 mt-4 mr-2"
              value="select_topic_is_private"
              title={__('Private Topic', 'bit-integrations')}
              subTitle={__('Make topic private', 'bit-integrations')}
            />
            <ConfirmModal
              className="custom-conf-mdl"
              mainMdlCls="o-v"
              btnClass="blue"
              btnTxt={__('Ok', 'bit-integrations')}
              show={actionMdl.show === 'forum'}
              close={clsActionMdl}
              action={clsActionMdl}
              title={__('Forums', 'bit-integrations')}>
              <div className="btcd-hr mt-2 mb-2" />
              <div className="mt-2 flx">
                {__('Select Forum', 'bit-integrations')}
                <Cooltip width={250} icnSize={17} className="ml-1">
                  <div className="txt-body">Topics will be added to the selected forum.</div>
                </Cooltip>
              </div>
              {loading.forums ? (
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
                <div className="mt-2">
                  <MultiSelect
                    options={dokanConf.forums}
                    className="msl-wrp-options"
                    defaultValue={dokanConf?.selectedForum}
                    onChange={(val) => setChanges(val, 'selectedForum')}
                    style={{ width: '100%' }}
                    singleSelect
                  />
                </div>
              )}
            </ConfirmModal>
            <ConfirmModal
              className="custom-conf-mdl"
              mainMdlCls="o-v"
              btnClass="blue"
              btnTxt={__('Ok', 'bit-integrations')}
              show={actionMdl.show === 'tags'}
              close={clsActionMdl}
              action={clsActionMdl}
              title={__('Tags', 'bit-integrations')}>
              <div className="btcd-hr mt-2 mb-2" />
              <div className="mt-2 flx">
                {__('Select Tags', 'bit-integrations')}
                <Cooltip width={250} icnSize={17} className="ml-1">
                  <div className="txt-body">
                    Separate tags by pressing enter or comma (,) after writing them.
                  </div>
                </Cooltip>
              </div>
              <div className="mt-2">
                <MultiSelect
                  options={dokanConf.tags}
                  className="msl-wrp-options"
                  defaultValue={dokanConf?.selectedTags}
                  onChange={(val) => setChanges(val, 'selectedTags')}
                  style={{ width: '100%' }}
                  customValue
                />
              </div>
            </ConfirmModal>
          </>
        )}
      </div>
    </>
  )
}
