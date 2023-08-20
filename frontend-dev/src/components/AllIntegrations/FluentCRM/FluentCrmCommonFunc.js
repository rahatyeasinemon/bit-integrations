import { __ } from "../../../Utils/i18nwrap";
import bitsFetch from "../../../Utils/bitsFetch";
import { create } from "mutative";

export const refreshCrmList = (
  formID,
  fluentCrmConf,
  setFluentCrmConf,
  setIsLoading,
  setSnackbar
) => {
  setIsLoading(true);
  bitsFetch({}, "refresh_fluent_crm_lists")
    .then((result) => {
      if (result && result.success) {
        const newConf = { ...fluentCrmConf };
        if (!newConf.default) {
          newConf.default = {};
        }
        if (result.data.fluentCrmList) {
          newConf.default.fluentCrmList = result.data.fluentCrmList;
        }
        if (result.data.fluentCrmTags) {
          newConf.default.fluentCrmTags = result.data.fluentCrmTags;
        }
        setSnackbar({
          show: true,
          msg: __("FluentCRM list refreshed", "bit-integrations"),
        });
      } else if (
        (result && result.data && result.data.data) ||
        (!result.success && typeof result.data === "string")
      ) {
        setSnackbar({
          show: true,
          msg: `${__(
            "FluentCRM list refresh failed Cause:",
            "bit-integrations"
          )}${result.data.data || result.data}. ${__(
            "please try again",
            "bit-integrations"
          )}`,
        });
      } else {
        setSnackbar({
          show: true,
          msg: __(
            "FluentCRM list refresh failed. please try again",
            "bit-integrations"
          ),
        });
      }
      setIsLoading(false);
    })
    .catch(() => setIsLoading(false));
};

export const refreshCrmTag = (
  formID,
  fluentCrmConf,
  setFluentCrmConf,
  setIsLoading,
  setSnackbar
) => {
  setIsLoading(true);
  bitsFetch({}, "refresh_fluent_crm_tags")
    .then((result) => {
      if (result && result.success) {
        setFluentCrmConf((prevConf) =>
          create(prevConf, (newConf) => {
            if (!newConf.default) {
              newConf.default = {};
            }
            if (result.data.fluentCrmTags) {
              newConf.fluentCrmTags = result.data.fluentCrmTags;
            }
          })
        );
        setSnackbar({
          show: true,
          msg: __("FluentCRM Tags refreshed", "bit-integrations"),
        });
      } else if (
        (result && result.data && result.data.data) ||
        (!result.success && typeof result.data === "string")
      ) {
        setSnackbar({
          show: true,
          msg: `${__(
            "FluentCRM tags refresh failed Cause:",
            "bit-integrations"
          )}${result.data.data || result.data}. ${__(
            "please try again",
            "bit-integrations"
          )}`,
        });
      } else {
        setSnackbar({
          show: true,
          msg: __(
            "FluentCRM tags refresh failed. please try again",
            "bit-integrations"
          ),
        });
      }
      setIsLoading(false);
    })
    .catch(() => setIsLoading(false));
};

export const refreshfluentCrmHeader = (
  fluentCrmConf,
  setFluentCrmConf,
  setIsLoading,
  setSnackbar
) => {
  setIsLoading(true);
  bitsFetch({}, "fluent_crm_headers")
    .then((result) => {
      if (result && result.success) {
        if (result.data.fluentCrmFlelds) {
          setFluentCrmConf((prevConf) =>
            create(prevConf, (newConf) => {
              newConf.fields = result.data.fluentCrmFlelds;
              newConf.field_map = mapNewRequiredFields(newConf);
            })
          );
          setSnackbar({
            show: true,
            msg: __("Fluent CRM fields refreshed", "bit-integrations"),
          });
        } else {
          setSnackbar({
            show: true,
            msg: __(
              "No Fluent CRM fields found. Try changing the header row number or try again",
              "bit-integrations"
            ),
          });
        }
      } else {
        setSnackbar({
          show: true,
          msg: __(
            "Fluent CRM fields refresh failed. please try again",
            "bit-integrations"
          ),
        });
      }
      setIsLoading(false);
    })
    .catch(() => setIsLoading(false));
};

export const mapNewRequiredFields = (fluentCrmConf) => {
  const { field_map } = fluentCrmConf;
  const { fields } = fluentCrmConf;
  const required = Object.values(fields)
    .filter((f) => f.required)
    .map((f) => ({ formField: "", fluentCRMField: f.key, required: true }));
  const requiredFieldNotInFieldMap = required.filter(
    (f) => !field_map.find((m) => m.fluentCRMField === f.fluentCRMField)
  );
  const notEmptyFieldMap = field_map.filter(
    (f) => f.fluentCRMField || f.formField
  );
  const newFieldMap = notEmptyFieldMap.map((f) => {
    const field = fields[f.fluentCRMField];
    if (field) {
      return { ...f, formField: field.label };
    }
    return f;
  });
  return [...requiredFieldNotInFieldMap, ...newFieldMap];
};

export const handleInput = (e, fluentCrmConf, setFluentCrmConf) => {
  const newConf = { ...fluentCrmConf };
  newConf.name = e.target.value;
  setFluentCrmConf({ ...newConf });
};
export const checkMappedFields = (fluentCrmConf) => {
  const mappedFields = fluentCrmConf?.field_map
    ? fluentCrmConf.field_map.filter(
        (mappedField) =>
          !mappedField.formField &&
          mappedField.fluentCRMField &&
          mappedField.required
      )
    : [];
  if (mappedFields.length > 0) {
    return false;
  }
  return true;
};
