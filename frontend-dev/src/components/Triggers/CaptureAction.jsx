/* eslint-disable react/button-has-type */
/* eslint-disable no-console */
import { Fragment, useEffect, useRef, useState } from 'react'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import { useRecoilState, useRecoilValue, useSetRecoilState } from 'recoil'
import { $btcbi, $flowStep, $formFields, $newFlow } from '../../GlobalStates'
import bitsFetch from '../../Utils/bitsFetch'
import { __ } from '../../Utils/i18nwrap'
import LoaderSm from '../Loaders/LoaderSm'
import CopyText from '../Utilities/CopyText'
import SnackMsg from '../Utilities/SnackMsg'
import WebhookDataTable from '../Utilities/WebhookDataTable'
import EyeIcn from '../Utilities/EyeIcn'
import EyeOffIcn from '../Utilities/EyeOffIcn'
import Note from '../Utilities/Note'
import ReactJson from '@microlink/react-json-view'
import JsonViewer from '../Utilities/JsonViewer'
import TagifyInput from '../Utilities/TagifyInput'
import { create } from 'mutative'
import CloseIcn from '../../Icons/CloseIcn'
import TreeViewer from '../Utilities/treeViewer/TreeViewer'
import toast from 'react-hot-toast'
import ConfirmModal from '../Utilities/ConfirmModal'
import MultiSelect from 'react-multiple-select-dropdown-lite'

