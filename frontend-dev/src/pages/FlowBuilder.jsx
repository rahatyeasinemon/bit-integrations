// eslint-disable-next-line import/no-extraneous-dependencies
import { useRecoilValue } from 'recoil'
import { $flowStep, $newFlow } from '../GlobalStates'
import SelectAction from '../components/Flow/New/SelectAction'
import SelectTrigger from '../components/Flow/New/SelectTrigger'

export default function FlowBuilder() {
  const newFlow = useRecoilValue($newFlow)
  const flowStep = useRecoilValue($flowStep)

  return (
    <div className="btcd-s-wrp" style={{ height: '82vh' }}>
      <div className="flx flx-center flx-col">
        {(!newFlow?.triggered_entity || !newFlow?.triggerData || flowStep === 1) && <SelectTrigger />}
        {(newFlow?.triggered_entity !== false && newFlow?.triggerData && !newFlow?.action && flowStep === 2) && <SelectAction />}
      </div>
    </div>
  )
}
