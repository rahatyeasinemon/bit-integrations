import { useEffect, useState } from "react";
import AuthAccRadioBtn from "../../Utilities/AuthAccRadioBtn";
import bitsFetch from "../../../Utils/bitsFetch";
import { __ } from "../../../Utils/i18nwrap";


export default function GoogleSheetInfo({ sheetConf, isInfo }) {

  const [authData, setAuthData] = useState([])

  useEffect(() => {
    bitsFetch(sheetConf.authId, 'auth/getbyId').then((res) => {
      if (res.success) {
        if (res.data.data.length > 0) {
          setAuthData(res.data.data);
        }
      }
    })
  }, []);
  return (
    <div style={{ width: '900px' }}>
      {sheetConf.tokenDetails.selectedAuthType == 'Custom Authorization' && (
        <div>
          <h3>Account Details <small>(Custom Authorization)</small> </h3>
          <div className="mt-3"><b>{__('Client id:', 'bit-integrations')}</b></div>
          <input className="btcd-paper-inp w-6 mt-1" name="clientId" value={sheetConf.clientId} type="text" placeholder={__('Client id...', 'bit-integrations')} disabled={isInfo} />

          <div className="mt-3"><b>{__('Client secret:', 'bit-integrations')}</b></div>
          <input className="btcd-paper-inp w-6 mt-1" name="clientSecret" value={sheetConf.clientSecret} type="text" placeholder={__('Client secret...', 'bit-integrations')} disabled={isInfo} />

        </div>
      )}
      {(sheetConf.tokenDetails.selectedAuthType == 'One Click Authorization' && authData.length !== 0) && (
        <div>
          <h3>Account Details <small>(One Click Authorization)</small> </h3>
          <AuthAccRadioBtn
            authData={authData}
            isInfo={isInfo}
          />
        </div>
      )}
      {(sheetConf.tokenDetails.selectedAuthType == 'One Click Authorization' && authData.length === 0) && (
        <div>
          <h3>The Authorized Account Has been Deleted. <small>(One Click Authorization)</small> </h3>
        </div>
      )}

    </div>
  )
}