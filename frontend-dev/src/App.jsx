/* eslint-disable no-undef */
/* eslint-disable react/jsx-props-no-spreading */
/* eslint-disable no-console */
/* eslint-disable react/jsx-one-expression-per-line */

import { lazy, Suspense, useEffect } from 'react'
import { Routes, Route, NavLink, Link } from 'react-router-dom'
import './resource/sass/app.scss'
// eslint-disable-next-line import/no-extraneous-dependencies

import { Toaster } from 'react-hot-toast'
import { __ } from './Utils/i18nwrap'
import './resource/icons/style.css'
import Loader from './components/Loaders/Loader'
import logo from '../logo.svg'
import Integrations from './components/Integrations'
import TableLoader from './components/Loaders/TableLoader'
import Settings from './pages/Settings'
import FlowBuilder from './pages/FlowBuilder'

const AllIntegrations = lazy(() => import('./pages/AllIntegrations'))
const Error404 = lazy(() => import('./pages/Error404'))

function App() {
  const loaderStyle = { height: '82vh' }

  useEffect(() => { removeUnwantedCSS() }, [])


  return (
    <Suspense fallback={(<Loader className="g-c" style={loaderStyle} />)}>
      <Toaster
        position="bottom-right"
        containerStyle={{ inset: '-25px 30px 20px -10px' }}
        toastOptions={{
          duration: 4000,
          style: {
            background: '#000000',
            color: '#fff',
            bottom: 40,
            padding: '15px 18px',
            boxShadow: '0 0px 7px rgb(0 0 0 / 30%), 0 3px 30px rgb(0 0 0 / 20%)',
          },
        }}
      />

      <div className="Btcd-App">

        <div className="nav-wrp">
          <div className="flx">
            <div className="logo flx" title={__('Integrations for Forms')}>
              <Link to="/" className="flx">
                <img src={logo} alt="logo" style={{ marginLeft: '8px' }} />
                <span className="ml-2">Bit Integrations</span>
              </Link>
            </div>
            <nav className="top-nav ml-2">
              <NavLink
                to="/"
                className={({ isActive }) => (isActive ? 'app-link-active' : '')}
              >
                {__('Integrations', 'bit-integrations')}
              </NavLink>
              <NavLink
                to="/app-settings"
                className={({ isActive }) => (isActive ? 'app-link-active' : '')}
              >
                {__('Settings', 'bit-integrations')}
              </NavLink>
            </nav>
          </div>
        </div>

        <div className="route-wrp">
          <Routes>
            <Route
              path="/"
              element={(
                <Suspense fallback={<TableLoader />}>
                  <AllIntegrations />
                </Suspense>
              )}
            />

            <Route
              path="/app-settings"
              element={(
                <Suspense fallback={<Loader className="g-c" style={loaderStyle} />}>
                  <Settings />
                </Suspense>
              )}
            />

            <Route
              path="/flow/new"
              element={
                <Suspense fallback={<Loader className="g-c" style={loaderStyle} />}>
                  <FlowBuilder />
                </Suspense>
              }
            />

            <Route
              path="/flow/action/*"
              element={(
                <Suspense fallback={<Loader className="g-c" style={loaderStyle} />}>
                  <Integrations />
                </Suspense>
              )}
            />

            <Route path="*" element={<Error404 />} />

          </Routes>
        </div>
      </div>
    </Suspense>
  )
}

function removeUnwantedCSS() {
  const conflictStyles = ['bootstrap']
  const styles = document.styleSheets

  for (let i = 0; i < styles.length; i += 1) {
    if (styles[i].href !== null) {
      const regex = new RegExp(conflictStyles.join('.*css|'), 'gi')
      if (styles[i]?.href.match(regex)) {
        styles[i].disabled = true
      }
    }
  }
}

export default App
