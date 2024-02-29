<?php

/**
 * NutshellCRM Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\NutshellCRM;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for NutshellCRM integration
 */
class NutshellCRMController
{
    protected $_defaultHeader;
    protected $apiEndpoint;

    private function setApiEndpoint()
    {
        return $this->apiEndpoint = "https://app.nutshell.com/api/v1/json";
    }

    private function checkValidation($fieldsRequestParams, $customParam = '**')
    {
        if (empty($fieldsRequestParams->user_name) || empty($fieldsRequestParams->api_token) || empty($customParam)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }
    }

    private function setHeaders($userName, $apiToken)
    {
        return
            [
                "Authorization" => 'Basic ' . base64_encode("$userName:$apiToken"),
                "Content-type"  => "application/json",
            ];
    }

    public function authentication($fieldsRequestParams)
    {
        $this->checkValidation($fieldsRequestParams);
        $userName       = $fieldsRequestParams->user_name;
        $apiToken       = $fieldsRequestParams->api_token;
        $apiEndpoint    = $this->setApiEndpoint();
        $headers        = $this->setHeaders($userName, $apiToken);
        $body = [
            'method'    => 'getUser',
            'id'        => 'randomstring',
        ];

        $response       = Http::request($apiEndpoint, 'Post', json_encode($body), $headers);


        if (isset($response->result)) {
            Response::success('Authentication successful');
        } else {
            Response::error('Please enter valid User Name & Secret or Access Api URL', 400);
        }
    }

    public function getCompanies($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->user_name || $fieldsRequestParams->api_token)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $userName       = $fieldsRequestParams->user_name;
        $apiToken       = $fieldsRequestParams->api_token;
        $apiEndpoint    = $this->setApiEndpoint();
        $headers        = $this->setHeaders($userName, $apiToken);
        $body = [
            'method'    => 'findAccounts',
            'id'        => 'randomstring',
        ];

        $response       = Http::request($apiEndpoint, 'Post', json_encode($body), $headers);

        if (isset($response->result)) {
            foreach ($response->result as $company) {
                $companies[] = [
                    'id'   => (string) $company->id,
                    'name' => $company->name
                ];
            }
            Response::success($companies);
        } else {
            Response::error('Contacts fetching failed', 400);
        }
    }

    public function getContacts($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->user_name || $fieldsRequestParams->api_token)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $userName       = $fieldsRequestParams->user_name;
        $apiToken       = $fieldsRequestParams->api_token;
        $apiEndpoint    = $this->setApiEndpoint();
        $headers        = $this->setHeaders($userName, $apiToken);
        $body = [
            'method'    => 'findContacts',
            'id'        => 'randomstring',
        ];

        $response       = Http::request($apiEndpoint, 'Post', json_encode($body), $headers);

        if (isset($response->result)) {
            foreach ($response->result as $contact) {
                $contacts[] = [
                    'id'   => (string) $contact->id,
                    'name' => $contact->name
                ];
            }
            Response::success($contacts);
        } else {
            Response::error('Contacts fetching failed', 400);
        }
    }

    public function getProducts($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->user_name || $fieldsRequestParams->api_token)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $userName       = $fieldsRequestParams->user_name;
        $apiToken       = $fieldsRequestParams->api_token;
        $apiEndpoint    = $this->setApiEndpoint();
        $headers        = $this->setHeaders($userName, $apiToken);
        $body = [
            'method'    => 'findProducts',
            'id'        => 'randomstring',
        ];

        $response       = Http::request($apiEndpoint, 'Post', json_encode($body), $headers);

        if (isset($response->result)) {
            foreach ($response->result as $product) {
                $products[] = [
                    'id'   => (string) $product->id,
                    'name' => $product->name
                ];
            }
            Response::success($products);
        } else {
            Response::error('Products fetching failed', 400);
        }
    }

    public function getSources($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->user_name || $fieldsRequestParams->api_token)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $userName       = $fieldsRequestParams->user_name;
        $apiToken       = $fieldsRequestParams->api_token;
        $apiEndpoint    = $this->setApiEndpoint();
        $headers        = $this->setHeaders($userName, $apiToken);
        $body = [
            'method'    => 'findSources',
            'id'        => 'randomstring',
        ];

        $response       = Http::request($apiEndpoint, 'Post', json_encode($body), $headers);

        if (isset($response->result)) {
            foreach ($response->result as $source) {
                $sources[] = [
                    'id'   => (string) $source->id,
                    'name' => $source->name
                ];
            }
            Response::success($sources);
        } else {
            Response::error('Sources fetching failed', 400);
        }
    }

    public function getTags($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->user_name || $fieldsRequestParams->api_token)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $userName       = $fieldsRequestParams->user_name;
        $apiToken       = $fieldsRequestParams->api_token;
        $apiEndpoint    = $this->setApiEndpoint();
        $headers        = $this->setHeaders($userName, $apiToken);
        $body = [
            'method'    => 'findTags',
            'id'        => 'randomstring',
        ];

        $response       = Http::request($apiEndpoint, 'Post', json_encode($body), $headers);

        if (isset($response->result)) {
            foreach ($response->result->Leads as $tag) {
                $tags[] = [
                    'id'   => (string) $tag,
                    'name' => $tag
                ];
            }
            Response::success($tags);
        } else {
            Response::error('Tags fetching failed', 400);
        }
    }

    public function getCompanyTypes($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->user_name || $fieldsRequestParams->api_token)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $userName       = $fieldsRequestParams->user_name;
        $apiToken       = $fieldsRequestParams->api_token;
        $apiEndpoint    = $this->setApiEndpoint();
        $headers        = $this->setHeaders($userName, $apiToken);
        $body = [
            'method'    => 'findAccountTypes',
            'id'        => 'randomstring',
        ];

        $response       = Http::request($apiEndpoint, 'Post', json_encode($body), $headers);

        if (isset($response->result)) {
            foreach ($response->result as $companyType) {
                $companyTypes[] = [
                    'id'   => (string) $companyType->id,
                    'name' => $companyType->name
                ];
            }
            Response::success($companyTypes);
        } else {
            Response::error('CompanyTypes fetching failed', 400);
        }
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId            = $integrationData->id;
        $userName             = $integrationDetails->user_name;
        $apiToken          = $integrationDetails->api_token;
        $fieldMap           = $integrationDetails->field_map;
        $actionName         = $integrationDetails->actionName;

        if (empty($fieldMap) || empty($userName) || empty($apiToken) || empty($actionName)) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for NutshellCRM api', 'bit-integrations'));
        }

        $recordApiHelper        = new RecordApiHelper($integrationDetails, $integId, $userName, $apiToken);
        $nutshellCRMApiResponse  = $recordApiHelper->execute($fieldValues, $fieldMap, $actionName);

        if (is_wp_error($nutshellCRMApiResponse)) {
            return $nutshellCRMApiResponse;
        }
        return $nutshellCRMApiResponse;
    }
}
