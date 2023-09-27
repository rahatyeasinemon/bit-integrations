/* eslint-disable no-param-reassign */

import { useState } from 'react'
import MultiSelect from 'react-multiple-select-dropdown-lite'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import { __ } from '../../../Utils/i18nwrap'
import Loader from '../../Loaders/Loader'
import ConfirmModal from '../../Utilities/ConfirmModal'
import TableCheckBox from '../../Utilities/TableCheckBox'
// import { getAllTags } from './WoodpeckerCommonFunc'

export default function WoodpeckerActions({ woodpeckerConf, setWoodpeckerConf, loading, setLoading }) {
  const [actionMdl, setActionMdl] = useState({ show: false, action: () => { } })

  const actionHandler = (e, type) => {
    const newConf = { ...woodpeckerConf }
    if (type === 'update') {
      if (e.target.checked) {
        newConf.actions.update = true
      } else {
        delete newConf.actions.update
      }
    }

    setActionMdl({ show: type })
    setWoodpeckerConf({ ...newConf })
  }

  const clsActionMdl = () => {
    setActionMdl({ show: false })
  }

  const setChanges = (val, name) => {
    const newConf = { ...woodpeckerConf }
    newConf[name] = val
    setWoodpeckerConf({ ...newConf })
  }

  return (
    <div className="pos-rel d-flx flx-wrp">
      {woodpeckerConf.actionName !== 'create_company' && <TableCheckBox checked={woodpeckerConf.actions?.update || false} onChange={(e) => actionHandler(e, 'update')} className="wdt-200 mt-4 mr-2" value="user_share" title={__('Update Prospects', 'bit-integrations')} subTitle={__('Update Responses with Prospects existing Data?', 'bit-integrations')} />}
    </div>
  )
}

