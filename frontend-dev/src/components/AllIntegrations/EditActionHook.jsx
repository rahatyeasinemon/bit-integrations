/* eslint-disable no-param-reassign */

import { create } from 'mutative'
import { useEffect, useRef, useState } from 'react'
import MultiSelect from 'react-multiple-select-dropdown-lite'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import { useRecoilState, useSetRecoilState } from 'recoil'
import { $formFields, $newFlow } from '../../GlobalStates'
import bitsFetch from '../../Utils/bitsFetch'
import { __ } from '../../Utils/i18nwrap'
import LoaderSm from '../Loaders/LoaderSm'
import toast from 'react-hot-toast'
import { deepCopy } from '../../Utils/Helpers'
import CloseIcn from '../../Icons/CloseIcn'
import EyeIcn from '../Utilities/EyeIcn'
import EyeOffIcn from '../Utilities/EyeOffIcn'
import TreeViewer from '../Utilities/treeViewer/TreeViewer'

function EditActionHook({ setSnackbar }) {
  const [flow, setFlow] = useRecoilState($newFlow)
  const setFormFields = useSetRecoilState($formFields)
  const [hookID, setHookID] = useState('')
  const [selectedHook, setSelectedHook] = useState('custom')
  const [customHook, setCustomHook] = useState(true)
  const [primaryKey, setPrimaryKey] = useState()
  const [primaryKeyModal, setPrimaryKeyModal] = useState(false)
  const [selectedFields, setSelectedFields] = useState([])
  const [showResponse, setShowResponse] = useState(true)
  const [showSelectedFields, setShowSelectedFields] = useState(false)
  const [isLoading, setIsLoading] = useState(false)
  const intervalRef = useRef(null)

  console.log(flow)

  // const handleFetch = () => {
  //   if (isLoading) {
  //     clearInterval(intervalRef.current)
  //     removeTestData()
  //     setIsLoading(false)
  //     return
  //   }

  //   setIsLoading(true)
  //   intervalRef.current = setInterval(() => {
  //     bitsFetch(null, fetchAction, null, fetchMethod).then((resp) => {
  //       if (resp.success) {
  //         clearInterval(intervalRef.current)
  //         setFlow(prevFlow => {
  //           const draftFlow = deepCopy(prevFlow)

  //           draftFlow.flow_details.fields = resp.data?.formData
  //           draftFlow.flow_details.primaryKey.key = resp.data?.primaryKey?.key
  //           draftFlow.flow_details.primaryKey.value = resp.data?.primaryKey?.value

  //           return draftFlow
  //         })
  //         setFormFields(resp.data?.formData)
  //         setIsLoading(false)
  //         bitsFetch({ reset: true }, removeAction, null, removeMethod)
  //       }
  //     })
  //   }, 1500)
  // }

  // const primaryKeySet = (key) => {
  //   setFlow(prevFlow => create(prevFlow, draftFlow => {
  //     const keys = key?.split(',') || []
  //     const primaryKey = keys.map(k => (
  //       {
  //         key: k,
  //         value: flow.flow_details.fields?.find(item => item.name === k)?.value
  //       }
  //     ))

  //     const hasEmptyValues = primaryKey.some(item => !item.value);
  //     if (key && hasEmptyValues) {
  //       draftFlow.flow_details.primaryKey = null

  //       toast.error('Unique value not found!')
  //       return
  //     }

  //     draftFlow.flow_details.primaryKey = primaryKey
  //   }))
  // }

  // const removeTestData = (hookID) => {
  //   bitsFetch({ hook_id: hookID }, 'action_hook/test/remove').then(
  //     (resp) => {
  //       delete window.hook_id
  //       intervalRef.current && clearInterval(intervalRef.current)
  //     },
  //   )
  // }

  // useEffect(() => {
  //   return () => {
  //     removeTestData()
  //   }
  // }, [])

  return (
    <div className='trigger-custom-width'>
      <div className="flx flx-between">
        <div className="wdt-100 d-in-b">
          <b>{__('Hook:', 'bit-integrations')}</b>
        </div>
        <input className="btcd-paper-inp mt-1" onChange={e => setHook(e.target.value, 'custom')} name="custom" value={flow?.triggered_entity_id || ''} type="text" placeholder={__('Enter Hook...', 'bit-integrations')} disabled={isLoading} />

      </div>
      <div className="flx mt-1 flx-between">
        <b className="wdt-100 d-in-b">{__('Unique Key:', 'bit-integrations')}</b>
        <MultiSelect
          options={flow.flow_details.fields?.map(field => ({ label: field?.label, value: field?.name }))}
          className="msl-wrp-options"
          defaultValue={Array.isArray(flow?.flow_details?.primaryKey) ? flow?.flow_details?.primaryKey.map(item => item.key).join(',') : flow?.flow_details?.primaryKey?.key || ''}
          // onChange={primaryKeySet}
          disabled={isLoading}
          closeOnSelect
        />
        {/* <button onClick={handleFetch} className={`btn btcd-btn-lg sh-sm flx ml-1 ${isLoading ? 'red' : 'green'}`} type="button">
          {isLoading
            ? __('Stop', 'bit-integrations')
            : (flow.flow_details.fields
              ? __('Fetched ✔', 'bit-integrations')
              : __('Fetch', 'bit-integrations'))
          }
          {isLoading && <LoaderSm size="20" clr="#022217" className="ml-2" />}
        </button> */}
        <button className={`btn btcd-btn-lg sh-sm flx ml-1 ${isLoading ? 'red' : 'green'}`} type="button">
          {isLoading
            ? __('Stop', 'bit-integrations')
            : (flow.flow_details.fields
              ? __('Fetched ✔', 'bit-integrations')
              : __('Fetch', 'bit-integrations'))
          }
          {isLoading && <LoaderSm size="20" clr="#022217" className="ml-2" />}
        </button>

      </div>
      {flow.flow_details?.fields &&
        <>
          <div className="flx flx-between">
            <div className="my-3">
              <b>{__('Selected Fields:', 'bit-integrations')}</b>
            </div>
            <button
              onClick={() => setShowSelectedFields(prev => !prev)}
              className="btn btcd-btn-md sh-sm flx"
            >
              <span className="txt-actionHook-resbtn font-inter-500">
                {showSelectedFields ? 'Hide Selected Fields' : 'View Selected Fields'}
              </span>
              {!showSelectedFields ? (
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
          </div>
          {showSelectedFields &&
            <div className="bg-white rounded border my-1 table-webhook-div p-2" style={{ minHeight: '40px', maxHeight: '14rem' }}>
              {flow.flow_details?.fields.map((field, index) => (
                <div key={index} style={{ position: "relative" }}>
                  <input
                    key={index}
                    className="btcd-paper-inp w-100 m-1"
                    type='text'
                    // onChange={e => setSelectedFieldsData(e.target.value, index)} 
                    value={field?.name.replace(/[,]/gi, '.').replace(/["{\}[\](\)]/gi, '')}
                    disabled={isLoading}
                  />
                  <button
                    className="btn btcd-btn-lg sh-sm"
                    // onClick={() => removeSelectedField(index)}
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
                </div>
              ))}
            </div>
          }
        </>
      }
      <div className="flx flx-between">
        <div className="my-3">
          <b>{__('Select Fields:', 'bit-integrations')}</b>
        </div>
        <button
          onClick={() => setShowResponse(prev => !prev)}
          className="btn btcd-btn-md sh-sm flx"
        >
          <span className="txt-actionHook-resbtn font-inter-500">
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
      </div>
      {
        flow.flow_details?.rawData && showResponse && (
          <>
            <TreeViewer
              data={flow?.flow_details?.rawData}
            // onChange={setSelectedFieldsData} 
            />
          </>
        )
      }
    </div>
  )
}

export default EditActionHook
