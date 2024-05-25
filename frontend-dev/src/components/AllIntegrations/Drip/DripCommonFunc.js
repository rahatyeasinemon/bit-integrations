// eslint-disable-next-line import/no-extraneous-dependencies
import { __ } from "@wordpress/i18n";
import bitsFetch from "../../../Utils/bitsFetch";
import toast from "react-hot-toast";

export const handleInput = (e, dripConf, setDripConf) => {
  const newConf = { ...dripConf };
  newConf.name = e.target.value;
  setDripConf({ ...newConf });
};

export const dripAuthentication = (dripConf, setDripConf, setError, setisAuthorized, loading, setLoading, type = 'authentication') => {
  const newConf = { ...dripConf }

  if (!newConf.name || !newConf.api_token) {
    setError({
      name: !newConf.name ? __('Integration name cann\'t be empty', 'bit-integrations') : '',
      api_token: !newConf.api_token ? __('Access Api Token Key cann\'t be empty', 'bit-integrations') : '',
    })
    return
  }

  let responseErrorMsg

  if (type === 'authentication') {
    setLoading({ ...loading, auth: true })
    responseErrorMsg = 'Authorization Failed'
  } else if (type === 'accounts') {
    setLoading({ ...loading, accounts: true })
    responseErrorMsg = 'Accounts fetching failed'
  }

  const data = {
    api_token: newConf.api_token,
  }

  bitsFetch(data, 'drip_authorize')
    .then(result => {
      if (result?.success) {
        newConf.accounts = result.data
        if (type === 'authentication') {
          setisAuthorized(true)
          toast.success('Authorized Successfully')
        } else if (type === 'accounts') {
          toast.success('Accounts fetched Successfully')
        }
      } else {
        toast.error(responseErrorMsg)
      }

      setDripConf({ ...newConf })
      setLoading({ ...loading, auth: false, accounts: false })
    })
}

// refreshMappedCampaigns
export const refreshDripCampaign = (
  dripConf,
  setDripConf,
  setIsLoading,
  setSnackbar
) => {
  const refreshCampaignsRequestParams = {
    api_token: dripConf.api_token,
    account_id: dripConf.account_id,
  };
  bitsFetch(refreshCampaignsRequestParams, "drip_campaigns")
    .then((result) => {
      if (result && result.success) {
        const newConf = { ...dripConf };
        if (result.data.dripCampaigns) {
          if (!newConf.default) {
            newConf.default = {};
          }
          newConf.default.dripCampaigns = result.data.dripCampaigns;
          setSnackbar({
            show: true,
            msg: __("Drip campaigns refreshed", "bit-integrations"),
          });
        } else {
          setSnackbar({
            show: true,
            msg: __(
              "No Drip campaigns found. Try changing the header row number or try again",
              "bit-integrations"
            ),
          });
        }

        setDripConf({ ...newConf });
      } else {
        setSnackbar({
          show: true,
          msg: __(
            "Drip campaigns refresh failed. please try again",
            "bit-integrations"
          ),
        });
      }
      setIsLoading(false);
    })
    .catch(() => setIsLoading(false));
};

// refreshMappedFields
export const refreshDripHeader = (
  dripConf,
  setDripConf,
  setIsLoading,
  setSnackbar
) => {
  const refreshCampaignsRequestParams = {
    api_token: dripConf.api_token,
    campaign_id: dripConf.campaignId,
  };

  bitsFetch(refreshCampaignsRequestParams, "drip_headers")
    .then((result) => {
      if (result && result.success) {
        const newConf = { ...dripConf };
        if (result.data.dripField) {
          if (!newConf.default) {
            newConf.default = {};
          }

          newConf.default.fields = result.data.dripField;
          const { fields } = newConf.default;
          newConf.field_map = Object.values(fields)
            .filter((f) => f.required)
            .map((f) => ({
              formField: "",
              dripField: f.fieldValue,
              required: true,
            }));
          setSnackbar({
            show: true,
            msg: __("Drip fields refreshed", "bit-integrations"),
          });
        } else {
          setSnackbar({
            show: true,
            msg: __(
              "No Drip fields found. Try changing the header row number or try again",
              "bit-integrations"
            ),
          });
        }

        setDripConf({ ...newConf });
      } else {
        setSnackbar({
          show: true,
          msg: __(
            "Drip fields refresh failed. please try again",
            "bit-integrations"
          ),
        });
      }
      setIsLoading(false);
    })
    .catch(() => setIsLoading(false));
};

export const checkMappedFields = (dripConf) => {
  const mappedFields = dripConf?.field_map
    ? dripConf.field_map.filter(
      (mappedField) =>
        !mappedField.formField ||
        !mappedField.dripField ||
        (!mappedField.formField === 'custom' && !mappedField.customValue)
    )
    : [];
  if (mappedFields.length > 0) {
    return false;
  }
  return true;
};
