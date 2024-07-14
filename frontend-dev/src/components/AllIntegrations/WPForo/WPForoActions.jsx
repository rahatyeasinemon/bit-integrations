/* eslint-disable no-param-reassign */

import { useState } from 'react'
import { __ } from '../../../Utils/i18nwrap'
import ConfirmModal from '../../Utilities/ConfirmModal'
import TableCheckBox from '../../Utilities/TableCheckBox'
import MultiSelect from 'react-multiple-select-dropdown-lite'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import Cooltip from '../../Utilities/Cooltip'
import { getWPForoGroups } from './WPForoCommonFunc'
import Loader from '../../Loaders/Loader'

export default function WPForoActions({
  wpforoConf,
  setWPForoConf,
  loading,
  setLoading
}) {
  const [actionMdl, setActionMdl] = useState({ show: false })
  const actionHandler = (e) => {
    getWPForoGroups(wpforoConf, setWPForoConf, setLoading)
    const newConf = { ...wpforoConf }
    setActionMdl({ show: 'group' })
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
      <TableCheckBox
        checked={wpforoConf.selectedGroup || false}
        onChange={(e) => actionHandler(e)}
        className="wdt-200 mt-4 mr-2"
        value="select_group"
        title={__('Select Group', 'bit-integrations')}
        subTitle={__('Select a group to grant or revoke access.', 'bit-integrations')}
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
              The user will be added or removed from the selected group.
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
    </div>
  )
}
