/* eslint-disable no-param-reassign */

import { useState } from 'react'
import MultiSelect from 'react-multiple-select-dropdown-lite'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import { __ } from '../../../Utils/i18nwrap'
import Loader from '../../Loaders/Loader'
import ConfirmModal from '../../Utilities/ConfirmModal'
import TableCheckBox from '../../Utilities/TableCheckBox'
import { getAllStaffs } from './OneHashCRMCommonFunc'

export default function OneHashCRMActions({ oneHashCRMConf, setOneHashCRMConf, loading, setLoading }) {
  const [actionMdl, setActionMdl] = useState({ show: false, action: () => { } })

  const actionHandler = (e, type) => {
    const newConf = { ...oneHashCRMConf }
    if (type === 'organizationLead') {
      if (e.target?.checked) {
        newConf.actions.organizationLead = true
      } else {
        setActionMdl({ show: false })
        delete newConf.actions.organizationLead
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
    } else if (type === 'Territory') {
      if (e.target?.checked) {
        newConf.actions.Territory = true
        newConf.Territories = ['All Territories', 'Rest Of The World']
      } else {
        delete newConf.actions.Territory
      }
    } else if (type === 'LeadIndustry') {
      if (e.target?.checked) {
        newConf.actions.LeadIndustry = true
        newConf.LeadIndustries = ['Motion Picture & Video', 'Music', 'Newspaper Publishers', 'Online Auctions', 'Pension Funds', 'Pharmaceuticals', 'Private Equity', 'Publishing', 'Real State', 'Retail & Wholesale', 'Securities & Commodity Exchanges', 'Service', 'Soup & Detergent', 'Software', 'Sports', 'Technology', 'Telecommunications', 'Television', 'Transportation', 'Venture Capital']
      } else {
        delete newConf.actions.LeadIndustry
      }
    }

    setActionMdl({ show: type })
    setOneHashCRMConf({ ...newConf })
  }

  const clsActionMdl = () => {
    setActionMdl({ show: false })
  }

  const setChanges = (val, name) => {
    const newConf = { ...oneHashCRMConf }
    newConf[name] = val
    setOneHashCRMConf({ ...newConf })
  }

  return (
    <div className="pos-rel d-flx flx-wrp">
      {oneHashCRMConf.actionName === 'lead' && <TableCheckBox checked={oneHashCRMConf.actions?.organizationLead || false} onChange={(e) => actionHandler(e, 'organizationLead')} className="wdt-200 mt-4 mr-2" value="organizationLead" title={__('Lead is an Organization', 'bit-integrations')} subTitle={__('Lead is an Organization', 'bit-integrations')} />}
      {oneHashCRMConf.actionName === 'lead' && <TableCheckBox checked={oneHashCRMConf?.selectedLeadSource || false} onChange={(e) => actionHandler(e, 'leadSource')} className="wdt-200 mt-4 mr-2" value="leadSource" title={__('Add Source', 'bit - integrations')} subTitle={__('Add Source')} />}
      {oneHashCRMConf.actionName === 'lead' && <TableCheckBox checked={oneHashCRMConf?.selectedLeadAddressType || false} onChange={(e) => actionHandler(e, 'LeadAddressType')} className="wdt-200 mt-4 mr-2" value="LeadAddressType" title={__('Add Address Type', 'bit - integrations')} subTitle={__('Add Address Type')} />}
      {oneHashCRMConf.actionName === 'lead' && <TableCheckBox checked={oneHashCRMConf?.selectedLeadType || false} onChange={(e) => actionHandler(e, 'LeadType')} className="wdt-200 mt-4 mr-2" value="LeadType" title={__('Add Lead Type', 'bit - integrations')} subTitle={__('Add Lead Type')} />}
      {oneHashCRMConf.actionName === 'lead' && <TableCheckBox checked={oneHashCRMConf?.selectedRequestType || false} onChange={(e) => actionHandler(e, 'RequestType')} className="wdt-200 mt-4 mr-2" value="RequestType" title={__('Add Request Type', 'bit - integrations')} subTitle={__('Add Request Type')} />}
      {oneHashCRMConf.actionName === 'lead' && <TableCheckBox checked={oneHashCRMConf?.selectedMarketSegment || false} onChange={(e) => actionHandler(e, 'MarketSegment')} className="wdt-200 mt-4 mr-2" value="MarketSegment" title={__('Add Market Segment', 'bit - integrations')} subTitle={__('Add Market Segment')} />}
      {oneHashCRMConf.actionName === 'lead' && <TableCheckBox checked={oneHashCRMConf?.selectedLeadIndustry || false} onChange={(e) => actionHandler(e, 'LeadIndustry')} className="wdt-200 mt-4 mr-2" value="LeadIndustry" title={__('Add Industry', 'bit - integrations')} subTitle={__('Add Industry')} />}
      {oneHashCRMConf.actionName === 'lead' && <TableCheckBox checked={oneHashCRMConf?.selectedTerritory || false} onChange={(e) => actionHandler(e, 'Territory')} className="wdt-200 mt-4 mr-2" value="Territory" title={__('Add Territory', 'bit - integrations')} subTitle={__('Add Territory')} />}
      {/* {oneHashCRMConf.actionName === 'contact' && <TableCheckBox checked={oneHashCRMConf?.selectedPermission?.length || false} onChange={(e) => actionHandler(e, 'permission')} className="wdt-200 mt-4 mr-2" value="permission" title={__('Add Permissions', 'bit - integrations')} subTitle={__('Add Permissions for this contact')} />}
      {oneHashCRMConf.actionName === 'contact' && <TableCheckBox checked={oneHashCRMConf.actions?.contactIsPrimary || false} onChange={(e) => actionHandler(e, 'contactIsPrimary')} className="wdt-200 mt-4 mr-2" value="contactIsPrimary" title={__('Is it Primary Contact?', 'bit-integrations')} subTitle={__('Is it Primary Contact?', 'bit-integrations')} />}
      {oneHashCRMConf.actionName === 'project' && <TableCheckBox checked={oneHashCRMConf?.selectedProjectMembers || false} onChange={(e) => actionHandler(e, 'projectMembers')} className="wdt-200 mt-4 mr-2" value="projectMembers" title={__('Add Project Members', 'bit-integrations')} subTitle={__('Add Project Members', 'bit-integrations')} />} */}

      <ConfirmModal
        className="custom-conf-mdl"
        mainMdlCls="o-v"
        btnClass="blue"
        btnTxt={__('Ok', 'bit-integrations')}
        show={actionMdl.show === 'leadSource'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Add Source', 'bit-integrations')}
      >
        <div className="btcd-hr mt-2 mb-2" />
        <div className="mt-2">
          {__('Select Source', 'bit-integrations')}
        </div>

        <div className="flx flx-between mt-2">
          <MultiSelect
            options={oneHashCRMConf?.leadSources?.map(source => ({ label: source, value: source }))}
            className="msl-wrp-options"
            defaultValue={oneHashCRMConf?.selectedLeadSource}
            onChange={val => setChanges(val, 'selectedLeadSource')}
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
            options={oneHashCRMConf?.LeadAddressTypes?.map(type => ({ label: type, value: type }))}
            className="msl-wrp-options"
            defaultValue={oneHashCRMConf?.selectedLeadAddressType}
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


        <div className="flx flx-between mt-2">
          <MultiSelect
            options={oneHashCRMConf?.LeadTypes?.map(type => ({ label: type, value: type }))}
            className="msl-wrp-options"
            defaultValue={oneHashCRMConf?.selectedLeadType}
            onChange={val => setChanges(val, 'selectedLeadType')}
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
            options={oneHashCRMConf?.RequestTypes?.map(type => ({ label: type, value: type }))}
            className="msl-wrp-options"
            defaultValue={oneHashCRMConf?.selectedRequestType}
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
            options={oneHashCRMConf?.MarketSegments?.map(segment => ({ label: segment, value: segment }))}
            className="msl-wrp-options"
            defaultValue={oneHashCRMConf?.selectedMarketSegment}
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
        show={actionMdl.show === 'Territory'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Add Territory', 'bit-integrations')}
      >
        <div className="btcd-hr mt-2 mb-2" />
        <div className="mt-2">
          {__('Select Territory', 'bit-integrations')}
        </div>


        <div className="flx flx-between mt-2">
          <MultiSelect
            options={oneHashCRMConf?.Territories?.map(Territory => ({ label: Territory, value: Territory }))}
            className="msl-wrp-options"
            defaultValue={oneHashCRMConf?.selectedTerritory}
            onChange={val => setChanges(val, 'selectedTerritory')}
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
        show={actionMdl.show === 'LeadIndustry'}
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
            options={oneHashCRMConf?.LeadIndustries?.map(industry => ({ label: industry, value: industry }))}
            className="msl-wrp-options"
            defaultValue={oneHashCRMConf?.selectedLeadIndustry}
            onChange={val => setChanges(val, 'selectedLeadIndustry')}
            singleSelect
            closeOnSelect
          />
        </div>
      </ConfirmModal>
    </div>
  )
}

