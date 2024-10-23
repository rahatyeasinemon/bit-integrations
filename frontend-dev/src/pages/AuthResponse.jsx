import { useEffect } from 'react'
import { useRecoilState } from 'recoil'
import { grantTokenAtom } from '../GlobalStates'

// popup window: render when redirected from oauth to bit-integration with code
export default function AuthResponse() {
  const [, setGrantToken] = useRecoilState(grantTokenAtom)

  useEffect(() => {
    const hash = window.location.hash

    const urlParams = new URLSearchParams(hash.split('?')[1] || hash.split('&')[1])

    const code = urlParams.get('code')

    if (code) {
      setGrantToken(code)

      window.close()
    }
  }, [])

  return <h4>Auth Response Captured</h4>
}
