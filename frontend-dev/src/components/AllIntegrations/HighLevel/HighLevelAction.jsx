/* eslint-disable no-param-reassign */

import { useState } from 'react'
import MultiSelect from 'react-multiple-select-dropdown-lite'
import { __ } from '../../../Utils/i18nwrap'
import ConfirmModal from '../../Utilities/ConfirmModal'
import TableCheckBox from '../../Utilities/TableCheckBox'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import Loader from '../../Loaders/Loader'
import Cooltip from '../../Utilities/Cooltip'
import { getAllTags } from './HighLevelCommonFunc'

export default function HighLevelActions({ highLevelConf, setHighLevelConf, loading, setLoading }) {
  const [actionMdl, setActionMdl] = useState({ show: false })
  const actionHandler = (e, type) => {
    const newConf = { ...highLevelConf }
    if (type === 'tagsAdd') {
      if (e.target?.checked) {
        getAllTags(highLevelConf, setHighLevelConf, setLoading)
        newConf.actions.tagsAdd = true
      } else {
        setActionMdl({ show: false })
        delete newConf.actions.tagsAdd
      }
      setActionMdl({ show: 'tagsAdd' })
    }
    if (type === 'tagsRemove') {
      if (e.target?.checked) {
        getAllTags(highLevelConf, setHighLevelConf, setLoading)
        newConf.actions.tagsRemove = true
      } else {
        setActionMdl({ show: false })
        delete newConf.actions.tagsRemove
      }
      setActionMdl({ show: 'tagsRemove' })
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
    setHighLevelConf({ ...newConf })
  }

  const clsActionMdl = () => {
    setActionMdl({ show: false })
  }

  const setChanges = (val, type) => {
    const newConf = { ...highLevelConf }
    newConf[type] = val
    setHighLevelConf({ ...newConf })
  }

  return (
    <div className="pos-rel d-flx w-8">
      <TableCheckBox
        checked={highLevelConf?.selectedStatus || false}
        onChange={(e) => actionHandler(e, 'status')}
        className="wdt-200 mt-4 mr-2"
        value="status"
        title={__('Status', 'bit-integrations')}
        subTitle={__('Set the subscriber status', 'bit-integrations')}
      />
      <TableCheckBox
        checked={highLevelConf?.selectedTags?.length || false}
        onChange={(e) => actionHandler(e, 'tagsAdd')}
        className="wdt-200 mt-4 mr-2"
        value="tagsAdd"
        title={__('Add Tags', 'bit - integrations')}
        subTitle={__('Select tags for subscriber')}
      />
      <TableCheckBox
        checked={highLevelConf?.selectedRemoveTags?.length || false}
        onChange={(e) => actionHandler(e, 'tagsRemove')}
        className="wdt-200 mt-4 mr-2"
        value="tagsRemove"
        title={__('Remove Tags', 'bit - integrations')}
        subTitle={__('Remove tags from subscriber')}
      />
      <ConfirmModal
        className="custom-conf-mdl"
        mainMdlCls="o-v"
        btnClass="purple"
        btnTxt={__('Ok', 'bit-integrations')}
        show={actionMdl.show === 'status'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Status', 'bit-integrations')}>
        <div className="btcd-hr mt-2 mb-2" />
        <div className="mt-2 flx">
          {__('Select subscriber status', 'bit-integrations')}
          <Cooltip width={250} icnSize={17} className="ml-1">
            <div className="txt-body">If omitted, the status will be set to active.</div>
          </Cooltip>
        </div>
        <div className="mt-2">
          <MultiSelect
            options={[
              { label: 'Active', value: 'active' },
              { label: 'Unsubscribed', value: 'unsubscribed' }
            ]}
            className="msl-wrp-options"
            defaultValue={highLevelConf?.selectedStatus}
            onChange={(val) => setChanges(val, 'selectedStatus')}
            singleSelect
            style={{ width: '100%' }}
          />
        </div>
      </ConfirmModal>
      <ConfirmModal
        className="custom-conf-mdl"
        mainMdlCls="o-v"
        btnClass="purple"
        btnTxt={__('Ok', 'bit-integrations')}
        show={actionMdl.show === 'tagsAdd'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Add Tags', 'bit-integrations')}>
        <div className="btcd-hr mt-2 mb-2" />
        <div className="mt-2 flx">
          {__('Select tags', 'bit-integrations')}
          <Cooltip width={350} icnSize={17} className="ml-1">
            <div className="txt-body">
              Choose from the existing tags, or create and add new ones by typing them and pressing
              enter or comma (,).
            </div>
          </Cooltip>
        </div>
        {loading.tags ? (
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
          <div className="flx flx-between mt-2">
            <MultiSelect
              options={highLevelConf?.tags?.map((tag) => ({ label: tag, value: tag }))}
              className="msl-wrp-options"
              defaultValue={highLevelConf?.selectedTags}
              onChange={(val) => setChanges(val, 'selectedTags')}
              customValue
            />
            <button
              onClick={() => getAllTags(highLevelConf, setHighLevelConf, setLoading)}
              className="icn-btn sh-sm ml-2 mr-2 tooltip"
              style={{ '--tooltip-txt': `${__('Refresh Tags', 'bit-integrations')}'` }}
              type="button">
              &#x21BB;
            </button>
          </div>
        )}
      </ConfirmModal>
      <ConfirmModal
        className="custom-conf-mdl"
        mainMdlCls="o-v"
        btnClass="purple"
        btnTxt={__('Ok', 'bit-integrations')}
        show={actionMdl.show === 'tagsRemove'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Remove Tags', 'bit-integrations')}>
        <div className="btcd-hr mt-2 mb-2" />
        <div className="mt-2 flx">{__('Select tags for remove', 'bit-integrations')}</div>
        {loading.tags ? (
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
          <div className="flx flx-between mt-2">
            <MultiSelect
              options={highLevelConf?.tags?.map((tag) => ({ label: tag, value: tag }))}
              className="msl-wrp-options"
              defaultValue={highLevelConf?.selectedRemoveTags}
              onChange={(val) => setChanges(val, 'selectedRemoveTags')}
            />
            <button
              onClick={() => getAllTags(highLevelConf, setHighLevelConf, setLoading)}
              className="icn-btn sh-sm ml-2 mr-2 tooltip"
              style={{ '--tooltip-txt': `${__('Refresh Tags', 'bit-integrations')}'` }}
              type="button">
              &#x21BB;
            </button>
          </div>
        )}
      </ConfirmModal>
    </div>
  )
}
