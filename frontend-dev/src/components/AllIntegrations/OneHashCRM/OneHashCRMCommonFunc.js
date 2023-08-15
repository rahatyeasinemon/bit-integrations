/* eslint-disable no-console */
/* eslint-disable no-else-return */
import { create } from "mutative";
import toast from "react-hot-toast";
import bitsFetch from "../../../Utils/bitsFetch";
import { __ } from "../../../Utils/i18nwrap";

export const handleInput = (e, oneHashCRMConf, setOneHashCRMConf) => {
  const newConf = { ...oneHashCRMConf };
  const { name } = e.target;
  if (e.target.value !== "") {
    newConf[name] = e.target.value;
  } else {
    delete newConf[name];
  }
  setOneHashCRMConf({ ...newConf });
};

export const generateMappedField = (oneHashCRMConf) => {
  const requiredFlds =
    oneHashCRMConf?.oneHashCRMFields &&
    oneHashCRMConf?.oneHashCRMFields.filter(
      (fld) =>
        fld.required === true && fld.key !== "owner" && fld.key !== "pipeline"
    );
  return requiredFlds.length > 0
    ? requiredFlds.map((field) => ({
        formField: "",
        oneHashCRMFormField: field.key,
      }))
    : [{ formField: "", oneHashCRMFormField: "" }];
};

export const checkMappedFields = (oneHashCRMConf) => {
  const mappedFields = oneHashCRMConf?.field_map
    ? oneHashCRMConf.field_map.filter(
        (mappedField) =>
          !mappedField.formField ||
          !mappedField.oneHashCRMFormField ||
          (mappedField.formField === "custom" && !mappedField.customValue) ||
          (mappedField.oneHashCRMFormField === "customFieldKey" &&
            !mappedField.customFieldKey)
      )
    : [];
  if (mappedFields.length > 0) {
    return false;
  }
  return true;
};

export const oneHashCRMAuthentication = (
  confTmp,
  setConf,
  setError,
  setIsAuthorized,
  loading,
  setLoading
) => {
  if (!confTmp.api_key || !confTmp.api_secret || !confTmp.domain) {
    setError({
      api_key: !confTmp.api_key
        ? __("Api Key can't be empty", "bit-integrations")
        : "",
      api_secret: !confTmp.api_secret
        ? __("Api Secret can't be empty", "bit-integrations")
        : "",
      domain: !confTmp.domain
        ? __("Access API URL can't be empty", "bit-integrations")
        : "",
    });
    return;
  }

  setError({});
  setLoading({ ...loading, auth: true });

  const requestParams = {
    api_key: confTmp.api_key,
    api_secret: confTmp.api_secret,
    domain: confTmp.domain,
  };

  bitsFetch(requestParams, "onehashcrm_authentication").then((result) => {
    if (result && result.success) {
      setIsAuthorized(true);
      setLoading({ ...loading, auth: false });
      toast.success(__("Authorized successfully", "bit-integrations"));
      return;
    }
    setLoading({ ...loading, auth: false });
    toast.error(
      __(
        "Authorized failed, Please enter valid API Key & Secret or Access Api URL",
        "bit-integrations"
      )
    );
  });
};

export const getAllCustomer = (confTmp, setConf, loading, setLoading) => {
  setLoading({ ...loading, customers: true });

  const requestParams = {
    api_token: confTmp.api_token,
    domain: confTmp.domain,
  };

  bitsFetch(requestParams, "perfexcrm_fetch_all_customers").then((result) => {
    if (result && result.success) {
      setConf((prevConf) =>
        create(prevConf, (draftConf) => {
          if (result.data) {
            draftConf.customers = result.data;
          }
        })
      );
      setLoading({ ...loading, customers: false });

      toast.success(__("Customer fetched successfully", "bit-integrations"));
      return;
    }
    setLoading({ ...loading, customers: false });
    toast.error(__("Customer Not Found!", "bit-integrations"));
  });
};
export const getAllLeads = (confTmp, setConf, loading, setLoading) => {
  setLoading({ ...loading, leads: true });

  const requestParams = {
    api_token: confTmp.api_token,
    domain: confTmp.domain,
  };

  bitsFetch(requestParams, "perfexcrm_fetch_all_leads").then((result) => {
    if (result && result.success) {
      setConf((prevConf) =>
        create(prevConf, (draftConf) => {
          if (result.data) {
            draftConf.leads = result.data;
          }
        })
      );
      setLoading({ ...loading, leads: false });

      toast.success(__("Lead fetched successfully", "bit-integrations"));
      return;
    }
    setLoading({ ...loading, leads: false });
    toast.error(__("Lead Not Found!", "bit-integrations"));
  });
};
export const getAllStaffs = (confTmp, setConf, loading, setLoading) => {
  setLoading({ ...loading, staffs: true });

  const requestParams = {
    api_token: confTmp.api_token,
    domain: confTmp.domain,
  };

  bitsFetch(requestParams, "perfexcrm_fetch_all_staffs").then((result) => {
    if (result && result.success) {
      setConf((prevConf) =>
        create(prevConf, (draftConf) => {
          if (result.data) {
            draftConf.staffs = result.data;
          }
        })
      );
      setLoading({ ...loading, staffs: false });

      toast.success(
        __("Project Member fetched successfully", "bit-integrations")
      );
      return;
    }
    setLoading({ ...loading, staffs: false });
    toast.error(__("Project Member Not Found!", "bit-integrations"));
  });
};
