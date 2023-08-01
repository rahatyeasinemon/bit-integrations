/* eslint-disable no-param-reassign */

import { useState } from 'react'
import MultiSelect from 'react-multiple-select-dropdown-lite'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import { __ } from '../../../Utils/i18nwrap'
import Loader from '../../Loaders/Loader'
import ConfirmModal from '../../Utilities/ConfirmModal'
import TableCheckBox from '../../Utilities/TableCheckBox'
import { getAllStaffs } from './PerfexCRMCommonFunc'

export default function PerfexCRMActions({ perfexCRMConf, setPerfexCRMConf, loading, setLoading }) {
  const [actionMdl, setActionMdl] = useState({ show: false, action: () => { } })

  const actionHandler = (e, type) => {
    const newConf = { ...perfexCRMConf }
    if (type === 'direction') {
      if (e.target?.checked) {
        newConf.actions.direction = true
      } else {
        setActionMdl({ show: false })
        delete newConf.actions.direction
      }
    } else if (type === 'contactIsPrimary') {
      if (e.target?.checked) {
        newConf.actions.contactIsPrimary = true
      } else {
        delete newConf.actions.contactIsPrimary
      }
    } else if (type === 'permission') {
      if (e.target?.checked) {
        newConf.permissions = [
          { 'id': 1, 'name': 'Invoices permission' },
          { 'id': 2, 'name': 'Estimates permission' },
          { 'id': 3, 'name': 'Contracts permission' },
          { 'id': 4, 'name': 'Proposals permission' },
          { 'id': 5, 'name': 'Support permission' },
          { 'id': 6, 'name': 'Projects permission' }
        ]
        newConf.actions.permission = true
      } else {
        setActionMdl({ show: false })
        delete newConf.actions.permission
      }
    } else if (type === 'leadIsPublic') {
      if (e.target?.checked) {
        newConf.actions.leadIsPublic = true
      } else {
        delete newConf.actions.leadIsPublic
      }
    } else if (type === 'contactedToday') {
      if (e.target?.checked) {
        newConf.actions.contactedToday = true
      } else {
        delete newConf.actions.contactedToday
      }
    } else if (type === 'projectMembers') {
      if (e.target?.checked) {
        getAllStaffs(newConf, setPerfexCRMConf, loading, setLoading)
        newConf.actions.projectMembers = true
      } else {
        delete newConf.actions.projectMembers
      }
    }

    setActionMdl({ show: type })
    setPerfexCRMConf({ ...newConf })
  }

  const clsActionMdl = () => {
    setActionMdl({ show: false })
  }

  const setChanges = (val, name) => {
    const newConf = { ...perfexCRMConf }
    newConf[name] = val
    setPerfexCRMConf({ ...newConf })
  }

  return (
    <div className="pos-rel d-flx flx-wrp">
      {perfexCRMConf.actionName === 'contact' && <TableCheckBox checked={perfexCRMConf?.selectedDirection || false} onChange={(e) => actionHandler(e, 'direction')} className="wdt-200 mt-4 mr-2" value="direction" title={__('Add Direction', 'bit - integrations')} subTitle={__('Add Direction')} />}
      {perfexCRMConf.actionName === 'contact' && <TableCheckBox checked={perfexCRMConf?.selectedPermission?.length || false} onChange={(e) => actionHandler(e, 'permission')} className="wdt-200 mt-4 mr-2" value="permission" title={__('Add Permissions', 'bit - integrations')} subTitle={__('Add Permissions for this contact')} />}
      {perfexCRMConf.actionName === 'contact' && <TableCheckBox checked={perfexCRMConf.actions?.contactIsPrimary || false} onChange={(e) => actionHandler(e, 'contactIsPrimary')} className="wdt-200 mt-4 mr-2" value="contactIsPrimary" title={__('Is it Primary Contact?', 'bit-integrations')} subTitle={__('Is it Primary Contact?', 'bit-integrations')} />}
      {perfexCRMConf.actionName === 'lead' && <TableCheckBox checked={perfexCRMConf.actions?.leadIsPublic || false} onChange={(e) => actionHandler(e, 'leadIsPublic')} className="wdt-200 mt-4 mr-2" value="leadIsPublic" title={__('Public', 'bit-integrations')} subTitle={__('Is it Public?', 'bit-integrations')} />}
      {perfexCRMConf.actionName === 'lead' && <TableCheckBox checked={perfexCRMConf.actions?.contactedToday || false} onChange={(e) => actionHandler(e, 'contactedToday')} className="wdt-200 mt-4 mr-2" value="contactedToday" title={__('Contacted Today', 'bit-integrations')} subTitle={__('Contacted Today', 'bit-integrations')} />}
      {perfexCRMConf.actionName === 'project' && <TableCheckBox checked={perfexCRMConf?.selectedProjectMembers || false} onChange={(e) => actionHandler(e, 'projectMembers')} className="wdt-200 mt-4 mr-2" value="projectMembers" title={__('Add Project Members', 'bit-integrations')} subTitle={__('Add Project Members', 'bit-integrations')} />}

      <ConfirmModal
        className="custom-conf-mdl"
        mainMdlCls="o-v"
        btnClass="blue"
        btnTxt={__('Ok', 'bit-integrations')}
        show={actionMdl.show === 'direction'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Direction', 'bit-integrations')}
      >
        <div className="btcd-hr mt-2 mb-2" />
        <div className="mt-2">
          {__('Select Direction', 'bit-integrations')}
        </div>

        <div className="flx flx-between mt-2">
          <MultiSelect
            options={['rtl', 'ltr']?.map(direction => ({ label: direction, value: direction }))}
            className="msl-wrp-options"
            defaultValue={perfexCRMConf?.selectedDirection}
            onChange={val => setChanges(val, 'selectedDirection')}
            singleSelect
            closeOnSelect
          />
        </div>
      </ConfirmModal>

      <ConfirmModal
        className="custom-conf-mdl"
        mainMdlCls="o-v"
        btnClass="blue"
        btnTxt={__('Ok', 'bit-integrations')}
        show={actionMdl.show === 'permission'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Add Permission', 'bit-integrations')}
      >
        <div className="btcd-hr mt-2 mb-2" />
        <div className="mt-2">
          {__('Select Permission', 'bit-integrations')}
        </div>

        <div className="flx flx-between mt-2">
          <MultiSelect
            options={perfexCRMConf?.permissions?.map(permission => ({ label: permission.name, value: permission.id.toString() }))}
            className="msl-wrp-options"
            defaultValue={perfexCRMConf?.selectedPermission}
            onChange={val => setChanges(val, 'selectedPermission')}
          />
        </div>
      </ConfirmModal>

      <ConfirmModal
        className="custom-conf-mdl"
        mainMdlCls="o-v"
        btnClass="blue"
        btnTxt={__('Ok', 'bit-integrations')}
        show={actionMdl.show === 'projectMembers'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Add Project Members', 'bit-integrations')}
      >
        <div className="btcd-hr mt-2 mb-2" />
        <div className="mt-2">
          {__('Select Project Member', 'bit-integrations')}
        </div>

        {
          loading.staffs ? (
            <Loader style={{
              display: 'flex',
              justifyContent: 'center',
              alignItems: 'center',
              height: 45,
              transform: 'scale(0.5)',
            }}
            />
          )
            : (
              <div className="flx flx-between mt-2">
                <MultiSelect
                  options={perfexCRMConf?.staffs?.map(staff => ({ label: staff.name, value: staff.id.toString() }))}
                  className="msl-wrp-options"
                  defaultValue={perfexCRMConf?.selectedProjectMembers}
                  onChange={val => setChanges(val, 'selectedProjectMembers')}
                />
                <button onClick={() => getAllStaffs(perfexCRMConf, setPerfexCRMConf, loading, setLoading)} className="icn-btn sh-sm ml-2 mr-2 tooltip" style={{ '--tooltip-txt': `${__('Refresh Project Members', 'bit-integrations')}'` }} type="button">&#x21BB;</button>
              </div>
            )
        }
      </ConfirmModal>
    </div>
  )
}

