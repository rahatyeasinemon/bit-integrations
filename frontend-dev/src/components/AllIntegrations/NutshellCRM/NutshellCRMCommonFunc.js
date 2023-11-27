/* eslint-disable no-console */
/* eslint-disable no-else-return */
import toast from "react-hot-toast";
import bitsFetch from "../../../Utils/bitsFetch";
import { __ } from "../../../Utils/i18nwrap";

export const handleInput = (e, nutshellCRMConf, setNutshellCRMConf) => {
  const newConf = { ...nutshellCRMConf };
  const { name } = e.target;
  if (e.target.value !== "") {
    newConf[name] = e.target.value;
  } else {
    delete newConf[name];
  }
  setNutshellCRMConf({ ...newConf });
};

export const generateMappedField = (nutshellCRMConf) => {
  const requiredFlds =
    nutshellCRMConf?.nutshellCRMFields &&
    nutshellCRMConf?.nutshellCRMFields.filter(
      (fld) =>
        fld.required === true && fld.key !== "owner" && fld.key !== "pipeline"
    );
  return requiredFlds.length > 0
    ? requiredFlds.map((field) => ({
        formField: "",
        nutshellCRMFormField: field.key,
      }))
    : [{ formField: "", nutshellCRMFormField: "" }];
};

export const checkMappedFields = (nutshellCRMConf) => {
  const mappedFields = nutshellCRMConf?.field_map
    ? nutshellCRMConf.field_map.filter(
        (mappedField) =>
          !mappedField.formField ||
          !mappedField.nutshellCRMFormField ||
          (mappedField.formField === "custom" && !mappedField.customValue) ||
          (mappedField.nutshellCRMFormField === "customFieldKey" &&
            !mappedField.customFieldKey)
      )
    : [];
  if (mappedFields.length > 0) {
    return false;
  }
  return true;
};

export const nutshellCRMAuthentication = (
  confTmp,
  setConf,
  setError,
  setIsAuthorized,
  loading,
  setLoading
) => {
  if (!confTmp.user_name || !confTmp.api_token) {
    setError({
      user_name: !confTmp.user_name
        ? __("User Name can't be empty", "bit-integrations")
        : "",
      api_token: !confTmp.api_token
        ? __("API Token can't be empty", "bit-integrations")
        : "",
    });
    return;
  }

  setError({});
  setLoading({ ...loading, auth: true });

  const requestParams = {
    user_name: confTmp.user_name,
    api_token: confTmp.api_token,
  };

  bitsFetch(requestParams, "nutshellcrm_authentication").then((result) => {
    if (result && result.success) {
      setIsAuthorized(true);
      setLoading({ ...loading, auth: false });
      toast.success(__("Authorized successfully", "bit-integrations"));
      return;
    }
    setLoading({ ...loading, auth: false });
    toast.error(
      __(
        "Authorized failed, Please enter valid User Name & Api Token",
        "bit-integrations"
      )
    );
  });
};
