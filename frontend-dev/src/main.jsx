/* eslint-disable no-undef */
import { lazy, Suspense } from 'react'
import ReactDOM from 'react-dom/client'
import { HashRouter } from 'react-router-dom'
import { RecoilRoot } from 'recoil'
import RecoilNexus from 'recoil-nexus'
import Loader from './components/Loaders/Loader'
import { AllFormContextProvider } from './Utils/AllFormContext'

const App = lazy(() => import('./App'))
const root = ReactDOM.createRoot(document.getElementById('btcd-app'))
root.render(
  <AllFormContextProvider>
    <RecoilRoot>
      <RecoilNexus />
      <HashRouter>
        <Suspense
          fallback={(
            <Loader
              style={{
                display: 'flex',
                justifyContent: 'center',
                alignItems: 'center',
                height: '82vh',
              }}
            />
          )}
        >
          <App />
        </Suspense>
      </HashRouter>
    </RecoilRoot>

  </AllFormContextProvider>,
)

// serviceWorker.register();
