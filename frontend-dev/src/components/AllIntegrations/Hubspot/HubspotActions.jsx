/* eslint-disable no-unused-vars */
/* eslint-disable no-param-reassign */

import { useState } from 'react'
import MultiSelect from 'react-multiple-select-dropdown-lite'
import { __ } from '../../../Utils/i18nwrap'
import Loader from '../../Loaders/Loader'
import ConfirmModal from '../../Utilities/ConfirmModal'
import TableCheckBox from '../../Utilities/TableCheckBox'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import { getAllCompany, getAllContacts, getAllIndustry, getAllOwners } from './HubspotCommonFunc'
import { useRecoilValue } from 'recoil'
import { $btcbi } from '../../../GlobalStates'

export default function HubspotActions({ hubspotConf, setHubspotConf, formFields, loading, setLoading }) {
  const [actionMdl, setActionMdl] = useState({ show: false, action: () => { } })
  const btcbi = useRecoilValue($btcbi)
  const { isPro } = btcbi

  const actionHandler = (e, type) => {
    const newConf = { ...hubspotConf }
    if (type === 'contact_owner') {
      getAllOwners(hubspotConf, setHubspotConf, setLoading)
      if (e.target.checked) {
        newConf.actions.contact_owner = true
      } else {
        delete newConf.actions.contact_owner
      }
    } else if (type === 'contact') {
      getAllContacts(hubspotConf, setHubspotConf, setLoading)
      if (e.target.checked) {
        newConf.actions.contact = true
      } else {
        delete newConf.actions.contact
      }
    } else if (type === 'company') {
      getAllCompany(hubspotConf, setHubspotConf, setLoading)
      if (e.target.checked) {
        newConf.actions.company = true
      } else {
        delete newConf.actions.company
      }
    } else if (type === 'deal_type') {
      if (e.target.checked) {
        newConf.actions.deal_type = true
      } else {
        delete newConf.actions.deal_type
      }
    } else if (type === 'priority') {
      if (e.target.checked) {
        newConf.actions.priority = true
      } else {
        delete newConf.actions.priority
      }
    } else if (type === 'lifecycle_stage') {
      if (e.target.checked) {
        newConf.actions.lifecycle_stage = true
      } else {
        delete newConf.actions.lifecycle_stage
      }
    } else if (type === 'lead_status') {
      if (e.target.checked) {
        newConf.actions.lead_status = true
      } else {
        delete newConf.actions.lead_status
      }
    } else if (type === 'company_type') {
      if (e.target.checked) {
        newConf.actions.company_type = true
      } else {
        delete newConf.actions.company_type
      }
    } else if (type === 'industry') {
      if (e.target.checked) {
        newConf.actions.industry = true
        getAllIndustry(hubspotConf, setHubspotConf, setLoading)
      } else {
        delete newConf.actions.industry
      }
    } else if (type === 'update') {
      if (e.target.checked) {
        newConf.actions.update = true
      } else {
        delete newConf.actions.update
      }
    }

    setActionMdl({ show: type })
    setHubspotConf({ ...newConf })
  }

  const clsActionMdl = () => {
    setActionMdl({ show: false })
  }

  const setChanges = (val, type) => {
    const newConf = { ...hubspotConf }
    newConf[type] = val
    setHubspotConf({ ...newConf })
  }

  const lifecycleStage = [
    { value: 'subscriber', label: 'Subscriber' },
    { value: 'lead', label: 'Lead' },
    { value: 'marketingqualifiedlead', label: 'Marketing Qualified Lead' },
    { value: 'salesqualifiedlead', label: 'Sales Qualified Lead' },
    { value: 'opportunity', label: 'Opportunity' },
    { value: 'evangelist', label: 'Evangelist' },
    { value: 'other', label: 'Other' },
    { value: 'customer', label: 'Customer' },
  ]

  const leadStatus = [
    { value: 'OPEN', label: 'Open' },
    { value: 'NEW', label: 'New' },
    { value: 'IN_PROGRESS', label: 'In Progress' },
    { value: 'OPEN_DEAL', label: 'Open Deal' },
    { value: 'UNQUALIFIED', label: 'Unqualified' },
    { value: 'ATTEMPTED_TO_CONTACT', label: 'Attempted to contact' },
    { value: 'CONNECTED', label: 'Connected' },
    { value: 'BAD_TIMING', label: 'Bad timing' },
  ]

  const dealType = [
    { value: 'newbusiness', label: 'New Business' },
    { value: 'existingbusiness', label: 'Existing Business' },
  ]

  const priority = [
    { value: 'low', label: 'Low' },
    { value: 'medium', label: 'Medium' },
    { value: 'high', label: 'High' },
  ]

  const companyTypes = [
    { value: 'PROSPECT', label: 'Prospect' },
    { value: 'PARTNER', label: 'Partner' },
    { value: 'RESELLER', label: 'Reseller' },
    { value: 'VENDOR', label: 'Vendor' },
    { value: 'OTHER', label: 'Other' },
  ]

  const lifecycleStages = [
    { value: 'subscriber', label: 'Subscriber' },
    { value: 'lead', label: 'Lead' },
    { value: 'marketingqualifiedlead', label: 'Marketing Qualified Lead' },
    { value: 'salesqualifiedlead', label: 'Sales Qualified Lead' },
    { value: 'opportunity', label: 'Opportunity' },
    { value: 'customer', label: 'Customer' },
    { value: 'evangelist', label: 'Evangelist' },
    { value: 'other', label: 'Other' },
  ]

  return (

    <div className="pos-rel d-flx w-8">
      {hubspotConf?.actionName && <TableCheckBox checked={hubspotConf?.contact_owner?.length || false} onChange={(e) => actionHandler(e, 'contact_owner')} className="wdt-200 mt-4 mr-2" value="contact_owner" title={__('Contact Owner', 'bit-integrations')} subTitle={__('Add a contact owner', 'bit-integrations')} />}
      {(hubspotConf?.actionName === 'contact' || hubspotConf?.actionName === 'company') && <TableCheckBox checked={hubspotConf?.lifecycle_stage || false} onChange={(e) => actionHandler(e, 'lifecycle_stage')} className="wdt-200 mt-4 mr-2" value="lifecycle_stage" title={__('Lifecycle Stage', 'bit-integrations')} subTitle={__('Add a lifecycle stage', 'bit-integrations')} />}
      {(hubspotConf?.actionName === 'contact' || hubspotConf?.actionName === 'company') && <TableCheckBox checked={hubspotConf?.lead_status || false} onChange={(e) => actionHandler(e, 'lead_status')} className="wdt-200 mt-4 mr-2" value="lead_status" title={__('Lead Status', 'bit-integrations')} subTitle={__('Add lead status', 'bit-integrations')} />}
      {hubspotConf?.actionName === 'deal' && <TableCheckBox checked={hubspotConf?.contact || false} onChange={(e) => actionHandler(e, 'contact')} className="wdt-200 mt-4 mr-2" value="contact" title={__('Contact', 'bit-integrations')} subTitle={__('Associate deal with contacts', 'bit-integrations')} />}
      {hubspotConf?.actionName === 'deal' && <TableCheckBox checked={hubspotConf?.company || false} onChange={(e) => actionHandler(e, 'company')} className="wdt-200 mt-4 mr-2" value="company" title={__('Company', 'bit-integrations')} subTitle={__('Associate deal with company', 'bit-integrations')} />}
      {hubspotConf?.actionName === 'deal' && <TableCheckBox checked={hubspotConf?.deal_type || false} onChange={(e) => actionHandler(e, 'deal_type')} className="wdt-200 mt-4 mr-2" value="deal_type" title={__('Deal Type', 'bit-integrations')} subTitle={__('Add type to deal', 'bit-integrations')} />}
      {hubspotConf?.actionName !== 'contact' && hubspotConf?.actionName !== 'company' && <TableCheckBox checked={hubspotConf?.priority || false} onChange={(e) => actionHandler(e, 'priority')} className="wdt-200 mt-4 mr-2" value="deal_type" title={__('Priority', 'bit-integrations')} subTitle={__('Add priority', 'bit-integrations')} />}
      {hubspotConf?.actionName === 'company' && <TableCheckBox checked={hubspotConf?.company_type || false} onChange={(e) => actionHandler(e, 'company_type')} className="wdt-200 mt-4 mr-2" value="company_type" title={__('Type', 'bit-integrations')} subTitle={__('The optional classification of this company record - prospect, partner, etc.', 'bit-integrations')} />}
      {hubspotConf?.actionName === 'company' && <TableCheckBox checked={hubspotConf?.industry || false} onChange={(e) => actionHandler(e, 'industry')} className="wdt-200 mt-4 mr-2" value="industry" title={__('Industry', 'bit-integrations')} subTitle={__('The type of business the company performs. By default, this property has approximately 150 pre-defined options to select from.', 'bit-integrations')} />}
      {<TableCheckBox checked={hubspotConf?.actions?.update || false} onChange={(e) => actionHandler(e, 'update')} className="wdt-200 mt-4 mr-2" value="update" title={__(`Update ${hubspotConf?.actionName} ${!isPro ? '(Pro)' : ''}`, 'bit-integrations')} subTitle={__(`${isPro ? 'Update Record' : 'The Bit Integration Pro v(2.2.1) plugin needs to be installed and activated to enable the Upsert Record feature'}`, 'bit-integrations')} isInfo={!isPro} />}

      <ConfirmModal
        className="custom-conf-mdl"
        mainMdlCls="o-v"
        btnClass="purple"
        btnTxt={__('Ok', 'bit-integrations')}
        show={actionMdl.show === 'contact_owner'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Contact Owner', 'bit-integrations')}
      >
        <div className="btcd-hr mt-2 mb-2" />
        {loading.owners
          ? (
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
                className="msl-wrp-options"
                defaultValue={hubspotConf?.contact_owner}
                options={hubspotConf.default?.owners?.map(list => ({ label: list.ownerName, value: list.ownerId.toString() }))}
                onChange={val => setChanges(val, 'contact_owner')}
                customValue
                singleSelect
              />
              <button onClick={() => getAllOwners(hubspotConf, setHubspotConf, setLoading)} className="icn-btn sh-sm ml-2 mr-2 tooltip" style={{ '--tooltip-txt': `${__('Refresh Owners', 'bit-integrations')}'` }} type="button" disabled={loading.owners}>&#x21BB;</button>
            </div>
          )}
      </ConfirmModal>

      <ConfirmModal
        className="custom-conf-mdl"
        mainMdlCls="o-v"
        btnClass="purple"
        btnTxt={__('Ok', 'bit-integrations')}
        show={actionMdl.show === 'lifecycle_stage'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Lifecycle Stage', 'bit-integrations')}
      >
        <div className="btcd-hr mt-2 mb-2" />
        <div className="flx flx-center mt-2">
          <MultiSelect
            className="msl-wrp-options"
            defaultValue={hubspotConf?.lifecycle_stage}
            options={lifecycleStage?.map(list => ({ label: list.label, value: list.value.toString() }))}
            onChange={val => setChanges(val, 'lifecycle_stage')}
            customValue
            singleSelect
          />
        </div>
      </ConfirmModal>

      <ConfirmModal
        className="custom-conf-mdl"
        mainMdlCls="o-v"
        btnClass="purple"
        btnTxt={__('Ok', 'bit-integrations')}
        show={actionMdl.show === 'lead_status'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Lead Status', 'bit-integrations')}
      >
        <div className="btcd-hr mt-2 mb-2" />
        <div className="flx flx-center mt-2">
          <MultiSelect
            className="msl-wrp-options"
            defaultValue={hubspotConf?.lead_status}
            options={leadStatus?.map(list => ({ label: list.label, value: list.value.toString() }))}
            onChange={val => setChanges(val, 'lead_status')}
            customValue
            singleSelect
          />
        </div>
      </ConfirmModal>

      <ConfirmModal
        className="custom-conf-mdl"
        mainMdlCls="o-v"
        btnClass="purple"
        btnTxt={__('Ok', 'bit-integrations')}
        show={actionMdl.show === 'contact'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Contacts', 'bit-integrations')}
      >
        <div className="btcd-hr mt-2 mb-2" />
        {loading.contacts
          ? (
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
                className="msl-wrp-options"
                defaultValue={hubspotConf?.contact}
                options={hubspotConf?.default?.contacts?.map(list => ({ label: list.contactName, value: list.contactId.toString() }))}
                onChange={val => setChanges(val, 'contact')}
                customValue
              />
              <button onClick={() => getAllContacts(hubspotConf, setHubspotConf, setLoading)} className="icn-btn sh-sm ml-2 mr-2 tooltip" style={{ '--tooltip-txt': `${__('Refresh CRM Tags', 'bit-integrations')}'` }} type="button" disabled={loading.contacts}>&#x21BB;</button>
            </div>
          )}
      </ConfirmModal>

      <ConfirmModal
        className="custom-conf-mdl"
        mainMdlCls="o-v"
        btnClass="purple"
        btnTxt={__('Ok', 'bit-integrations')}
        show={actionMdl.show === 'company'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Company', 'bit-integrations')}
      >
        <div className="btcd-hr mt-2 mb-2" />
        {loading.companies
          ? (
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
                className="msl-wrp-options"
                defaultValue={hubspotConf?.company}
                options={hubspotConf?.default?.companies?.map(list => ({ label: list.companyName, value: list.companyId.toString() }))}
                onChange={val => setChanges(val, 'company')}
                customValue
              />
              <button onClick={() => getAllCompany(hubspotConf, setHubspotConf, setLoading)} className="icn-btn sh-sm ml-2 mr-2 tooltip" style={{ '--tooltip-txt': `${__('Refresh CRM Tags', 'bit-integrations')}'` }} type="button" disabled={loading.companies}>&#x21BB;</button>
            </div>
          )}
      </ConfirmModal>

      <ConfirmModal
        className="custom-conf-mdl"
        mainMdlCls="o-v"
        btnClass="purple"
        btnTxt={__('Ok', 'bit-integrations')}
        show={actionMdl.show === 'deal_type'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Deal Type', 'bit-integrations')}
      >
        <div className="btcd-hr mt-2 mb-2" />
        <div className="flx flx-center mt-2">
          <MultiSelect
            className="msl-wrp-options"
            defaultValue={hubspotConf?.deal_type}
            options={dealType?.map(list => ({ label: list.label, value: list.value.toString() }))}
            onChange={val => setChanges(val, 'deal_type')}
            customValue
            singleSelect
          />
        </div>
      </ConfirmModal>

      <ConfirmModal
        className="custom-conf-mdl"
        mainMdlCls="o-v"
        btnClass="purple"
        btnTxt={__('Ok', 'bit-integrations')}
        show={actionMdl.show === 'priority'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Priority', 'bit-integrations')}
      >
        <div className="btcd-hr mt-2 mb-2" />
        <div className="flx flx-center mt-2">
          <MultiSelect
            className="msl-wrp-options"
            defaultValue={hubspotConf?.priority}
            options={priority?.map(list => ({ label: list.label, value: list.value.toString() }))}
            onChange={val => setChanges(val, 'priority')}
            customValue
            singleSelect
          />
        </div>
      </ConfirmModal>

      <ConfirmModal
        className="custom-conf-mdl"
        mainMdlCls="o-v"
        btnClass="blue"
        btnTxt={__('Ok', 'bit-integrations')}
        show={actionMdl.show === 'company_type'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Type', 'bit-integrations')}
      >
        <div className="btcd-hr mt-2 mb-2" />
        <div className="flx flx-center mt-2">
          <MultiSelect
            className="msl-wrp-options"
            defaultValue={hubspotConf?.company_type}
            options={companyTypes?.map(list => ({ label: list.label, value: list.value }))}
            onChange={val => setChanges(val, 'company_type')}
            customValue
            singleSelect
          />
        </div>
      </ConfirmModal>

      <ConfirmModal
        className="custom-conf-mdl"
        mainMdlCls="o-v"
        btnClass="blue"
        btnTxt={__('Ok', 'bit-integrations')}
        show={actionMdl.show === 'industry'}
        close={clsActionMdl}
        action={clsActionMdl}
        title={__('Industry', 'bit-integrations')}
      >
        <div className="btcd-hr mt-2 mb-2" />
        {loading.industry
          ? (
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
            <div className="flx flx-center mt-2">
              <MultiSelect
                className="msl-wrp-options"
                defaultValue={hubspotConf?.industry}
                options={hubspotConf?.industries?.map(list => ({ label: list.label, value: list.value }))}
                onChange={val => setChanges(val, 'industry')}
                customValue
                singleSelect
              />
              <button onClick={() => getAllIndustry(hubspotConf, setHubspotConf, setLoading)} className="icn-btn sh-sm ml-2 mr-2 tooltip" style={{ '--tooltip-txt': `${__('Refresh Industry', 'bit-integrations')}'` }} type="button" disabled={loading.industry}>&#x21BB;</button>
            </div>
          )}
      </ConfirmModal>
    </div>
  )
}
