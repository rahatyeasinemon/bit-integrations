import { atom, selector } from 'recoil'

// atoms
// eslint-disable-next-line no-undef
export const $btcbi = atom({ key: '$btcbi', default: typeof btcbi !== 'undefined' ? btcbi : {} })
export const $newFlow = atom({ key: '$newFlow', default: {}, dangerouslyAllowMutability: true })
export const $actionConf = atom({
  key: '$actionConf',
  default: {},
  dangerouslyAllowMutability: true
})
export const $formFields = atom({
  key: '$formFields',
  default: {},
  dangerouslyAllowMutability: true
})
export const $flowStep = atom({ key: '$flowStep', default: 1, dangerouslyAllowMutability: true })

export const $flowFormFields = selector({
  key: 'flow-form-fields',
  get: ({ get }) => {
    const flow = get($newFlow)
    return flow?.triggerData?.fields || []
  }
})
