<?php

/**
 * OneHashCRM Integration
 */

namespace BitCode\FI\Actions\OneHashCRM;

use WP_Error;
use BitCode\FI\Core\Util\HttpHelper;

/**
 * Provide functionality for OneHashCRM integration
 */
class OneHashCRMController
{
    protected $_defaultHeader;
    protected $apiEndpoint;
    protected $domain;

    private function setApiEndpoint()
    {
        return $this->apiEndpoint = "https://{$this->domain}.onehash.ai/api/resource";
    }

    private function checkValidation($fieldsRequestParams, $customParam = '**')
    {
        if (empty($fieldsRequestParams->api_key) || empty($fieldsRequestParams->api_secret) || empty($fieldsRequestParams->domain) || empty($customParam)) {
            wp_send_json_error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }
    }

    private function setHeaders($apiKey, $apiSecret)
    {
        return
            [
                "Authorization" => "token {$apiKey}:$apiSecret",
                "Content-type"  => "application/json",
            ];
    }

    public function authentication($fieldsRequestParams)
    {
        $this->checkValidation($fieldsRequestParams);
        $this->domain   = $fieldsRequestParams->domain;
        $apiKey         = $fieldsRequestParams->api_key;
        $apiSecret      = $fieldsRequestParams->api_secret;
        $apiEndpoint    = $this->setApiEndpoint() . "/Lead";
        $headers        = $this->setHeaders($apiKey, $apiSecret);
        $response       = HttpHelper::get($apiEndpoint, null, $headers);

        if (isset($response->data)) {
            wp_send_json_success('Authentication successful', 200);
        } else {
            wp_send_json_error('Please enter valid API Key & Secret or Access Api URL', 400);
        }
    }

    public function getCustomFields($fieldsRequestParams)
    {
        $this->checkValidation($fieldsRequestParams, $fieldsRequestParams->action_name);
        $this->domain   = $fieldsRequestParams->domain;
        $apiToken       = $fieldsRequestParams->api_token;

        switch ($fieldsRequestParams->action_name) {
            case 'customer':
                $actionName = 'customers';
                break;
            case 'contact':
                $actionName = 'contacts';
                break;
            case 'lead':
                $actionName = 'leads';
                break;
            case 'project':
                $actionName = 'projects';
                break;

            default:
                $actionName = '';
                break;
        }

        $apiEndpoint    = $this->setApiEndpoint() . "/custom_fields/{$actionName}";
        $headers        = $this->setHeaders($apiToken);
        $response       = HttpHelper::get($apiEndpoint, null, $headers);

        if (!isset($response->status)) {
            $fieldMap = [];
            foreach ($response as $field) {
                array_push(
                    $fieldMap,
                    (object)[
                        'key'       => $field->field_name,
                        'label'     => $field->label,
                        'required'  => $field->required === "1" ? true : false
                    ]
                );
            }
            wp_send_json_success($fieldMap, 200);
        } else {
            wp_send_json_error('Custom Fields fetching failed', 400);
        }
    }

    public function getAllCustomer($fieldsRequestParams)
    {
        $this->checkValidation($fieldsRequestParams);
        $this->domain   = $fieldsRequestParams->domain;
        $apiToken       = $fieldsRequestParams->api_token;
        $apiEndpoint    = $this->setApiEndpoint() . "/customers";
        $headers        = $this->setHeaders($apiToken);
        $response       = HttpHelper::get($apiEndpoint, null, $headers);

        if (!isset($response->status)) {
            $customers = [];
            foreach ($response as $customer) {
                array_push(
                    $customers,
                    (object)[
                        'id' => $customer->userid,
                        'name' => $customer->company
                    ]
                );
            }
            wp_send_json_success($customers, 200);
        } else {
            wp_send_json_error('Customer fetching failed', 400);
        }
    }

    public function getAllLead($fieldsRequestParams)
    {
        $this->checkValidation($fieldsRequestParams);
        $this->domain   = $fieldsRequestParams->domain;
        $apiToken       = $fieldsRequestParams->api_token;
        $apiEndpoint    = $this->setApiEndpoint() . "/leads";
        $headers        = $this->setHeaders($apiToken);
        $response       = HttpHelper::get($apiEndpoint, null, $headers);

        if (!isset($response->status)) {
            $leads = [];
            foreach ($response as $lead) {
                array_push(
                    $leads,
                    (object)[
                        'id' => $lead->id,
                        'name' => $lead->name
                    ]
                );
            }
            wp_send_json_success($leads, 200);
        } else {
            wp_send_json_error('Lead fetching failed', 400);
        }
    }

    public function getAllStaff($fieldsRequestParams)
    {
        $this->checkValidation($fieldsRequestParams);
        $this->domain   = $fieldsRequestParams->domain;
        $apiToken       = $fieldsRequestParams->api_token;
        $apiEndpoint    = $this->setApiEndpoint() . "/staffs";
        $headers        = $this->setHeaders($apiToken);
        $response       = HttpHelper::get($apiEndpoint, null, $headers);

        if (!isset($response->status)) {
            $staffs = [];
            foreach ($response as $staff) {
                array_push(
                    $staffs,
                    (object)[
                        'id' => $staff->staffid,
                        'name' => "$staff->firstname $staff->lastname"
                    ]
                );
            }
            wp_send_json_success($staffs, 200);
        } else {
            wp_send_json_error('Project Member fetching failed', 400);
        }
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId            = $integrationData->id;
        $apiToken           = $integrationDetails->api_token;
        $fieldMap           = $integrationDetails->field_map;
        $actionName         = $integrationDetails->actionName;
        $actionId           = $integrationDetails->actionId;
        $domain           = $integrationDetails->domain;

        if (empty($fieldMap) || empty($apiToken) || empty($actionName) || empty($domain)) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for OneHashCRM api', 'bit-integrations'));
        }

        $recordApiHelper        = new RecordApiHelper($integrationDetails, $integId, $apiToken, $domain);
        $oneHashCRMApiResponse   = $recordApiHelper->execute($fieldValues, $fieldMap, $actionName);

        if (is_wp_error($oneHashCRMApiResponse)) {
            return $oneHashCRMApiResponse;
        }
        return $oneHashCRMApiResponse;
    }
}