const CaptureAction = () => {
  const [newFlow, setNewFlow] = useRecoilState($newFlow)
  const setFlowStep = useSetRecoilState($flowStep)
  const setFields = useSetRecoilState($formFields)
  const [hookID, setHookID] = useState('')
  const [primaryKey, setPrimaryKey] = useState()
  const [primaryKeyModal, setPrimaryKeyModal] = useState(false)
  const [selectedFields, setSelectedFields] = useState([])
  const [isLoading, setIsLoading] = useState(false)
  const [snack, setSnackbar] = useState({ show: false })
  const { api } = useRecoilValue($btcbi)
  const [showResponse, setShowResponse] = useState(false)
  const intervalRef = useRef(null)

  const setTriggerData = () => {
    if (!selectedFields.length) {
      toast.error('Please Select fields')
      return
    }
    if (!primaryKey) {
      toast.error('Please Select a Primary Key')
      return
    }

    const tmpNewFlow = { ...newFlow }
    tmpNewFlow.triggerData = {
      formID: hookID,
      primaryKey: primaryKey,
      fields: selectedFields.map(field => ({ label: field, name: field }))
    }
    tmpNewFlow.triggered_entity_id = hookID
    setFields(selectedFields)
    setNewFlow(tmpNewFlow)
    setFlowStep(2)
  }

  const setSelectedFieldsData = (value = null, remove = false, index = null) => {
    if (remove) {
      index = index ? index : selectedFields.indexOf(value)

      if (index !== -1) {
        removeSelectedField(index)
      }
      return
    }
    addSelectedField(value)
  }

  const addSelectedField = value => {
    // console.log('value', extractValue(newFlow?.triggerDetail?.data, value))
    setSelectedFields(prevFields => create(prevFields, (draftFields) => {
      draftFields.push(value)
    }))
  }

  const removeSelectedField = index => {
    setSelectedFields(prevFields => create(prevFields, (draftFields) => {
      draftFields.splice(index, 1)
    }))
  }

  useEffect(() => {
    if (newFlow.triggerDetail?.data?.length > 0 && newFlow.triggerDetail?.hook_id) {
      setHookID(newFlow.triggerDetail?.hook_id)
      window.hook_id = newFlow.triggerDetail?.hook_id
    } else {
      bitsFetch({ hook_id: hookID }, 'capture_action/new', null, 'get').then(
        (resp) => {
          if (resp.success) {
            setHookID(resp.data.hook_id)
            window.hook_id = resp.data.hook_id
          }
        },
      )
    }
    return () => {
      setFields()
      bitsFetch({ hook_id: window.hook_id }, 'capture_action/test/remove').then(
        (resp) => {
          delete window.hook_id
          intervalRef.current && clearInterval(intervalRef.current)
        },
      )
    }
  }, [])
  // console.log(newFlow?.triggerDetail?.data)

  const handleFetch = () => {
    if (isLoading) {
      clearInterval(intervalRef.current)
      setIsLoading(false)
      return
    }

    setIsLoading(true)
    window.hook_id = hookID
    intervalRef.current = setInterval(() => {
      bitsFetch({ hook_id: hookID }, 'capture_action/test').then((resp) => {
        if (resp.success) {
          clearInterval(intervalRef.current)
          const tmpNewFlow = { ...newFlow }

          tmpNewFlow.triggerDetail.tmp = resp.data.captureAction
          tmpNewFlow.triggerDetail.data = resp.data.captureAction
          tmpNewFlow.triggerDetail.hook_id = hookID
          setNewFlow(tmpNewFlow)
          setIsLoading(false)
          setShowResponse(true)
          setSelectedFields([])
          bitsFetch(
            { hook_id: window.hook_id, reset: true },
            'capture_action/test/remove',
          )
        }
      })
    }, 1500)
  }

  const showResponseTable = () => {
    setShowResponse((prevState) => !prevState)
  }

  const primaryKeySet = (val) => {
    setPrimaryKey(!val ? undefined : {
      key: val,
      value: extractValueFromPath(newFlow.triggerDetail?.data, val)
    })
  }

  // const info = `You can test any kind of captureAction using <a href="https://captureAction.is/" target="_blank" rel="noreferrer">captureAction.is</a>`


  return (
    <div className="trigger-custom-width">
      <SnackMsg snack={snack} setSnackbar={setSnackbar} />
      <div className="mt-3">
        <b>{__('Hook:', 'bit-integrations')}</b>
      </div>
      <input className="btcd-paper-inp w-100 mt-1" onChange={e => setHookID(e.target.value)} name="hook" value={hookID} type="text" placeholder={__('Enter Hook...', 'bit-integrations')} disabled={newFlow?.triggerData?.fields || isLoading || false} />
      {/* <br />
      <br /> */}
      {/* {newFlow?.triggerDetail?.data &&
        <>
          <b className="wdt-200 d-in-b">{__('Select Form Id:', 'bit-integrations')}</b>
          <select onChange={(e) => setPrimaryKey(e.target.value)} name="primaryKey" value={primaryKey} className="btcd-paper-inp mt-1">
            <option value="">{__('Select Key', 'bit-integrations')}</option>
            {
              newFlow?.triggerDetail?.data && newFlow?.triggerDetail?.data.map(({ name, label }) => (
                <option key={name} value={label}>
                  {label}
                </option>
              ))
            }
          </select>
          <br />
          <br />
        </>
      } */}
      {newFlow.triggerDetail?.data &&
        <>
          <div className="my-3">
            <b>{__('Selected Fields:', 'bit-integrations')}</b>
          </div>
          <div className="bg-white rounded border my-1 table-webhook-div p-2" style={{ minHeight: '100px', maxHeight: '14rem' }}>
            {selectedFields.map((field, index) => <div key={index} style={{ position: "relative" }}>
              <input key={index} className="btcd-paper-inp w-100 m-1" type='text' onChange={e => setSelectedFieldsData(e.target.value, index)} value={field.replace(/[,]/gi, '.').replace(/["{\}[\](\)]/gi, '')} disabled={newFlow?.triggerData?.fields || isLoading || false} />
              <button
                className="btn btcd-btn-lg sh-sm"
                onClick={() => removeSelectedField(index)}
                style={
                  {
                    position: 'absolute',
                    top: -5,
                    right: -5,
                    color: '#ff4646',
                    padding: '2px'
                  }
                }
              >
                <CloseIcn size={12} />
              </button>
            </div>)}
          </div>
        </>
      }
      <div className="flx flx-between">
        <button
          onClick={handleFetch}
          className={`btn btcd-btn-lg sh-sm flx ${isLoading ? 'red' : 'green'}`}
          type="button"
          disabled={!hookID}
        >
          {isLoading ? __('Stop', 'bit-integrations') : newFlow.triggerDetail?.data
            ? __('Fetched ✔', 'bit-integrations')
            : __('Fetch', 'bit-integrations')}
          {isLoading && (
            <LoaderSm size="20" clr="#022217" className="ml-2" />
          )}
        </button>
        {selectedFields.length > 0 &&
          <button
            onClick={() => setPrimaryKeyModal(true)}
            className={`btn btcd-btn-lg sh-sm flx ${selectedFields.length && 'green'}`}
            type="button"
            disabled={!selectedFields.length}
          >
            {primaryKey
              ? __('Primary Key ✔', 'bit-integrations')
              : __('Primary Key', 'bit-integrations')}
          </button>
        }
      </div>
      <ConfirmModal
        className="custom-conf-mdl"
        mainMdlCls="o-v"
        btnClass="blue"
        btnTxt={__('Ok', 'bit-integrations')}
        show={primaryKeyModal}
        close={() => setPrimaryKeyModal(false)}
        action={() => setPrimaryKeyModal(false)}
        title={__('Primary Key', 'bit-integrations')}
      >
        <div className="btcd-hr mt-2 mb-2" />
        <div className="mt-2">
          {__('Select Primary Key', 'bit-integrations')}
        </div>
        <div className="flx flx-between mt-2">
          <MultiSelect
            options={selectedFields.map(field => ({ label: field, value: field }))}
            className="msl-wrp-options"
            defaultValue={primaryKey?.key}
            onChange={primaryKeySet}
            singleSelect
            closeOnSelect
          />
        </div>
      </ConfirmModal>

      {
        showResponse && (
          <>
            <div className="mt-3">
              <b>{__('Select Fields:', 'bit-integrations')}</b>
            </div>
            <TreeViewer data={newFlow?.triggerDetail?.data} onChange={setSelectedFieldsData} />
          </>
        )
      }
      {newFlow.triggerDetail?.data &&
        <div className="flx flx-between">
          {newFlow.triggerDetail?.data && (
            <button
              onClick={showResponseTable}
              className="btn btcd-btn-lg sh-sm flx"
            >
              <span className="txt-captureAction-resbtn font-inter-500">
                {showResponse ? 'Hide Response' : 'View Response'}
              </span>
              {!showResponse ? (
                <EyeIcn
                  width="20"
                  height="20"
                  strokeColor="#000000"
                />
              ) : (
                <EyeOffIcn
                  width="20"
                  height="20"
                  strokeColor="#000000"
                />
              )}
            </button>
          )}
          <button
            onClick={setTriggerData}
            className="btn btcd-btn-lg green sh-sm flx"
            type="button"
            disabled={!selectedFields.length || !primaryKey}
          >
            Set Action
          </button>
        </div>
      }
      {/* <Note note={info} /> */}
    </div >
  )
}
export default CaptureAction

function extractValueFromPath(json, path) {
  const parts = Array.isArray(path) ? path : path.split('.')
  if (parts.length === 0) {
    return json
  }

  const currentPart = parts.shift()
  if (Array.isArray(json)) {
    const index = parseInt(currentPart, 10)
    if (isNaN(index) || index >= json.length) {
      toast.error('Index out of bounds or invalid')
      return
    }

    return extractValueFromPath(json[index], parts)
  }

  if (json && typeof json === 'object') {
    if (!(currentPart in json)) {
      toast.error('Invalid path')
      retrun
    }

    return extractValueFromPath(json[currentPart], parts)
  }

  toast.error('Invalid path')
  return
}