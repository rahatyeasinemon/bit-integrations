/* eslint-disable max-len */
/* eslint-disable no-param-reassign */

import 'react-multiple-select-dropdown-lite/dist/index.css'
import { __ } from '../../../Utils/i18nwrap'
import TableCheckBox from '../../Utilities/TableCheckBox'
import { useRecoilValue } from 'recoil'
import { $btcbi } from '../../../GlobalStates'

export default function FreshSalesActions({ freshSalesConf, setFreshSalesConf }) {
  const btcbi = useRecoilValue($btcbi)
  const { isPro } = btcbi

  const actionHandler = (e, type) => {
    const newConf = { ...freshSalesConf }
    if (type === 'upsert') {
      if (e.target.checked) {
        newConf.actions[type] = true
      } else {
        delete newConf.actions[type]
      }
    }

    setFreshSalesConf({ ...newConf })
  }

  return (
    <>
      <div className="pos-rel d-flx w-8">
        {'Product' !== freshSalesConf.moduleData.module && (
          <TableCheckBox
            onChange={(e) => actionHandler(e, 'upsert')}
            checked={freshSalesConf?.actions?.upsert || false}
            className="wdt-200 mt-4 mr-2"
            value="Upsert_Record"
            isInfo={!isPro}
            title={__(`Upsert Record ${!isPro ? '(Pro)' : ''}`, 'bit-integrations')}
            subTitle={__(
              `${isPro ? 'A record gets updated based on the unique identifier value, else a new record will be created.' : 'The Bit Integration Pro plugin needs to be installed and activated to enable the Upsert Record feature'}`,
              'bit-integrations'
            )}
          />
        )}
      </div>
    </>
  )
}
