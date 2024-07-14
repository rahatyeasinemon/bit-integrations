/* eslint-disable no-param-reassign */

import { useState } from 'react'
import { __ } from '../../../Utils/i18nwrap'
import ConfirmModal from '../../Utilities/ConfirmModal'
import TableCheckBox from '../../Utilities/TableCheckBox'
import MultiSelect from 'react-multiple-select-dropdown-lite'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import Cooltip from '../../Utilities/Cooltip'
import { getWPForoGroups, getWPForoReputations } from './WPForoCommonFunc'
import Loader from '../../Loaders/Loader'

export default function WPForoActions({ wpforoConf, setWPForoConf, loading, setLoading }) {
  const [actionMdl, setActionMdl] = useState({ show: false })
  const actionHandler = (e, type) => {
    const newConf = { ...wpforoConf }

    if (type === 'reputations') {
      getWPForoReputations(wpforoConf, setWPForoConf, setLoading)
      setActionMdl({ show: 'reputation' })
    } else if (type === 'groups') {
      getWPForoGroups(wpforoConf, setWPForoConf, setLoading)
      setActionMdl({ show: 'group' })
    }

    setWPForoConf({ ...newConf })
  }

  const clsActionMdl = () => {
    setActionMdl({ show: false })
  }

  const setChanges = (val, type) => {
    const newConf = { ...wpforoConf }
    newConf[type] = val
    setWPForoConf({ ...newConf })
  }

  return (
    <div className="pos-rel d-flx w-8">
      {wpforoConf.selectedTask === 'userReputation' && (
        <>
          <TableCheckBox
            checked={wpforoConf.selectedReputation || false}
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
                  options={wpforoConf.reputations}
                  className="msl-wrp-options"
                  defaultValue={wpforoConf?.selectedReputation}
                  onChange={(val) => setChanges(val, 'selectedReputation')}
                  style={{ width: '100%' }}
                  singleSelect
                />
              </div>
            )}
          </ConfirmModal>
        </>
      )}
      {wpforoConf.selectedTask === 'addToGroup' && (
        <>
          <TableCheckBox
            checked={wpforoConf.selectedGroup || false}
            onChange={(e) => actionHandler(e, 'groups')}
            className="wdt-200 mt-4 mr-2"
            value="select_group"
            title={__('Select Group', 'bit-integrations')}
            subTitle={__('Select a group to add a user to it.', 'bit-integrations')}
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
                <div className="txt-body">User will be added to the selected group.</div>
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
                  options={wpforoConf.groups}
                  className="msl-wrp-options"
                  defaultValue={wpforoConf?.selectedGroup}
                  onChange={(val) => setChanges(val, 'selectedGroup')}
                  style={{ width: '100%' }}
                  singleSelect
                />
              </div>
            )}
          </ConfirmModal>
        </>
      )}
    </div>
  )
}
