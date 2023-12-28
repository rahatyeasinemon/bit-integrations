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

const CaptureAction = () => {
  const [newFlow, setNewFlow] = useRecoilState($newFlow)
  const setFlowStep = useSetRecoilState($flowStep)
  const setFields = useSetRecoilState($formFields)
  const [hookID, setHookID] = useState('')
  const [primaryKey, setPrimaryKey] = useState('')
  const [selectedFields, setSelectedFields] = useState([])
  const [isLoading, setIsLoading] = useState(false)
  const [snack, setSnackbar] = useState({ show: false })
  const { api } = useRecoilValue($btcbi)
  const [showResponse, setShowResponse] = useState(false)
  const intervalRef = useRef(null)

  const setTriggerData = () => {
    const tmpNewFlow = { ...newFlow }
    tmpNewFlow.triggerData = {
      formID: hookID,
      fields: tmpNewFlow.triggerDetail.data,
    }
    tmpNewFlow.triggered_entity_id = hookID
    setFields(tmpNewFlow.triggerDetail.data)
    setNewFlow(tmpNewFlow)
    setFlowStep(2)
  }

  const setSelectedFieldsData = (value, index = null) => {
    setSelectedFields(prevFields => create(prevFields, (draftFields) => {
      if (index !== null) {
        draftFields[index] = value
        return
      }
      value.namespace.push(value.name)
      draftFields.push(JSON.stringify(value.namespace))
    }))
  }

  const removeSelectedField = index => {
    setSelectedFields(prevFields => create(prevFields, (draftFields) => {
      draftFields.splice(index, 1)
    }))
  }
  // console.log(selectedFields)

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
          // const data = resp.data.captureAction

          // let convertedData = Object.entries(data).reduce(
          //   (outObj, item) => {
          //     const [name, obj] = item
          //     if (typeof obj === 'object' && obj !== null && obj !== undefined) {
          //       const objArr = Object.entries(obj)
          //       const inObj = objArr.reduce((out, [n, v]) => {
          //         const propName = `${name}_${n}`

          //         return { ...out, [propName]: v }
          //       }, {})
          //       return { ...outObj, ...inObj }
          //     }
          //     return data
          //   },
          //   {},
          // )

          // if (typeof resp.data.captureAction === 'object') {
          //   convertedData = Object.keys(convertedData).map(fld => (
          //     { name: fld, label: `${convertedData[fld]}-${fld}`, type: 'text' }
          //   ))
          // }

          tmpNewFlow.triggerDetail.tmp = resp.data.captureAction
          // tmpNewFlow.triggerDetail.data = convertedData
          tmpNewFlow.triggerDetail.data = resp.data.captureAction
          tmpNewFlow.triggerDetail.hook_id = hookID
          setNewFlow(tmpNewFlow)
          setIsLoading(false)
          setShowResponse(true)
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

  // const info = `You can test any kind of captureAction using <a href="https://captureAction.is/" target="_blank" rel="noreferrer">captureAction.is</a>`


  return (
    <div className="trigger-custom-width">
      <SnackMsg snack={snack} setSnackbar={setSnackbar} />
      <div className="mt-3">
        <b>{__('Hook:', 'bit-integrations')}</b>
      </div>
      {/* <CopyText
        value={`${api.base}/callback/${hookID}`}
        className="field-key-cpy w-10 ml-0"
        setSnackbar={setSnackbar}
        readOnly
      /> */}
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
      <div className="mt-3">
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
      </div>
      {/* {showResponse && (
        <WebhookDataTable
          data={newFlow?.triggerDetail?.data}
          flow={newFlow}
          setFlow={setNewFlow}
        />
      )} */}

      {
        showResponse && (
          <>
            <div className="mt-3">
              <b>{__('Select Fields:', 'bit-integrations')}</b>
            </div>
            {/* <JsonViewer
              data={newFlow?.triggerDetail?.data}
              onChange={(value) => setSelectedFieldsData(value)}
            /> */}
            {/* <TreeViewer data={newFlow?.triggerDetail?.data} /> */}
          </>
        )
      }
      <TreeViewer data={newFlow?.triggerDetail?.data} />
      <button
        onClick={setTriggerData}
        className="btn btcd-btn-lg green sh-sm flx"
        type="button"
        disabled={!newFlow.triggerDetail?.data}
      >
        Set Action
      </button>
      {/* <Note note={info} /> */}
    </div >
  )
}
export default CaptureAction

