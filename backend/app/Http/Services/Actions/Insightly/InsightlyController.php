<?php

/**
 * Insightly Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\Insightly;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for Insightly integration
 */
class InsightlyController
{
    protected $_defaultHeader;

    public function authentication($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->api_key) || empty($fieldsRequestParams->api_url)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiUrl       = $fieldsRequestParams->api_url;
        $apiKey      = $fieldsRequestParams->api_key;
        $apiEndpoint = "https://api." . $apiUrl . "/v3.1/Users";
        $headers = [
            "Authorization" => 'Basic ' . base64_encode("$apiKey:"),
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $headers);

        if (is_array($response) && isset($response[0]->USER_ID)) {
            Response::success('Authentication successful');
        } else {
            Response::error('Please enter valid API URL & API key', 400);
        }
    }

    public function getAllOrganisations($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->api_key) || empty($fieldsRequestParams->api_url)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }


        $apiUrl       = $fieldsRequestParams->api_url;
        $apiKey      = $fieldsRequestParams->api_key;
        $apiEndpoint = "https://api." . $apiUrl . "/v3.1/Organisations";
        $headers = [
            "Authorization" => 'Basic ' . base64_encode("$apiKey:"),
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $headers);
        if (!empty($response)) {
            foreach ($response as $organisation) {
                $organisations[] = [
                    'id'   => (string) $organisation->ORGANISATION_ID,
                    'name' => $organisation->ORGANISATION_NAME
                ];
            }
            Response::success($organisations);
        } else {
            Response::error('Owners fetching failed', 400);
        }
    }

    public function getAllCategories($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->api_key) || empty($fieldsRequestParams->api_url)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiUrl       = $fieldsRequestParams->api_url;
        $apiKey      = $fieldsRequestParams->api_key;


        if ($fieldsRequestParams->action_name == "opportunity") {
            $apiEndpoint = "https://api." . $apiUrl . "/v3.1/OpportunityCategories";
        } elseif ($fieldsRequestParams->action_name == "project") {
            $apiEndpoint = "https://api." . $apiUrl . "/v3.1/ProjectCategories";
        } elseif ($fieldsRequestParams->action_name == "task") {
            $apiEndpoint = "https://api." . $apiUrl . "/v3.1/TaskCategories";
        }

        $headers = [
            "Authorization" => 'Basic ' . base64_encode("$apiKey:"),
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $headers);
        if (!empty($response)) {
            foreach ($response as $category) {
                $categories[] = [
                    'id'   => (string) $category->CATEGORY_ID,
                    'name' => $category->CATEGORY_NAME
                ];
            }
            Response::success($categories);
        } else {
            Response::error('Categories fetching failed', 400);
        }
    }

    public function getAllStatuses($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->api_key) || empty($fieldsRequestParams->api_url)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiKey      = $fieldsRequestParams->api_key;
        $apiUrl       = $fieldsRequestParams->api_url;
        $apiEndpoint = "https://my.insightly.app/api/v1/crm-statuses?api_token=$apiKey";
        $header      = [
            'Brand' => $apiUrl
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

    public function getLeadStatuses($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->api_key) || empty($fieldsRequestParams->api_url)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiUrl       = $fieldsRequestParams->api_url;
        $apiKey      = $fieldsRequestParams->api_key;
        $apiEndpoint = "https://api." . $apiUrl . "/v3.1/LeadStatuses";
        $headers = [
            "Authorization" => 'Basic ' . base64_encode("$apiKey:"),
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $headers);
        if (!empty($response)) {
            foreach ($response as $leadStatus) {
                $leadStatuses[] = [
                    'id'   => (string) $leadStatus->LEAD_STATUS_ID,
                    'name' => $leadStatus->LEAD_STATUS
                ];
            }
            Response::success($leadStatuses);
        } else {
            Response::error('Lead Status fetching failed', 400);
        }
    }

    public function getLeadSources($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->api_key) || empty($fieldsRequestParams->api_url)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiUrl       = $fieldsRequestParams->api_url;
        $apiKey      = $fieldsRequestParams->api_key;
        $apiEndpoint = "https://api." . $apiUrl . "/v3.1/LeadSources";
        $headers = [
            "Authorization" => 'Basic ' . base64_encode("$apiKey:"),
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $headers);
        if (!empty($response)) {
            foreach ($response as $leadSource) {
                $leadSources[] = [
                    'id'   => (string) $leadSource->LEAD_SOURCE_ID,
                    'name' => $leadSource->LEAD_SOURCE
                ];
            }
            Response::success($leadSources);
        } else {
            Response::error('Lead Status fetching failed', 400);
        }
    }

    public function getAllCRMPipelines($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->api_key) || empty($fieldsRequestParams->api_url)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiUrl       = $fieldsRequestParams->api_url;
        $apiKey      = $fieldsRequestParams->api_key;
        $apiEndpoint = "https://api." . $apiUrl . "/v3.1/Pipelines";
        $headers = [
            "Authorization" => 'Basic ' . base64_encode("$apiKey:"),
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $headers);

        if (!empty($response)) {
            foreach ($response as $pipeline) {
                if ($fieldsRequestParams->action_name == 'opportunity' && $pipeline->FOR_OPPORTUNITIES == true) {
                    $pipelines[] = [
                        'id'   => (string) $pipeline->PIPELINE_ID,
                        'name' => $pipeline->PIPELINE_NAME
                    ];
                }
                if ($fieldsRequestParams->action_name == 'project' && $pipeline->FOR_PROJECTS == true) {
                    $pipelines[] = [
                        'id'   => (string) $pipeline->PIPELINE_ID,
                        'name' => $pipeline->PIPELINE_NAME
                    ];
                }
            }
            Response::success($pipelines);
        } else {
            Response::error('Pipelines fetching failed', 400);
        }
    }

    public function getAllCRMPipelineStages($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->api_key) || empty($fieldsRequestParams->api_url)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiUrl         = $fieldsRequestParams->api_url;
        $apiKey         = $fieldsRequestParams->api_key;
        $apiEndpoint    = "https://api." . $apiUrl . "/v3.1/PipelineStages";
        $headers        = [
            "Authorization" => 'Basic ' . base64_encode("$apiKey:"),
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $headers);
        if (!empty($response)) {
            foreach ($response as $pipelineStage) {
                $pipelineStages[] = [
                    'id'           => (string) $pipelineStage->STAGE_ID,
                    'pipeline_id'  => (string) $pipelineStage->PIPELINE_ID,
                    'name'         => $pipelineStage->STAGE_NAME
                ];
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
        $authToken          = $integrationDetails->api_key;
        $fieldMap           = $integrationDetails->field_map;
        $actionName         = $integrationDetails->actionName;

        if (empty($fieldMap) || empty($authToken) || empty($actionName)) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Insightly api', 'bit-integrations'));
        }

        $recordApiHelper   = new RecordApiHelper($integrationDetails, $integId);
        $insightlyApiResponse = $recordApiHelper->execute($fieldValues, $fieldMap, $actionName);

        if (is_wp_error($insightlyApiResponse)) {
            return $insightlyApiResponse;
        }
        return $insightlyApiResponse;
    }
}
