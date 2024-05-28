/* eslint-disable no-param-reassign */

import { useState } from 'react'
import MultiSelect from 'react-multiple-select-dropdown-lite'
import { __ } from '../../../Utils/i18nwrap'
import ConfirmModal from '../../Utilities/ConfirmModal'
import TableCheckBox from '../../Utilities/TableCheckBox'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import Loader from '../../Loaders/Loader'
import Cooltip from '../../Utilities/Cooltip'
import { getAllTags } from './DripCommonFunc'


export default function DripActions({ dripConf, setDripConf, loading, setLoading }) {
  const [actionMdl, setActionMdl] = useState({ show: false })
  const actionHandler = (e, type) => {
    const newConf = { ...dripConf }
    if (type === 'tagsAdd') {
      if (e.target?.checked) {
        getAllTags(dripConf, setDripConf, setLoading)
        newConf.actions.tagsAdd = true
      } else {
        setActionMdl({ show: false })
        delete newConf.actions.tagsAdd
      }
      setActionMdl({ show: 'tagsAdd' })
    }
    if (type === 'update') {
      if (e.target.checked) {
        newConf.actions.update = true
      } else {
        delete newConf.actions.update
      }
    }
    if (type === 'status') {
      if (e.target.checked) {
        newConf.actions.status = true
      } else {
        delete newConf.actions.status
        setActionMdl({ show: false })
      }
      setActionMdl({ show: 'status' })
    }
    setDripConf({ ...newConf })
  }

  const clsActionMdl = () => {
    setActionMdl({ show: false })
  }

  const setChanges = (val, type) => {
    const newConf = { ...dripConf }
    newConf[type] = val
    setDripConf({ ...newConf })
  }

  return (
    <div className="pos-rel d-flx w-8">
      <TableCheckBox checked={dripConf?.selectedStatus || false} onChange={(e) => actionHandler(e, 'status')} className="wdt-200 mt-4 mr-2" value="status" title={__('Status', 'bit-integrations')} subTitle={__('Set the subscriber status', 'bit-integrations')} />
      <TableCheckBox checked={dripConf?.selectedTags?.length || false} onChange={(e) => actionHandler(e, 'tagsAdd')} className="wdt-200 mt-4 mr-2" value="tagsAdd" title={__('Add Tags', 'bit - integrations')} subTitle={__('Select tags for subscriber')} />
      <TableCheckBox checked={dripConf.actions?.update || false} onChange={(e) => actionHandler(e, 'update')} className="wdt-200 mt-4 mr-2" value="update_contact" title={__('Update contact', 'bit-integrations')} subTitle={__('Update an existing contact\'s info by responses.', 'bit-integrations')} />
      <ConfirmModal
        className="custom-conf-mdl"
        mainMdlCls="o-v"
        btnClass="blue"
        btnTxt={__('Ok', 'bit-integrations')}
        show={actionMdl.show === 'status'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Status', 'bit-integrations')}
      >
        <div className="btcd-hr mt-2 mb-2" />
        <div className="mt-2 flx">
          {__('Select subscriber status', 'bit-integrations')}
          <Cooltip width={250} icnSize={17} className="ml-2">
            <div className="txt-body">
              If omitted, the status will be set to active.
            </div>
          </Cooltip>
        </div>
        <div className="mt-2">
          <MultiSelect
            options={[
              { label: 'Active', value: 'active' },
              { label: 'Unsubscribed', value: 'unsubscribed' }
            ]}
            className="msl-wrp-options"
            defaultValue={dripConf?.selectedStatus}
            onChange={val => setChanges(val, 'selectedStatus')}
            singleSelect
            style={{ width: '100%' }}
          />
        </div>
      </ConfirmModal>
      <ConfirmModal
        className="custom-conf-mdl"
        mainMdlCls="o-v"
        btnClass="blue"
        btnTxt={__('Ok', 'bit-integrations')}
        show={actionMdl.show === 'tagsAdd'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Add Tags', 'bit-integrations')}
      >
        <div className="btcd-hr mt-2 mb-2" />
        <div className="mt-2">
          {__('Select tags', 'bit-integrations')}
        </div>
        {
          loading.tags ? (
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
                  options={dripConf?.tags?.map(tag => ({ label: tag, value: tag }))}
                  className="msl-wrp-options"
                  defaultValue={dripConf?.selectedTags}
                  onChange={val => setChanges(val, 'selectedTags')}
                />
                <button onClick={() => getAllTags(dripConf, setDripConf, setLoading)} className="icn-btn sh-sm ml-2 mr-2 tooltip" style={{ '--tooltip-txt': `${__('Refresh Tags', 'bit-integrations')}'` }} type="button">&#x21BB;</button>
              </div>
            )
        }
      </ConfirmModal>
    </div>
  )
}
