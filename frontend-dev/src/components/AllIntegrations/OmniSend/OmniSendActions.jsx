/* eslint-disable no-param-reassign */

import { useState } from 'react'
import MultiSelect from 'react-multiple-select-dropdown-lite'
import { __ } from '../../../Utils/i18nwrap'
import Loader from '../../Loaders/Loader'
import ConfirmModal from '../../Utilities/ConfirmModal'
import TableCheckBox from '../../Utilities/TableCheckBox'
import 'react-multiple-select-dropdown-lite/dist/index.css'

export default function OmniSendActions({ omniSendConf, setOmniSendConf, formFields, loading, setLoading }) {
  const [actionMdl, setActionMdl] = useState({ show: false, action: () => { } })
  const actionHandler = (e, type) => {
    const newConf = { ...omniSendConf }
    if (type === 'mailer_lite_type') {
      if (e.target.checked) {
        newConf.actions.mailer_lite_type = true
      } else {
        delete newConf.actions.mailer_lite_type
      }
      setActionMdl({ show: type })
    }

    setOmniSendConf({ ...newConf })
  }
  const clsActionMdl = () => {
    setActionMdl({ show: false })
  }

  const setChanges = (val, type) => {
    const newConf = { ...omniSendConf }
    if (type === 'mailer_lite_type' && val.length) {
      newConf.actions.mailer_lite_type = true
    } else if (type === 'mailer_lite_type' && val.length < 1) {
      delete newConf.actions.mailer_lite_type
    }
    newConf[type] = val
    setOmniSendConf({ ...newConf })
  }

  const omniSendTypes = [
    {
      label: 'Active',
      value: 'active',
    },
    {
      label: 'Unsubscribed',
      value: 'unsubscribed',
    },

    {
      label: 'Bounced',
      value: 'bounced',
    },
    {
      label: 'Spam Complaints',
      value: 'junk',
    },
    {
      label: 'Unconfirmed',
      value: 'unconfirmed',
    },
  ]

  return (
    <>
      {/* <div className="pos-rel d-flx w-8">
                <TableCheckBox checked={omniSendConf?.mailer_lite_type || false} onChange={(e) => actionHandler(e, 'mailer_lite_type')} className="wdt-200 mt-4 mr-2" value="type" title={__('Type', 'bit-integrations')} subTitle={__('Add Type', 'bit-integrations')} />
            </div>

            <ConfirmModal
                className="custom-conf-mdl"
                mainMdlCls="o-v"
                btnClass="blue"
                btnTxt={__('Ok', 'bit-integrations')}
                show={actionMdl.show === 'mailer_lite_type'}
                close={clsActionMdl}
                action={clsActionMdl}
                title={__('Type', 'bit-integrations')}
            >
                <div className="btcd-hr mt-2 mb-2" />

                <div className="flx flx-between mt-2">
                    <MultiSelect
                        className="msl-wrp-options"
                        defaultValue={omniSendConf?.mailer_lite_type}
                        options={omniSendTypes.map(type => ({ label: type.label, value: type.value }))}
                        onChange={val => setChanges(val, 'mailer_lite_type')}
                        customValue
                        singleSelect
                    />
                </div>

            </ConfirmModal> */}

    </>

  )
}
