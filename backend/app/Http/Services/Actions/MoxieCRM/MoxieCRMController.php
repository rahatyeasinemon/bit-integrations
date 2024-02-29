<?php

/**
 * MoxieCRM Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\MoxieCRM;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for MoxieCRM integration
 */
class MoxieCRMController
{
    protected $_defaultHeader;
    protected $apiEndpoint;

    // public function __construct()
    // {
    //     $this->apiEndpoint = "https://api.moxie.com/developer_api/v1";
    // }

    public function authentication($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->api_key)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiKey      = $fieldsRequestParams->api_key;
        $apiEndpoint = 'https://' . $fieldsRequestParams->api_url . "/api/public/action/users/list";
        $headers = [
            "X-API-KEY"  => $apiKey,
            "Content-Type"      => "application/json"
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $headers);

        if (!isset($response->error)) {
            Response::success('Authentication successful');
        } else {
            Response::error('Please enter valid API key', 400);
        }
    }

    // public function getCustomFields($fieldsRequestParams)
    // {
    //     if (empty($fieldsRequestParams->api_key)) {
    //         Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
    //     }

    //     $apiKey      = $fieldsRequestParams->api_key;
    //     $action      = $fieldsRequestParams->action;
    //     $apiUrl    = $fieldsRequestParams->api_url;

    //     $apiEndpoint = $this->apiEndpoint . "/custom_field_definitions";
    //     $headers = [
    //         "X-API-KEY"  => $apiKey,
    //         "Content-Type"      => "application/json"
    //     ];

    //     $response = Http::request($apiEndpoint, 'Get', null, $headers);
    //     if (isset($response)) {
    //         foreach ($response as $customField) {
    //             if (in_array($action, $customField->available_on)) {
    //                 $customFields[] = [
    //                     'key' => $customField->id,
    //                     'label' => $customField->name,
    //                 ];
    //             }
    //         }
    //         Response::success($customFields);
    //     } else {
    //         Response::error('Custom field fetching failed', 400);
    //     }
    // }

    public function getAllOpportunities($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->api_key)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiKey      = $fieldsRequestParams->api_key;
        $apiEndpoint = $this->apiEndpoint . "/opportunities";
        $headers = [
            "Authorization" => 'Bearer ' . $apiKey,
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $headers);

        if (isset($response->opportunities)) {
            foreach ($response->opportunities as $opportunity) {
                $opportunities[] = [
                    'id'   => (string) $opportunity->id,
                    'name' => $opportunity->name
                ];
            }
            Response::success($opportunities);
        } else {
            Response::error('Opportunity fetching failed', 400);
        }
    }

    public function getAllClients($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->api_key)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }
        $apiKey      = $fieldsRequestParams->api_key;
        $apiUrl     = $fieldsRequestParams->api_url;
        $apiEndpoint = 'https://' . $apiUrl . "/api/public/action/clients/list";
        $headers = [
            "X-API-KEY"  => $apiKey,
            "Content-Type"      => "application/json"
        ];


        $response = Http::request($apiEndpoint, 'Get', null, $headers);

        if (isset($response)) {
            foreach ($response as $client) {
                $clients[] = [
                    'id'   => (string) $client->id,
                    'name' => $client->name
                ];
            }
            Response::success($clients);
        } else {
            Response::error('Clients fetching failed', 400);
        }
    }


    public function getAllPipelineStages($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->api_key)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiKey      = $fieldsRequestParams->api_key;
        $apiEndpoint = 'https://' . $fieldsRequestParams->api_url . "/api/public/action/pipelineStages/list";
        $headers = [
            "X-API-KEY"  => $apiKey,
            "Content-Type"      => "application/json"
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $headers);

        if (isset($response)) {
            foreach ($response as $pipelineStage) {
                $pipelineStages[] = [
                    'id'   => $pipelineStage->id,
                    'name' => $pipelineStage->label
                ];
            }
            Response::success($pipelineStages);
        } else {
            Response::error('PipelineStages fetching failed', 400);
        }
    }

    public function getAllCRMPeoples($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->api_key)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiKey      = $fieldsRequestParams->api_key;
        $apiUrl     = $fieldsRequestParams->api_url;
        $apiEndpoint = $this->apiEndpoint . "/people/search";
        $headers = [
            "X-API-KEY"  => $apiKey,
            "Content-Type"      => "application/json"
        ];

        $response = Http::request($apiEndpoint, 'Post', null, $headers);

        if (!empty($response)) {
            foreach ($response as $people) {
                $peoples[] = [
                    'id'   => (string) $people->id,
                    'name' => $people->name
                ];
            }
            Response::success($peoples);
        } else {
            Response::error('Peoples fetching failed', 400);
        }
    }

    public function getAllCRMPipelines($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->api_key)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiKey      = $fieldsRequestParams->api_key;
        $apiUrl     = $fieldsRequestParams->api_url;
        $apiEndpoint = $this->apiEndpoint . "/pipelines";
        $headers = [
            "X-API-KEY"  => $apiKey,
            "Content-Type"      => "application/json"
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $headers);

        if (!empty($response)) {
            foreach ($response as $pipeline) {
                $pipelines[] = [
                    'id'   => (string) $pipeline->id,
                    'name' => $pipeline->name
                ];
            }
            Response::success($pipelines);
        } else {
            Response::error('Pipelines fetching failed', 400);
        }
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId            = $integrationData->id;
        $authToken          = $integrationDetails->api_key;
        $fieldMap           = $integrationDetails->field_map;
        $actionName         = $integrationDetails->actionName;

        if (empty($fieldMap) || empty($authToken) || empty($actionName)) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for MoxieCRM api', 'bit-integrations'));
        }

        $recordApiHelper   = new RecordApiHelper($integrationDetails, $integId);
        $moxiecrmApiResponse = $recordApiHelper->execute($fieldValues, $fieldMap, $actionName);

        if (is_wp_error($moxiecrmApiResponse)) {
            return $moxiecrmApiResponse;
        }
        return $moxiecrmApiResponse;
    }
}
