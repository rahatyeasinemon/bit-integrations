/* eslint-disable no-unused-expressions */
import { useEffect, useState } from 'react'
import toast from 'react-hot-toast'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import { useNavigate, useParams } from 'react-router-dom'
import BackIcn from '../../../Icons/BackIcn'
import { __ } from '../../../Utils/i18nwrap'
import SnackMsg from '../../Utilities/SnackMsg'
import Steps from '../../Utilities/Steps'
import { saveActionConf, setGrantTokenResponse } from '../IntegrationHelpers/IntegrationHelpers'
import { checkMappedFields, handleInput } from './SalesforceCommonFunc'
import SelesforceIntegLayout from './SalesforceIntegLayout'
import IntegrationStepThree from '../IntegrationHelpers/IntegrationStepThree'
import SalesforceAuthorization from './SalesforceAuthorization'

function Salesforce({ formFields, setFlow, flow, allIntegURL }) {
  const navigate = useNavigate()
  const { formID } = useParams()
  const [isLoading, setIsLoading] = useState(false)
  const [step, setStep] = useState(1)
  const [snack, setSnackbar] = useState({ show: false })

  const contactFields = [
    { key: 'LastName', label: __('Last Name', 'bit-integrations'), required: true },
    { key: 'FirstName', label: __('First Name', 'bit-integrations'), required: false },
    { key: 'Email', label: __('Email', 'bit-integrations'), required: false },
    { key: 'Salutation', label: __('Salutation', 'bit-integrations'), required: false },
    { key: 'OtherStreet', label: __('Other Street', 'bit-integrations'), required: false },
    { key: 'OtherCity', label: __('Other City', 'bit-integrations'), required: false },
    { key: 'Title', label: __('Title', 'bit-integrations'), required: false },
    { key: 'Department', label: __('Department', 'bit-integrations'), required: false },
    { key: 'MailingStreet', label: __('Mailing Street', 'bit-integrations'), required: false },
    { key: 'MailingCity', label: __('Mailing City', 'bit-integrations'), required: false },
    { key: 'MailingCountry', label: __('Mailing Country', 'bit-integrations'), required: false }
  ]
  const leadFields = [
    { key: 'LastName', label: __('Last Name', 'bit-integrations'), required: true },
    { key: 'Email', label: __('Email', 'bit-integrations'), required: true },
    { key: 'Company', label: __('Company', 'bit-integrations'), required: true },
    { key: 'FirstName', label: __('First Name', 'bit-integrations'), required: false },
    { key: 'City', label: __('City', 'bit-integrations'), required: false },
    { key: 'State', label: __('State', 'bit-integrations'), required: false },
    { key: 'Salutation', label: __('Salutation', 'bit-integrations'), required: false },
    { key: 'Title', label: __('Title', 'bit-integrations'), required: false },
    { key: 'URL', label: __('URL', 'bit-integrations'), required: false },
    { key: 'Phone', label: __('Phone', 'bit-integrations'), required: false },
    { key: 'Mobile', label: __('Mobile', 'bit-integrations'), required: false },
    { key: 'Fax', label: __('Fax', 'bit-integrations'), required: false },
    { key: 'Street', label: __('Street', 'bit-integrations'), required: false },
    { key: 'PostalCode', label: __('Zip/Postal Code', 'bit-integrations'), required: false },
    { key: 'Country', label: __('Country', 'bit-integrations'), required: false },
    { key: 'Description', label: __('Description', 'bit-integrations'), required: false },
    { key: 'LeadSource', label: __('Lead source', 'bit-integrations'), required: false },
    { key: 'Industry', label: __('Industry', 'bit-integrations'), required: false },
    { key: 'Rating', label: __('Rating', 'bit-integrations'), required: false },
    { key: 'Revenue', label: __('Revenue', 'bit-integrations'), required: false },
    { key: 'Employees', label: __('Employees', 'bit-integrations'), required: false }
  ]

  const accountFields = [
    { key: 'Name', label: __('Account Name', 'bit-integrations'), required: true },
    { key: 'Phone', label: __('Phone', 'bit-integrations'), required: false },
    { key: 'BillingStreet', label: __('Billing Street', 'bit-integrations'), required: false },
    { key: 'BillingCity', label: __('Billing City', 'bit-integrations'), required: false },
    { key: 'BillingState', label: __('Billing State', 'bit-integrations'), required: false },
    {
      key: 'BillingPostalCode',
      label: __('Billing Postal Code', 'bit-integrations'),
      required: false
    },
    { key: 'BillingCountry', label: __('Billing Country', 'bit-integrations'), required: false },
    { key: 'ShippingStreet', label: __('Shipping Street', 'bit-integrations'), required: false },
    { key: 'ShippingCity', label: __('Shipping City', 'bit-integrations'), required: false },
    { key: 'ShippingState', label: __('Shipping State', 'bit-integrations'), required: false },
    {
      key: 'ShippingPostalCode',
      label: __('Shipping Postal Code', 'bit-integrations'),
      required: false
    },
    { key: 'ShippingCountry', label: __('Shipping Country', 'bit-integrations'), required: false },
    { key: 'Website', label: __('Website', 'bit-integrations'), required: false },
    { key: 'Industry', label: __('Industry', 'bit-integrations'), required: false },
    {
      key: 'NumberOfEmployees',
      label: __('Number Of Employees', 'bit-integrations'),
      required: false
    },
    { key: 'Description', label: __('Description', 'bit-integrations'), required: false },
    { key: 'Rating', label: __('Rating', 'bit-integrations'), required: false },
    { key: 'Site', label: __('Site', 'bit-integrations'), required: false },
    { key: 'CleanStatus', label: __('Clean Status', 'bit-integrations'), required: false },
    { key: 'NaicsDesc', label: __('Naics Desc', 'bit-integrations'), required: false },
    { key: 'SicDesc', label: __('Sic Desc', 'bit-integrations'), required: false },
    { key: 'Fax', label: __('Fax', 'bit-integrations'), required: false },
    { key: 'TickerSymbol', label: __('Ticker Symbol', 'bit-integrations'), required: false },
    { key: 'AnnualRevenue', label: __('Annual Revenue', 'bit-integrations'), required: false }
  ]

  const campaignFields = [
    { key: 'Name', label: __('Name', 'bit-integrations'), required: true },
    { key: 'Type', label: __('Type', 'bit-integrations'), required: false },
    { key: 'Description', label: __('Description', 'bit-integrations'), required: false },
    { key: 'Status', label: __('Status', 'bit-integrations'), required: false },
    { key: 'StartDate', label: __('StartDate', 'bit-integrations'), required: false },
    { key: 'EndDate', label: __('EndDate', 'bit-integrations'), required: false },
    { key: 'ExpectedRevenue', label: __('ExpectedRevenue', 'bit-integrations'), required: false },
    { key: 'BudgetedCost', label: __('BudgetedCost', 'bit-integrations'), required: false },
    { key: 'ActualCost', label: __('ActualCost', 'bit-integrations'), required: false }
  ]

  const campaignMemberStatus = [
    { label: __('Planned', 'bit-integrations'), value: 'Planned' },
    { label: __('Received', 'bit-integrations'), value: 'Received' },
    { label: __('Responded', 'bit-integrations'), value: 'Responded' },
    { label: __('Sent', 'bit-integrations'), value: 'Sent' }
  ]

  const opportunityFields = [
    { key: 'Name', label: __('Name', 'bit-integrations'), required: true },
    { key: 'CloseDate', label: __('Close Date', 'bit-integrations'), required: true },
    { key: 'Description', label: __('Description', 'bit-integrations'), required: false },
    { key: 'Amount', label: __('Amount', 'bit-integrations'), required: false }
  ]

  const eventFields = [
    { key: 'StartDateTime', label: __('Start Date', 'bit-integrations'), required: true },
    { key: 'EndDateTime', label: __('End Date', 'bit-integrations'), required: true },
    { key: 'Location', label: __('Location', 'bit-integrations'), required: false },
    { key: 'Description', label: __('Description', 'bit-integrations'), required: false }
  ]

  const caseFields = [
    { key: 'SuppliedName', label: __('Name', 'bit-integrations'), required: true },
    { key: 'SuppliedEmail', label: __('Email', 'bit-integrations'), required: false },
    { key: 'SuppliedPhone', label: __('Phone', 'bit-integrations'), required: false },
    { key: 'SuppliedCompany', label: __('Company', 'bit-integrations'), required: false },
    { key: 'Subject', label: __('Subject', 'bit-integrations'), required: false },
    { key: 'Description', label: __('Description', 'bit-integrations'), required: false }
  ]

  const action_modules = [
    { label: __('Create Contact', 'bit-integrations'), value: 'contact-create' },
    { label: __('Create lead', 'bit-integrations'), value: 'lead-create' },
    { label: __('Create Account', 'bit-integrations'), value: 'account-create' },
    { label: __('Create Campaign', 'bit-integrations'), value: 'campaign-create' },
    { label: __('Add campaign member', 'bit-integrations'), value: 'add-campaign-member' },
    { label: __('Create Task', 'bit-integrations'), value: 'task-create' },
    { label: __('Oportunity Create', 'bit-integrations'), value: 'opportunity-create' },
    { label: __('Event Create', 'bit-integrations'), value: 'event-create' },
    { label: __('Create Case', 'bit-integrations'), value: 'case-create' }
  ]

  const [salesforceConf, setSalesforceConf] = useState({
    name: 'Salesforce',
    type: 'Salesforce',
    clientId:
      process.env.NODE_ENV === 'development'
        ? '3MVG9fe4g9fhX0E44ebIn.zn5KgPBKCuDGqLDPSpP5dIg8pf3XIPRMooVLLpLp5UvPMWegD00Vv_PE4_oQLGN'
        : '',
    clientSecret:
      process.env.NODE_ENV === 'development'
        ? '558BA6981F35209E3A89F617BD25C017E6354188F3C8C776E76EF116307AB522'
        : '',
    field_map: [{ formField: '', selesforceField: '' }],
    selesforceActionModules: action_modules,
    action_modules,
    contactFields,
    leadFields,
    accountFields,
    campaignFields,
    campaignMemberStatus,
    opportunityFields,
    eventFields,
    caseFields,
    actions: {}
  })
  useEffect(() => {
    window.opener && setGrantTokenResponse('salesforce')
  }, [])

  const checkedActionFieldsMapType = [
    'contact-create',
    'lead-create',
    'account-create',
    'campaign-create',
    'opportunity-create',
    'event-create',
    'case-create'
  ].includes(salesforceConf?.actionName)

  const nextPage = (val) => {
    if (checkedActionFieldsMapType && !checkMappedFields(salesforceConf)) {
      toast.error('Please map mandatory fields !')
      return
    }

    setStep(val)
  }
  const isDisabled = () => {
    if (salesforceConf?.actionName === 'opportunity-create') {
      return !salesforceConf.actions?.opportunityStageId
    }
    if (salesforceConf?.actionName === 'event-create') {
      return !salesforceConf.actions?.eventSubjectId
    }
    if (salesforceConf?.actionName === 'add-campaign-member') {
      return !salesforceConf.campaignId
    }
  }

  return (
    <div>
      <SnackMsg snack={snack} setSnackbar={setSnackbar} />
      <div className="txt-center mt-2">
        <Steps step={3} active={step} />
      </div>

      {/* STEP 1 */}
      <SalesforceAuthorization
        formID={formID}
        salesforceConf={salesforceConf}
        setSalesforceConf={setSalesforceConf}
        step={step}
        setStep={setStep}
        isLoading={isLoading}
        setIsLoading={setIsLoading}
        setSnackbar={setSnackbar}
      />

      {/* STEP 2 */}
      <div className="btcd-stp-page" style={{ width: step === 2 && 900, height: step === 2 && 'auto' }}>
        <SelesforceIntegLayout
          formID={formID}
          formFields={formFields}
          handleInput={(e) =>
            handleInput(e, salesforceConf, setSalesforceConf, formID, setIsLoading, setSnackbar)
          }
          salesforceConf={salesforceConf}
          setSalesforceConf={setSalesforceConf}
          isLoading={isLoading}
          setIsLoading={setIsLoading}
          setSnackbar={setSnackbar}
        />

        <button
          onClick={() => nextPage(3)}
          disabled={isDisabled() || isLoading}
          className="btn f-right btcd-btn-lg purple sh-sm flx"
          type="button"
        >
          {__('Next', 'bit-integrations')}
          <BackIcn className="ml-1 rev-icn" />
        </button>
      </div>

      {/* STEP 3 */}
      <IntegrationStepThree
        step={step}
        saveConfig={() =>
          saveActionConf({
            flow,
            setFlow,
            allIntegURL,
            navigate,
            conf: salesforceConf,
            setIsLoading,
            setSnackbar
          })
        }
      />
    </div>
  )
}

export default Salesforce
