/* eslint-disable no-unused-expressions */
/* eslint-disable max-len */
import { lazy, memo, useCallback, useEffect, useState } from 'react'
import toast from 'react-hot-toast'
import { Link } from 'react-router-dom'
import Loader from '../components/Loaders/Loader'
import BuyProModal from '../components/Utilities/BuyProModal'
import ConfirmModal from '../components/Utilities/ConfirmModal'
import MenuBtn from '../components/Utilities/MenuBtn'
import SingleToggle2 from '../components/Utilities/SingleToggle2'
import SnackMsg from '../components/Utilities/SnackMsg'
import Table from '../components/Utilities/Table'
import bitsFetch from '../Utils/bitsFetch'
import { __ } from '../Utils/i18nwrap'

const Welcome = lazy(() => import('./Welcome'))

function AllIntegrations({ integrations, setIntegrations, isLoading }) {
  const [snack, setSnackbar] = useState({ show: false })
  const [confMdl, setconfMdl] = useState({ show: false, btnTxt: '' })
  const [premiumModal, setPremiumModal] = useState({ show: false, msg: '' })
  const [cols, setCols] = useState([
    { width: 250, minWidth: 80, Header: __('Trigger', 'bit-integrations'), accessor: 'triggered_entity' },
    { width: 250, minWidth: 80, Header: __('Action Name', 'bit-integrations'), accessor: 'name' },
    { width: 220, minWidth: 200, Header: __('Created At', 'bit-integrations'), accessor: 'created_at' },
    { width: 70, minWidth: 60, Header: __('Status', 'bit-integrations'), accessor: 'status', Cell: value => <SingleToggle2 className="flx" action={(e) => handleStatus(e, value.row.original.id)} checked={Number(value.row.original.status) === 1} /> },
  ])

  useEffect(() => {
    const ncols = cols.filter(itm => itm.accessor !== 't_action' && itm.accessor !== 'status')

    ncols.push({ width: 70, minWidth: 60, Header: __('Status', 'bit-integrations'), accessor: 'status', Cell: value => <SingleToggle2 className="flx" action={(e) => handleStatus(e, value.row.original.id)} checked={Number(value.row.original.status) === 1} /> })
    ncols.push({ sticky: 'right', width: 100, minWidth: 60, Header: 'Actions', accessor: 't_action', Cell: val => <MenuBtn id={val.row.original.id} name={val.row.original.name} index={val.row.id} del={() => showDelModal(val.row.original.id, val.row.index)} /* dup={() => showDupMdl(val.row.original.formID)} export={() => showExportMdl(val.row.original.formID)} */ /> })
    setCols([...ncols])
  }, [integrations])

  const handleStatus = (e, id) => {
    const status = e.target.checked
    const tmp = [...integrations]
    const integ = tmp.find(int => int.id === id)
    integ.status = status === true ? '1' : '0'
    setIntegrations(tmp)

    const param = { id, status }
    bitsFetch(param, 'flow/toggleStatus')
      .then(res => {
        toast.success(__(res.data, 'bit-integrations'))
      }).catch(() => {
        toast.error(__('Something went wrong', 'bit-integrations'))
      })
  }

  const handleDelete = (id, index) => {
    bitsFetch({ id }, 'flow/delete').then(response => {
      if (response.success) {
        const tmpIntegrations = [...integrations]
        tmpIntegrations.splice(index, 1)
        setIntegrations(tmpIntegrations)
        toast.success(__(response.data, 'bit-integrations'))
      }
    })
  }

  const setBulkDelete = useCallback(rows => {
    const rowID = []
    const flowID = []
    for (let i = 0; i < rows.length; i += 1) {
      rowID.push(rows[i].id)
      flowID.push(rows[i].original.id)
    }

    bitsFetch({ flowID }, 'flow/bulk-delete').then(response => {
      if (response.success) {
        const newData = [...integrations]
        for (let i = rowID.length - 1; i >= 0; i -= 1) {
          newData.splice(Number(rowID[i]), 1)
        }
        setIntegrations(newData)
        toast.success(__(response.data, 'bit-integrations'))
      }
    })
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [integrations])

  const setTableCols = useCallback(newCols => { setCols(newCols) }, [])
  const closeConfMdl = () => {
    confMdl.show = false
    setconfMdl({ ...confMdl })
  }
  const showDelModal = (id, index) => {
    confMdl.action = () => { handleDelete(id, index); closeConfMdl() }
    confMdl.btnTxt = __('Delete', 'bit-integrations')
    confMdl.btn2Txt = null
    confMdl.btnClass = ''
    confMdl.body = __('Are you sure to delete this Integration?', 'bit-integrations')
    confMdl.show = true
    setconfMdl({ ...confMdl })
  }

  const actionHandler = () => {
    setPremiumModal({ show: true })
  }

  const loaderStyle = {
    display: 'flex',
    height: '82vh',
    justifyContent: 'center',
    alignItems: 'center',
  }

  if (isLoading) {
    return (
      <Loader style={loaderStyle} />
    )
  }
  return (

    <div id="all-forms">
      <SnackMsg snack={snack} setSnackbar={setSnackbar} />
      <ConfirmModal
        show={confMdl.show}
        body={confMdl.body}
        action={confMdl.action}
        close={closeConfMdl}
        btnTxt={confMdl.btnTxt}
        btn2Txt={confMdl.btn2Txt}
        btn2Action={confMdl.btn2Action}
        btnClass={confMdl.btnClass}
      />

      {integrations && integrations?.length ? (
        <>
          <div className="af-header flx flx-between">
            <h2>{__('Integrations', 'bit-integrations')}</h2>
            {integrations.length >= 1
              ? (
                // eslint-disable-next-line react/button-has-type
                <button className="btn round btcd-btn-lg blue blue-sh" onClick={() => actionHandler()}>
                  {__('Create Integration', 'bit-integrations')}
                </button>
              )
              : (
                <Link to="/flow/new" className="btn round btcd-btn-lg blue blue-sh">
                  {__('Create Integration', 'bit-integrations')}
                </Link>
              )}
          </div>
          <div className="forms">
            <Table
              className="f-table btcd-all-frm"
              height={500}
              columns={cols}
              data={integrations}
              rowSeletable
              resizable
              columnHidable
              setTableCols={setTableCols}
              setBulkDelete={setBulkDelete}
              search
            />
          </div>
          <BuyProModal
            md
            show={premiumModal.show}
            setModal={() => setPremiumModal({ show: false })}
            title={__('Want to create unlimited integration ?', 'bit-integrations')}
            className="pro-modal"
            data={integrations.length}
          />
        </>
      ) : <Welcome />}
    </div>
  )
}

export default memo(AllIntegrations)
