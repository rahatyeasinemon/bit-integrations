import { useEffect } from 'react'
import { useRecoilState } from 'recoil'
import { grantTokenAtom } from '../GlobalStates'

// popup window: render when redirected from oauth to bit-integration with code
export default function AuthResponse() {
  const [, setGrantToken] = useRecoilState(grantTokenAtom)

  useEffect(() => {
    const urlParams = new URLSearchParams(window.location.hash)
    const code = urlParams.get('code')

    if (code) {
      setGrantToken(code)

      setTimeout(() => {
        window.close()
      }, 100)
    }
  }, [])

  return <h4>Auth Response Captured</h4>
}
