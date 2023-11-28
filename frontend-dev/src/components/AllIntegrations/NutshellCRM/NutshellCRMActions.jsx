/* eslint-disable no-param-reassign */

import { useState } from 'react'
import MultiSelect from 'react-multiple-select-dropdown-lite'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import { __ } from '../../../Utils/i18nwrap'
import ConfirmModal from '../../Utilities/ConfirmModal'
import TableCheckBox from '../../Utilities/TableCheckBox'
import { getAllCompanyTypes, getAllContacts } from './NutshellCRMCommonFunc'
import Loader from '../../Loaders/Loader'

export default function NutshellCRMActions({ nutshellCRMConf, setNutshellCRMConf, loading, setLoading }) {
  const [actionMdl, setActionMdl] = useState({ show: false, action: () => { } })

  const actionHandler = (e, type) => {
    const newConf = { ...nutshellCRMConf }
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
        newConf.leadSources = ['Advertisement', 'Campaign', 'Cold Calling', "People's Vendor", 'Exhibition', 'Existing Cutomer', 'Mass Mailing', 'Reference', 'Supplier Reference', 'Walk In']
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
    } else if (type === 'CompanyStatus') {
      if (e.target?.checked) {
        newConf.actions.CompanyStatus = true
        newConf.CompanyStatus = ['Passive', 'Open', 'Replied']
      } else {
        delete newConf.actions.CompanyStatus
      }
    } else if (type === 'Contact') {
      if (e.target?.checked) {
        getAllContacts(nutshellCRMConf, setNutshellCRMConf, setLoading)
        newConf.actions.Contact = true

      } else {
        delete newConf.actions.Contact
      }
    } else if (type === 'CompanyType') {
      if (e.target?.checked) {
        getAllCompanyTypes(nutshellCRMConf, setNutshellCRMConf, setLoading)
        newConf.actions.CompanyType = true

      } else {
        delete newConf.actions.CompanyType
      }
    }

    setActionMdl({ show: type })
    setNutshellCRMConf({ ...newConf })
  }

  const clsActionMdl = () => {
    setActionMdl({ show: false })
  }

  const setChanges = (val, name) => {
    const newConf = { ...nutshellCRMConf }
    newConf[name] = val
    setNutshellCRMConf({ ...newConf })
  }

  return (
    <div className="pos-rel d-flx flx-wrp">
      {nutshellCRMConf.actionName === 'lead' && <TableCheckBox checked={nutshellCRMConf.actions?.organizationLead || false} onChange={(e) => actionHandler(e, 'organizationLead')} className="wdt-200 mt-4 mr-2" value="organizationLead" title={__('Lead is an Organization', 'bit-integrations')} subTitle={__('Lead is an Organization', 'bit-integrations')} />}
      {nutshellCRMConf.actionName === 'lead' && <TableCheckBox checked={nutshellCRMConf?.selectedLeadSource || false} onChange={(e) => actionHandler(e, 'leadSource')} className="wdt-200 mt-4 mr-2" value="leadSource" title={__('Add Source', 'bit - integrations')} subTitle={__('Add Source')} />}
      {nutshellCRMConf.actionName === 'lead' && <TableCheckBox checked={nutshellCRMConf?.selectedLeadAddressType || false} onChange={(e) => actionHandler(e, 'LeadAddressType')} className="wdt-200 mt-4 mr-2" value="LeadAddressType" title={__('Add Address Type', 'bit - integrations')} subTitle={__('Add Address Type')} />}
      {nutshellCRMConf.actionName === 'lead' && <TableCheckBox checked={nutshellCRMConf?.selectedLeadType || false} onChange={(e) => actionHandler(e, 'LeadType')} className="wdt-200 mt-4 mr-2" value="LeadType" title={__('Add Lead Type', 'bit - integrations')} subTitle={__('Add Lead Type')} />}
      {nutshellCRMConf.actionName === 'lead' && <TableCheckBox checked={nutshellCRMConf?.selectedRequestType || false} onChange={(e) => actionHandler(e, 'RequestType')} className="wdt-200 mt-4 mr-2" value="RequestType" title={__('Add Request Type', 'bit - integrations')} subTitle={__('Add Request Type')} />}
      {nutshellCRMConf.actionName === 'lead' && <TableCheckBox checked={nutshellCRMConf?.selectedMarketSegment || false} onChange={(e) => actionHandler(e, 'MarketSegment')} className="wdt-200 mt-4 mr-2" value="MarketSegment" title={__('Add Market Segment', 'bit - integrations')} subTitle={__('Add Market Segment')} />}
      {/* {nutshellCRMConf.actionName === 'company' && <TableCheckBox checked={nutshellCRMConf?.selectedCompanyStatus || false} onChange={(e) => actionHandler(e, 'CompanyStatus')} className="wdt-200 mt-4 mr-2" value="CompanyStatus" title={__('Add Status', 'bit - integrations')} subTitle={__('Add Status')} />} */}
      {nutshellCRMConf.actionName === 'company' && <TableCheckBox checked={nutshellCRMConf?.selectedContact || false} onChange={(e) => actionHandler(e, 'Contact')} className="wdt-200 mt-4 mr-2" value="Contact" title={__('Add Contact', 'bit - integrations')} subTitle={__('Add Contact')} />}
      {nutshellCRMConf.actionName === 'company' && <TableCheckBox checked={nutshellCRMConf?.selectedCompanyType || false} onChange={(e) => actionHandler(e, 'CompanyType')} className="wdt-200 mt-4 mr-2" value="CompanyType" title={__('Add CompanyType', 'bit - integrations')} subTitle={__('Add CompanyType')} />}

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
            options={nutshellCRMConf?.leadSources?.map(source => ({ label: source, value: source }))}
            className="msl-wrp-options"
            defaultValue={nutshellCRMConf?.selectedLeadSource}
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
            options={nutshellCRMConf?.LeadAddressTypes?.map(type => ({ label: type, value: type }))}
            className="msl-wrp-options"
            defaultValue={nutshellCRMConf?.selectedLeadAddressType}
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
            options={nutshellCRMConf?.LeadTypes?.map(type => ({ label: type, value: type }))}
            className="msl-wrp-options"
            defaultValue={nutshellCRMConf?.selectedLeadType}
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
            options={nutshellCRMConf?.RequestTypes?.map(type => ({ label: type, value: type }))}
            className="msl-wrp-options"
            defaultValue={nutshellCRMConf?.selectedRequestType}
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
            options={nutshellCRMConf?.MarketSegments?.map(segment => ({ label: segment, value: segment }))}
            className="msl-wrp-options"
            defaultValue={nutshellCRMConf?.selectedMarketSegment}
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
        show={actionMdl.show === 'CompanyStatus'}
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
            options={nutshellCRMConf?.CompanyStatus?.map(status => ({ label: status, value: status }))}
            className="msl-wrp-options"
            defaultValue={nutshellCRMConf?.selectedCompanyStatus}
            onChange={val => setChanges(val, 'selectedCompanyStatus')}
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
        show={actionMdl.show === 'Contact'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Add Contact', 'bit-integrations')}
      >
        <div className="btcd-hr mt-2 mb-2" />
        <div className="mt-2">
          {__('Select Contact', 'bit-integrations')}
        </div>

        {loading.contacts ? (
          <Loader style={{
            display: 'flex',
            justifyContent: 'center',
            alignItems: 'center',
            height: 45,
            transform: 'scale(0.5)',
          }}
          />
        )
          : (<div className="flx flx-between mt-2">
            <MultiSelect
              options={nutshellCRMConf?.contacts?.map(contact => ({ label: contact.name, value: contact.id }))}
              className="msl-wrp-options"
              defaultValue={nutshellCRMConf?.selectedContact}
              onChange={val => setChanges(val, 'selectedContact')}
              singleSelect
              closeOnSelect
            />
          </div>)}
      </ConfirmModal>

      <ConfirmModal
        className="custom-conf-mdl"
        mainMdlCls="o-v"
        btnClass="blue"
        btnTxt={__('Ok', 'bit-integrations')}
        show={actionMdl.show === 'CompanyType'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Add CompanyType', 'bit-integrations')}
      >
        <div className="btcd-hr mt-2 mb-2" />
        <div className="mt-2">
          {__('Select CompanyType', 'bit-integrations')}
        </div>

        {loading.companyTypes ? (
          <Loader style={{
            display: 'flex',
            justifyContent: 'center',
            alignItems: 'center',
            height: 45,
            transform: 'scale(0.5)',
          }}
          />
        )
          : (<div className="flx flx-between mt-2">
            <MultiSelect
              options={nutshellCRMConf?.companyTypes?.map(companyType => ({ label: companyType.name, value: companyType.id }))}
              className="msl-wrp-options"
              defaultValue={nutshellCRMConf?.selectedCompanyType}
              onChange={val => setChanges(val, 'selectedCompanyType')}
              singleSelect
              closeOnSelect
            />
          </div>)}
      </ConfirmModal>
    </div>
  )
}

