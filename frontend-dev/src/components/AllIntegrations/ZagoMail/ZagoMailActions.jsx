/* eslint-disable no-param-reassign */
// eslint-disable-next-line import/no-extraneous-dependencies
import { __ } from '@wordpress/i18n'
import TableCheckBox from '../../Utilities/TableCheckBox'

export default function ZagoMailActions({ zagoMailConf, setZagoMailConf }) {
  const actionHandler = (e, type) => {
    const newConf = { ...zagoMailConf }
    if (type === 'update') {
      if (e.target.checked) {
        newConf.actions.update = true
      } else {
        delete newConf.actions.update
      }
    }
    setZagoMailConf({ ...newConf })
  }

  return (
    <div className="pos-rel d-flx w-8">
      <TableCheckBox checked={zagoMailConf.actions?.update || false} onChange={(e) => actionHandler(e, 'update')} className="wdt-200 mt-4 mr-2" value="user_share" title={__('Update ZagoMail', 'bit-integrations')} subTitle={__('Update Responses with ZagoMail existing email?', 'bit-integrations')} />
    </div>
  )
}
