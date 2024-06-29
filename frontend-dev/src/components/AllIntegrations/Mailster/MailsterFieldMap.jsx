/* eslint-disable no-console */
import { useRecoilValue } from "recoil";
import { __ } from "../../../Utils/i18nwrap";
import MtInput from "../../Utilities/MtInput";
import {
  addFieldMap,
  delFieldMap,
  handleFieldMapping,
} from "./IntegrationHelpers";
import { SmartTagField } from "../../../Utils/StaticData/SmartTagField";
import { $btcbi } from "../../../GlobalStates";
import { generateMappedField } from "./MailsterCommonFunc";
import TagifyInput from "../../Utilities/TagifyInput";
import { handleCustomValue } from "../IntegrationHelpers/IntegrationHelpers";
import Cooltip from "../../Utilities/Cooltip";

export default function MailsterFieldMap({
  i,
  formFields,
  field,
  mailsterConf,
  setMailsterConf,
}) {
  const requiredFields =
    mailsterConf?.staticFields.filter((fld) => fld.required === true) || [];
  const customFields =
    mailsterConf?.staticFields?.filter((fld) => fld.required === false) || [];

  if (
    mailsterConf?.field_map?.length === 1 &&
    field.mailsterFormField === ""
  ) {
    const newConf = { ...mailsterConf };
    const tmp = generateMappedField(newConf);
    newConf.field_map = tmp;
    setMailsterConf(newConf);
  }

  let statusField = false;

  if (field.mailsterFormField === "status") {
    statusField = true;
  }

  const btcbi = useRecoilValue($btcbi);
  const { isPro } = btcbi;

  return (
    <div className="flx mt-2 mb-2 btcbi-field-map">
      <div className="pos-rel flx">
        <div className="flx integ-fld-wrp">
          <select
            className="btcd-paper-inp mr-2"
            name="formField"
            value={field.formField || ""}
            onChange={(ev) =>
              handleFieldMapping(ev, i, mailsterConf, setMailsterConf)
            }
          >
            <option value="">{__("Select Field", "bit-integrations")}</option>
            <optgroup label="Form Fields">
              {formFields?.map((f) => (
                <option key={`ff-rm-${f.name}`} value={f.name}>
                  {f.label}
                </option>
              ))}
            </optgroup>
            <option value="custom">
              {__("Custom...", "bit-integrations")}
            </option>
            <optgroup label={`General Smart Codes ${isPro ? "" : "(PRO)"}`}>
              {isPro &&
                SmartTagField?.map((f) => (
                  <option key={`ff-rm-${f.name}`} value={f.name}>
                    {f.label}
                  </option>
                ))}
            </optgroup>
          </select>

          {field.formField === "custom" && (
            <TagifyInput
              onChange={(e) =>
                handleCustomValue(e, i, mailsterConf, setMailsterConf)
              }
              label={__("Custom Value", "bit-integrations")}
              className="mr-2"
              type="text"
              value={field.customValue}
              placeholder={__("Custom Value", "bit-integrations")}
              formFields={formFields}
            />
          )}

          <select
            className="btcd-paper-inp"
            disabled={i < requiredFields.length}
            name="mailsterFormField"
            value={
              i < requiredFields
                ? requiredFields[i].label || ""
                : field.mailsterFormField || ""
            }
            onChange={(ev) =>
              handleFieldMapping(ev, i, mailsterConf, setMailsterConf)
            }
          >
            <option value="">{__("Select Field", "bit-integrations")}</option>
            {i < requiredFields.length ? (
              <option key={requiredFields[i].key} value={requiredFields[i].key}>
                {requiredFields[i].label}
              </option>
            ) : (
              customFields.map(({ key, label }) => (
                <option key={key} value={key}>
                  {label}
                </option>
              ))
            )}
          </select>
        </div>
        <div className="flx integ-fld-wrp">
          {statusField && (
            <div>
              <Cooltip width={350} icnSize={17} className="ml-2">
                <div className="txt-body">
                  <p>
                    Below are the available statuses for mailster (single
                    letters are the value):
                  </p>
                  <li>Confirmed = C</li>
                  <li>Not Confirmed = S</li>
                  <li>Unsubscribed = U</li>
                  <li>Bounced = B</li>
                  <li>Complained = P</li>
                  <p>
                    <strong>Note: </strong>you have to insert one of the
                    mentioned (C, S, U, B, or P) statuses; otherwise, subscriber
                    adding will fail. You can also omit this status field; if
                    omitted, the status will be set to "Confirmed (C)" by
                    default.
                  </p>
                </div>
              </Cooltip>
            </div>
          )}
          {i >= requiredFields.length && (
            <>
              <button
                onClick={() =>
                  addFieldMap(i, mailsterConf, setMailsterConf)
                }
                className="icn-btn sh-sm ml-2 mr-1"
                type="button"
              >
                +
              </button>
              <button
                onClick={() =>
                  delFieldMap(i, mailsterConf, setMailsterConf)
                }
                className="icn-btn sh-sm ml-1"
                type="button"
                aria-label="btn"
              >
                <span className="btcd-icn icn-trash-2" />
              </button>
            </>
          )}
        </div>
      </div>
    </div>
  );
}
