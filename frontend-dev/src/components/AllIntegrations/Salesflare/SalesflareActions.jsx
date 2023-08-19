/* eslint-disable no-param-reassign */

import { useState } from 'react'
import MultiSelect from 'react-multiple-select-dropdown-lite'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import { __ } from '../../../Utils/i18nwrap'
import ConfirmModal from '../../Utilities/ConfirmModal'
import TableCheckBox from '../../Utilities/TableCheckBox'
import { getAllTags } from './SalesflareCommonFunc'
import Loader from '../../Loaders/Loader'

export default function SalesflareActions({ salesflareConf, setSalesflareConf, loading, setLoading }) {
  const [actionMdl, setActionMdl] = useState({ show: false, action: () => { } })

  const actionHandler = (e, type) => {
    const newConf = { ...salesflareConf }
    if (type === 'tags') {
      if (e.target?.checked) {
        newConf.actions.tags = true
        getAllTags(salesflareConf, setSalesflareConf, setLoading)
      } else {
        setActionMdl({ show: false })
        delete newConf.actions.tags
      }
    } else if (type === 'leadSource') {
      if (e.target?.checked) {
        newConf.actions.leadSource = true
        newConf.leadSources = ['Advertisement', 'Campaign', 'Cold Calling', "Customer's Vendor", 'Exhibition', 'Existing Cutomer', 'Mass Mailing', 'Reference', 'Supplier Reference', 'Walk In']
      } else {
        delete newConf.actions.leadSource
      }
    } else if (type === 'LeadAddressType') {
      if (e.target?.checked) {
        newConf.actions.LeadAddressType = true
        newConf.LeadAddressTypes = ['Billing', 'Shipping', 'Office', "Personal", 'Plant', 'Postal', 'Shop', 'SubsiDiary', 'Warehouse', 'Current', 'Permanent', 'Other']
      } else {
        delete newConf.actions.LeadAddressType
      }
    } else if (type === 'LeadType') {
      if (e.target?.checked) {
        newConf.actions.LeadType = true
        newConf.LeadTypes = ['Client', 'Channel Partner', 'Consultant']
      } else {
        delete newConf.actions.LeadType
      }
    } else if (type === 'RequestType') {
      if (e.target?.checked) {
        newConf.actions.RequestType = true
        newConf.RequestTypes = ['Product Enquiry', 'Request For Information', 'Suggestions', 'Other']
      } else {
        delete newConf.actions.RequestType
      }
    } else if (type === 'MarketSegment') {
      if (e.target?.checked) {
        newConf.actions.MarketSegment = true
        newConf.MarketSegments = ['Lower Income', 'Middle Income', 'Upper Income']
      } else {
        delete newConf.actions.MarketSegment
      }
    } else if (type === 'ContactStatus') {
      if (e.target?.checked) {
        newConf.actions.ContactStatus = true
        newConf.ContactStatus = ['Passive', 'Open', 'Replied']
      } else {
        delete newConf.actions.ContactStatus
      }
    }

    setActionMdl({ show: type })
    setSalesflareConf({ ...newConf })
  }

  const clsActionMdl = () => {
    setActionMdl({ show: false })
  }

  const setChanges = (val, name) => {
    const newConf = { ...salesflareConf }
    newConf[name] = val
    setSalesflareConf({ ...newConf })
  }

  return (
    <div className="pos-rel d-flx flx-wrp">
      {salesflareConf.actionName && <TableCheckBox checked={salesflareConf?.selectedTags || false} onChange={(e) => actionHandler(e, 'tags')} className="wdt-200 mt-4 mr-2" value="tags" title={__('Add Tags', 'bit-integrations')} subTitle={__('Add Tags', 'bit-integrations')} />}
      {salesflareConf.actionName === 'lead' && <TableCheckBox checked={salesflareConf?.selectedLeadSource || false} onChange={(e) => actionHandler(e, 'leadSource')} className="wdt-200 mt-4 mr-2" value="leadSource" title={__('Add Source', 'bit - integrations')} subTitle={__('Add Source')} />}
      {salesflareConf.actionName === 'lead' && <TableCheckBox checked={salesflareConf?.selectedLeadAddressType || false} onChange={(e) => actionHandler(e, 'LeadAddressType')} className="wdt-200 mt-4 mr-2" value="LeadAddressType" title={__('Add Address Type', 'bit - integrations')} subTitle={__('Add Address Type')} />}
      {salesflareConf.actionName === 'lead' && <TableCheckBox checked={salesflareConf?.selectedLeadType || false} onChange={(e) => actionHandler(e, 'LeadType')} className="wdt-200 mt-4 mr-2" value="LeadType" title={__('Add Lead Type', 'bit - integrations')} subTitle={__('Add Lead Type')} />}
      {salesflareConf.actionName === 'lead' && <TableCheckBox checked={salesflareConf?.selectedRequestType || false} onChange={(e) => actionHandler(e, 'RequestType')} className="wdt-200 mt-4 mr-2" value="RequestType" title={__('Add Request Type', 'bit - integrations')} subTitle={__('Add Request Type')} />}
      {salesflareConf.actionName === 'lead' && <TableCheckBox checked={salesflareConf?.selectedMarketSegment || false} onChange={(e) => actionHandler(e, 'MarketSegment')} className="wdt-200 mt-4 mr-2" value="MarketSegment" title={__('Add Market Segment', 'bit - integrations')} subTitle={__('Add Market Segment')} />}
      {salesflareConf.actionName === 'contact' && <TableCheckBox checked={salesflareConf?.selectedContactStatus || false} onChange={(e) => actionHandler(e, 'ContactStatus')} className="wdt-200 mt-4 mr-2" value="ContactStatus" title={__('Add Status', 'bit - integrations')} subTitle={__('Add Status')} />}

      <ConfirmModal
        className="custom-conf-mdl"
        mainMdlCls="o-v"
        btnClass="blue"
        btnTxt={__('Ok', 'bit-integrations')}
        show={actionMdl.show === 'tags'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Add Tags', 'bit-integrations')}
      >
        <div className="btcd-hr mt-2 mb-2" />
        <div className="mt-2">
          {__('Select Tags', 'bit-integrations')}
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
                  options={salesflareConf?.tags?.map(tag => ({ label: tag, value: tag }))}
                  className="msl-wrp-options"
                  defaultValue={salesflareConf?.selectedLeadSource}
                  onChange={val => setChanges(val, 'selectedLeadSource')}
                />
              </div>
            )
        }
      </ConfirmModal>

      <ConfirmModal
        className="custom-conf-mdl"
        mainMdlCls="o-v"
        btnClass="blue"
        btnTxt={__('Ok', 'bit-integrations')}
        show={actionMdl.show === 'LeadAddressType'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Add Address Type', 'bit-integrations')}
      >
        <div className="btcd-hr mt-2 mb-2" />
        <div className="mt-2">
          {__('Select Address Type', 'bit-integrations')}
        </div>

        <div className="flx flx-between mt-2">
          <MultiSelect
            options={salesflareConf?.LeadAddressTypes?.map(type => ({ label: type, value: type }))}
            className="msl-wrp-options"
            defaultValue={salesflareConf?.selectedLeadAddressType}
            onChange={val => setChanges(val, 'selectedLeadAddressType')}
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
        show={actionMdl.show === 'LeadType'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Add Lead Type', 'bit-integrations')}
      >
        <div className="btcd-hr mt-2 mb-2" />
        <div className="mt-2">
          {__('Select Lead Type', 'bit-integrations')}
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
                  options={salesflareConf?.LeadTypes?.map(type => ({ label: type, value: type }))}
                  className="msl-wrp-options"
                  defaultValue={salesflareConf?.selectedLeadType}
                  onChange={val => setChanges(val, 'selectedLeadType')}
                  singleSelect
                  closeOnSelect
                />
              </div>
            )
        }
      </ConfirmModal>

      <ConfirmModal
        className="custom-conf-mdl"
        mainMdlCls="o-v"
        btnClass="blue"
        btnTxt={__('Ok', 'bit-integrations')}
        show={actionMdl.show === 'RequestType'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Add Request Type', 'bit-integrations')}
      >
        <div className="btcd-hr mt-2 mb-2" />
        <div className="mt-2">
          {__('Select Request Type', 'bit-integrations')}
        </div>


        <div className="flx flx-between mt-2">
          <MultiSelect
            options={salesflareConf?.RequestTypes?.map(type => ({ label: type, value: type }))}
            className="msl-wrp-options"
            defaultValue={salesflareConf?.selectedRequestType}
            onChange={val => setChanges(val, 'selectedRequestType')}
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
        show={actionMdl.show === 'MarketSegment'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Add Market Segment', 'bit-integrations')}
      >
        <div className="btcd-hr mt-2 mb-2" />
        <div className="mt-2">
          {__('Select Market Segment', 'bit-integrations')}
        </div>


        <div className="flx flx-between mt-2">
          <MultiSelect
            options={salesflareConf?.MarketSegments?.map(segment => ({ label: segment, value: segment }))}
            className="msl-wrp-options"
            defaultValue={salesflareConf?.selectedMarketSegment}
            onChange={val => setChanges(val, 'selectedMarketSegment')}
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
        show={actionMdl.show === 'ContactStatus'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Add Industry', 'bit-integrations')}
      >
        <div className="btcd-hr mt-2 mb-2" />
        <div className="mt-2">
          {__('Select Industry', 'bit-integrations')}
        </div>


        <div className="flx flx-between mt-2">
          <MultiSelect
            options={salesflareConf?.ContactStatus?.map(status => ({ label: status, value: status }))}
            className="msl-wrp-options"
            defaultValue={salesflareConf?.selectedContactStatus}
            onChange={val => setChanges(val, 'selectedContactStatus')}
            singleSelect
            closeOnSelect
          />
        </div>
      </ConfirmModal>
    </div>
  )
}

