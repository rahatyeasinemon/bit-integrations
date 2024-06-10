/* eslint-disable no-param-reassign */

import { __ } from '../../../Utils/i18nwrap'
import TableCheckBox from '../../Utilities/TableCheckBox'
import 'react-multiple-select-dropdown-lite/dist/index.css'

export default function NewsletterActions({ newsletterConf, setNewsletterConf }) {
  const actionHandler = (e) => {
    const newConf = { ...newsletterConf }
    if (e.target.checked) {
      newConf.actions.IsExcludedFromCampaigns = true
    } else {
      delete newConf.actions.IsExcludedFromCampaigns
    }
    setNewsletterConf({ ...newConf })
  }

  return (
    <div className="pos-rel d-flx w-8">
      <TableCheckBox
        checked={newsletterConf.actions?.IsExcludedFromCampaigns || false}
        onChange={(e) => actionHandler(e)}
        className="wdt-200 mt-4 mr-2"
        value="update_subscriber"
        title={__('Is excluded from campaigns', 'bit-integrations')}
        subTitle={__('Indicates whether the contact is added to the exclusion list for campaigns or not. An excluded contact will not be receiving any marketing emails.', 'bit-integrations')}
      />
    </div>
  )
}
