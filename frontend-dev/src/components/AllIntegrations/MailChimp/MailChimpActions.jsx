/* eslint-disable no-param-reassign */

import { __ } from '../../../Utils/i18nwrap'
import TableCheckBox from '../../Utilities/TableCheckBox'

export default function MailChimpActions({ mailChimpConf, setMailChimpConf, formFields, address }) {
  const actionHandler = (e, type) => {
    const newConf = { ...mailChimpConf }
    if (type === 'update') {
      if (e.target.checked) {
        newConf.actions.update = true
      } else {
        delete newConf.actions.update
      }
    }
    if (type === 'double_opt_in') {
      if (e.target.checked) {
        newConf.actions.double_opt_in = true
      } else {
        delete newConf.actions.double_opt_in
      }
    }
    if (type === 'address') {
      if (e.target.checked) {
        newConf.actions.address = true
        newConf.address_field = address.filter(addr => addr.required).map(adr => ({ formField: '', mailChimpAddressField: adr.tag, required: true }))
      } else {
        delete newConf.actions.address
        newConf.address_field = ''
      }
    }
    setMailChimpConf({ ...newConf })
  }

  return (

    <div className="pos-rel d-flx w-8">
      {(!mailChimpConf?.module || mailChimpConf?.module === 'add_a_member_to_an_audience') && <TableCheckBox checked={mailChimpConf.actions?.address || false} onChange={(e) => actionHandler(e, 'address')} className="wdt-200 mt-4 mr-2" value="address" title={__('Add Address Field', 'bit-integrations')} subTitle={__('Add Address Field', 'bit-integrations')} />}
      {(!mailChimpConf?.module || mailChimpConf?.module === 'add_a_member_to_an_audience') && <TableCheckBox checked={mailChimpConf.actions?.double_opt_in || false} onChange={(e) => actionHandler(e, 'double_opt_in')} className="wdt-200 mt-4 mr-2" value="double_opt_in" title={__('Double Opt-in', 'bit-integrations')} subTitle={__('Add Double Opt-in', 'bit-integrations')} />}
      {(!mailChimpConf?.module || mailChimpConf?.module === 'add_a_member_to_an_audience') && <TableCheckBox checked={mailChimpConf.actions?.update || false} onChange={(e) => actionHandler(e, 'update')} className="wdt-200 mt-4 mr-2" value="user_share" title={__('Update Mail Chimp', 'bit-integrations')} subTitle={__('Update Responses with MailChimp exist Aduience?', 'bit-integrations')} />}
    </div>
  )
}
