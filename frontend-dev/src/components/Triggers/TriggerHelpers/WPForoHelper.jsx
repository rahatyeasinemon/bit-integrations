import MultiSelect from 'react-multiple-select-dropdown-lite'
import 'react-multiple-select-dropdown-lite/dist/index.css'
import { useRecoilState } from 'recoil'
import { $newFlow } from '../../../GlobalStates'
import { __ } from '../../../Utils/i18nwrap'

const WPForoHelper = ({ flow, setFlowData, edit = false }) => {
  const id = !edit ? flow?.triggerData?.formID : flow.triggered_entity_id
  const [newFlow, setNewFlow] = useRecoilState($newFlow)
  const triggerData = !edit ? newFlow?.triggerData : flow.flow_details

  return (
    <div>
      {id === 'wpforo-1' && (
        <div className={edit ? 'flx mt-3' : ''}>
          <b className={edit ? 'wdt-200 d-in-b' : 'wdt-200 d-in-b mt-3 mb-3'}>Select Forum</b>
          <MultiSelect
            className="msl-wrp-options"
            defaultValue={triggerData?.selectedForum}
            options={triggerData?.forums}
            onChange={(val) => setFlowData(val, 'selectedForum')}
            singleSelect
            style={{ width: '100%', minWidth: 300, maxWidth: 400 }}
          />
        </div>
      )}

      {(id === 'wpforo-3' || id === 'wpforo-4') && (
        <div className={edit ? 'flx mt-3' : ''}>
          <b className={edit ? 'wdt-200 d-in-b' : 'wdt-200 d-in-b mt-3 mb-3'}>Select List</b>
          <MultiSelect
            className="msl-wrp-options"
            defaultValue={triggerData?.selectedList}
            options={triggerData?.lists?.map((list) => ({
              label: list.list_title,
              value: list.list_id.toString()
            }))}
            onChange={(val) => setFlowData(val, 'selectedList')}
            singleSelect
            style={{ width: '100%', minWidth: 300, maxWidth: 400 }}
          />
        </div>
      )}

      {id === 'wpforo-5' && (
        <div className={edit ? 'flx mt-3' : ''}>
          <b className={edit ? 'wdt-200 d-in-b' : 'wdt-200 d-in-b mt-3 mb-3'}>Select Status</b>
          <MultiSelect
            className="msl-wrp-options"
            defaultValue={triggerData?.selectedStatus}
            options={triggerData?.status?.map((status) => ({
              label: status.status_title,
              value: status.status_id.toString()
            }))}
            onChange={(val) => setFlowData(val, 'selectedStatus')}
            singleSelect
            style={{ width: '100%', minWidth: 300, maxWidth: 400 }}
          />
        </div>
      )}
    </div>
  )
}

export default WPForoHelper
