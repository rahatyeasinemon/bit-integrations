<?php

/**
 * AgiledCRM Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\AgiledCRM;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for AgiledCRM integration
 */
class AgiledCRMController
{
    protected $_defaultHeader;

    public function authentication($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->auth_token) || empty($fieldsRequestParams->brand)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $brand       = $fieldsRequestParams->brand;
        $apiKey      = $fieldsRequestParams->auth_token;
        $apiEndpoint = "https://my.agiled.app/api/v1/users?api_token=$apiKey";
        $header      = [
            'Brand' => $brand
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $header);

        if (isset($response->data[0]->id)) {
            Response::success('Authentication successful');
        } else {
            Response::error('Please enter valid Brand name & API key', 400);
        }
    }

    public function getAllOwners($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->auth_token) || empty($fieldsRequestParams->brand)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiKey      = $fieldsRequestParams->auth_token;
        $brand       = $fieldsRequestParams->brand;
        $apiEndpoint = "https://my.agiled.app/api/v1/sales-agents?api_token=$apiKey";
        $header      = [
            'Brand' => $brand
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $header);

        if (isset($response->data[0]->id)) {
            foreach ($response->data as $owner) {
                $owners[] = [
                    'id'   => (string) $owner->id,
                    'name' => $owner->user->name . ' ' . $owner->user->last_name
                ];
            }
            Response::success($owners);
        } else {
            Response::error('Owners fetching failed', 400);
        }
    }

    public function getAllAccounts($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->auth_token) || empty($fieldsRequestParams->brand)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiKey      = $fieldsRequestParams->auth_token;
        $brand       = $fieldsRequestParams->brand;
        $apiEndpoint = "https://my.agiled.app/api/v1/accounts?api_token=$apiKey";
        $header      = [
            'Brand' => $brand
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $header);

        if (!empty($response->data)) {
            foreach ($response->data as $account) {
                $accounts[] = [
                    'id'   => (string) $account->id,
                    'name' => $account->name
                ];
            }
            Response::success($accounts);
        } else {
            Response::error('Owners fetching failed', 400);
        }
    }

    public function getAllSources($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->auth_token) || empty($fieldsRequestParams->brand)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiKey      = $fieldsRequestParams->auth_token;
        $brand       = $fieldsRequestParams->brand;
        $apiEndpoint = "https://my.agiled.app/api/v1/crm-sources?api_token=$apiKey";
        $header      = [
            'Brand' => $brand
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $header);

        if (!empty($response->data)) {
            foreach ($response->data as $source) {
                $sources[] = [
                    'id'   => (string) $source->id,
                    'name' => $source->type
                ];
            }
            Response::success($sources);
        } else {
            Response::error('Owners fetching failed', 400);
        }
    }

    public function getAllStatuses($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->auth_token) || empty($fieldsRequestParams->brand)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiKey      = $fieldsRequestParams->auth_token;
        $brand       = $fieldsRequestParams->brand;
        $apiEndpoint = "https://my.agiled.app/api/v1/crm-statuses?api_token=$apiKey";
        $header      = [
            'Brand' => $brand
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $header);

        if (!empty($response->data)) {
            foreach ($response->data as $status) {
                $statuses[] = [
                    'id'   => (string) $status->id,
                    'name' => $status->type
                ];
            }
            Response::success($statuses);
        } else {
            Response::error('Owners fetching failed', 400);
        }
    }

    public function getAllLifeCycleStage($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->auth_token) || empty($fieldsRequestParams->brand)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiKey      = $fieldsRequestParams->auth_token;
        $brand       = $fieldsRequestParams->brand;
        $apiEndpoint = "https://my.agiled.app/api/v1/crm-stages?api_token=$apiKey";
        $header      = [
            'Brand' => $brand
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $header);

        if (!empty($response->data)) {
            foreach ($response->data as $lifeCycleStage) {
                $lifeCycleStages[] = [
                    'id'   => (string) $lifeCycleStage->id,
                    'name' => $lifeCycleStage->type
                ];
            }
            Response::success($lifeCycleStages);
        } else {
            Response::error('Owners fetching failed', 400);
        }
    }

    public function getAllCRMPipelines($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->auth_token) || empty($fieldsRequestParams->brand)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiKey      = $fieldsRequestParams->auth_token;
        $brand       = $fieldsRequestParams->brand;
        $apiEndpoint = "https://my.agiled.app/api/v1/crm/pipelines?api_token=$apiKey";
        $header      = [
            'Brand' => $brand
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $header);

        if (!empty($response->data)) {
            foreach ($response->data as $pipeline) {
                $pipelines[] = [
                    'id'   => (string) $pipeline->id,
                    'name' => $pipeline->pipeline_name
                ];
            }
            Response::success($pipelines);
        } else {
            Response::error('Pipelines fetching failed', 400);
        }
    }

    public function getAllCRMPipelineStages($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->auth_token) || empty($fieldsRequestParams->brand) || empty($fieldsRequestParams->selectedCRMPipeline)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiKey      = $fieldsRequestParams->auth_token;
        $brand       = $fieldsRequestParams->brand;
        $pipeline    = $fieldsRequestParams->selectedCRMPipeline;
        $apiEndpoint = "https://my.agiled.app/api/v1/crm/pipeline-stages?api_token=$apiKey";
        $header      = [
            'Brand' => $brand
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $header);

        if (!empty($response->data)) {
            foreach ($response->data as $pipelineStage) {
                if ($pipelineStage->pipeline_id == $pipeline) {
                    $pipelineStages[] = [
                        'id'   => (string) $pipelineStage->id,
                        'name' => $pipelineStage->stage_name
                    ];
                }
            }
            Response::success($pipelineStages);
        } else {
            Response::error('Pipeline stages fetching failed', 400);
        }
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId            = $integrationData->id;
        $authToken          = $integrationDetails->auth_token;
        $fieldMap           = $integrationDetails->field_map;
        $actionName         = $integrationDetails->actionName;

        if (empty($fieldMap) || empty($authToken) || empty($actionName)) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Agiled CRM api', 'bit-integrations'));
        }

        $recordApiHelper   = new RecordApiHelper($integrationDetails, $integId);
        $agiledApiResponse = $recordApiHelper->execute($fieldValues, $fieldMap, $actionName);

        if (is_wp_error($agiledApiResponse)) {
            return $agiledApiResponse;
        }
        return $agiledApiResponse;
    }
}
