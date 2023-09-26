/* eslint-disable no-console */
/* eslint-disable no-else-return */
import toast from "react-hot-toast";
import bitsFetch from "../../../Utils/bitsFetch";
import { __ } from "../../../Utils/i18nwrap";

export const handleInput = (e, woodpeckerConf, setWoodpeckerConf) => {
  const newConf = { ...woodpeckerConf };
  const { name } = e.target;
  if (e.target.value !== "") {
    newConf[name] = e.target.value;
  } else {
    delete newConf[name];
  }
  setWoodpeckerConf({ ...newConf });
};

export const generateMappedField = (woodpeckerConf) => {
  const requiredFlds =
    woodpeckerConf?.woodpeckerAllFields &&
    woodpeckerConf?.woodpeckerAllFields.filter(
      (fld) =>
        fld.required === true && fld.key !== "owner" && fld.key !== "pipeline"
    );
  return requiredFlds.length > 0
    ? requiredFlds.map((field) => ({
        formField: "",
        woodpeckerFormField: field.key,
      }))
    : [{ formField: "", woodpeckerFormField: "" }];
};

export const checkMappedFields = (woodpeckerConf) => {
  const mappedFields = woodpeckerConf?.field_map
    ? woodpeckerConf.field_map.filter(
        (mappedField) =>
          !mappedField.formField ||
          !mappedField.woodpeckerFormField ||
          (mappedField.formField === "custom" && !mappedField.customValue) ||
          (mappedField.woodpeckerFormField === "customFieldKey" &&
            !mappedField.customFieldKey)
      )
    : [];
  if (mappedFields.length > 0) {
    return false;
  }
  return true;
};

export const woodpeckerAuthentication = (
  confTmp,
  setError,
  setIsAuthorized,
  loading,
  setLoading
) => {
  if (!confTmp.api_key) {
    setError({
      api_key: !confTmp.api_key
        ? __("Api Key can't be empty", "bit-integrations")
        : "",
    });
    return;
  }

  setError({});
  setLoading({ ...loading, auth: true });

  const requestParams = {
    api_key: confTmp.api_key,
  };

  bitsFetch(requestParams, "woodpecker_authentication").then((result) => {
    if (result && result.success) {
      setIsAuthorized(true);
      setLoading({ ...loading, auth: false });
      toast.success(__("Authorized successfully", "bit-integrations"));
      return;
    }
    setLoading({ ...loading, auth: false });
    toast.error(
      __("Authorized failed, Please enter valid API Key", "bit-integrations")
    );
  });
};

export const woodpeckerFields = (
  woodpeckerConf,
  setWoodpeckerConf,
  setIsLoading,
  setSnackbar
) => {
  setIsLoading(true);
  const requestParams = {
    api_key: woodpeckerConf.api_key,
    action_name: woodpeckerConf.actionName,
  };

  bitsFetch(requestParams, "Woodpecker_custom_fields")
    .then((result) => {
      if (result && result.success) {
        setWoodpeckerConf((prevConf) => {
          const draftConf = prevConf;
          draftConf.field_map = [{ formField: "", salesmateFormField: "" }];
          if (result.data) {
            draftConf.woodpeckerAllFields = [];
            draftConf.woodpeckerAllFields = [
              ...draftConf.woodpeckerFields,
              ...result.data,
            ];
          }
          draftConf.field_map = generateMappedField(draftConf);
          return draftConf;
        });
        setSnackbar({
          show: true,
          msg: __("Woodpecker custom fields refreshed", "bit-integrations"),
        });
      } else {
        setSnackbar({
          show: true,
          msg: __("Woodpecker custom fields not found.", "bit-integrations"),
        });
      }
      setIsLoading(false);
    })
    .catch(() => setIsLoading(false));
};

export const getAllTags = (woodpeckerConf, setWoodpeckerConf, setLoading) => {
  setLoading({ ...setLoading, tags: true });
  const requestParams = {
    api_key: woodpeckerConf.api_key,
  };

  bitsFetch(requestParams, "Woodpecker_fetch_all_tags").then((result) => {
    if (result && result.success) {
      setWoodpeckerConf((prevConf) => {
        const draftConf = { ...prevConf };
        draftConf.tags = result.data;
        return draftConf;
      });

      toast.success(__("Tags fetched successfully", "bit-integrations"));
      setLoading({ ...setLoading, tags: false });
      return;
    }
    setLoading({ ...setLoading, tags: false });
    toast.error(__("Tags fetching failed", "bit-integrations"));
  });
};

export const getallAccounts = (
  woodpeckerConf,
  setWoodpeckerConf,
  loading,
  setLoading
) => {
  setLoading({ ...loading, account: true });
  const requestParams = {
    api_key: woodpeckerConf.api_key,
  };

  bitsFetch(requestParams, "Woodpecker_fetch_all_account").then((result) => {
    if (result && result.success) {
      setWoodpeckerConf((prevConf) => {
        const draftConf = { ...prevConf };
        draftConf.accounts = result.data;
        return draftConf;
      });

      toast.success(__("Accounts fetched successfully", "bit-integrations"));
      setLoading({ ...loading, account: false });
      return;
    }
    setLoading({ ...loading, account: false });
    toast.error(__("Accounts fetching failed", "bit-integrations"));
  });
};
export const getallPipelines = (
  woodpeckerConf,
  setWoodpeckerConf,
  loading,
  setLoading
) => {
  setLoading({ ...loading, pipeline: true });
  const requestParams = {
    api_key: woodpeckerConf.api_key,
  };

  bitsFetch(requestParams, "Woodpecker_fetch_all_pipelines").then((result) => {
    if (result && result.success) {
      setWoodpeckerConf((prevConf) => {
        const draftConf = { ...prevConf };
        draftConf.pipelines = result.data;
        return draftConf;
      });

      toast.success(__("pipelines fetched successfully", "bit-integrations"));
      setLoading({ ...loading, pipeline: false });
      return;
    }
    setLoading({ ...loading, pipeline: false });
    toast.error(__("pipelines fetching failed", "bit-integrations"));
  });
};
