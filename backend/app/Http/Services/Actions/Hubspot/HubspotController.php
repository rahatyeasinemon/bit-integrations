<?php

namespace BitApps\BTCBI\Http\Services\Actions\Hubspot;

use WP_Error;
use BitApps\BTCBI\Http\Services\Log\LogHandler;
use BitApps\BTCBI\Http\Controllers\FlowController;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

final class HubspotController
{
    private $_integrationID;

    public function __construct($integrationID)
    {
        $this->_integrationID = $integrationID;
    }

    public static function authorization($requestParams)
    {
        if (empty($requestParams->api_key)) {
            return Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiEndpoint = 'https://api.hubapi.com/crm/v3/objects/contacts';
        $header      = [
            'authorization' => 'Bearer ' . $requestParams->api_key
        ];

        $apiResponse = Http::request($apiEndpoint, 'Get', null, $header);

        if (isset($apiResponse->results)) {
            return Response::success('Authorization successfull');
        } else {
            return Response::error('Authorization failed', 400);
        }
    }

    public static function getFields($requestParams)
    {
        if (empty($requestParams->api_key) || empty($requestParams->type)) {
            return Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiEndpoint = "https://api.hubapi.com/crm/v3/properties/$requestParams->type";
        $header      = [
            'authorization' => 'Bearer ' . $requestParams->api_key
        ];

        $apiResponse = Http::request($apiEndpoint, 'Get', null, $header);

        if (isset($apiResponse->results) && !empty($apiResponse->results)) {
            foreach ($apiResponse->results as $field) {
                if ($requestParams->type == 'contact' && $field->formField === true) {
                    $fields[] = [
                        'key'      => $field->name,
                        'label'    => $field->label,
                        'required' => $field->name == 'email' ? true : false
                    ];
                } elseif ($requestParams->type == 'deal' && $field->formField === true) {
                    $fields[] = [
                        'key'      => $field->name,
                        'label'    => $field->label,
                        'required' => $field->name == 'dealname' ? true : false
                    ];
                } elseif ($requestParams->type == 'ticket' && $field->formField === true) {
                    $fields[] = [
                        'key'      => $field->name,
                        'label'    => $field->label,
                        'required' => $field->name == 'subject' ? true : false
                    ];
                }
            }

            return Response::success($fields);
        } else {
            return Response::error('fields fetching failed', 400);
        }
    }

    public static function getAllOwners($requestParams)
    {
        if (empty($requestParams->api_key)) {
            return Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiEndpoint = 'https://api.hubapi.com/crm/v3/owners';
        $header      = [
            'authorization' => 'Bearer ' . $requestParams->api_key
        ];

        $apiResponse = Http::request($apiEndpoint, 'Get', null, $header);

        if (isset($apiResponse->results) && !empty($apiResponse->results)) {
            foreach ($apiResponse->results as $owner) {
                $owners[] = [
                    'ownerId'   => $owner->id,
                    'ownerName' => "$owner->firstName $owner->lastName"
                ];
            }
            return Response::success($owners);
        } else {
            return Response::error('fields fetching failed', 400);
        }
    }

    public static function getAllPipelines($requestParams)
    {
        if (empty($requestParams->api_key) || empty($requestParams->type)) {
            return Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiEndpoint = "https://api.hubapi.com/crm/v3/pipelines/$requestParams->type";
        $header      = [
            'authorization' => 'Bearer ' . $requestParams->api_key
        ];

        $apiResponse = Http::request($apiEndpoint, 'Get', null, $header);

        if (isset($apiResponse->results) && !empty($apiResponse->results)) {
            $pipelines = $apiResponse->results;
            $response  = [];

            foreach ($pipelines as $pipeline) {
                $tempStage = [];
                foreach ($pipeline->stages as $stage) {
                    $tempStage[] = (object) array(
                        'stageId'   => $stage->id,
                        'stageName' => $stage->label
                    );
                }
                $response[] = (object) array(
                    'pipelineId'   => $pipeline->id,
                    'pipelineName' => $pipeline->label,
                    'stages'       => $tempStage
                );
            }
            return Response::success($response);
        } else {
            return Response::error('pipelines fetching failed', 400);
        }
    }

    public static function getAllContacts($requestParams)
    {
        if (empty($requestParams->api_key)) {
            return Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiEndpoint = 'https://api.hubapi.com/crm/v3/objects/contacts?limit=100';
        $header      = [
            'authorization' => 'Bearer ' . $requestParams->api_key
        ];

        $apiResponse = Http::request($apiEndpoint, 'Get', null, $header);

        if (isset($apiResponse->results) && !empty($apiResponse->results)) {
            foreach ($apiResponse->results as $contact) {
                $contactName = !empty($contact->properties->firstname || $contact->properties->lastname) ? $contact->properties->firstname . ' ' . $contact->properties->lastname : 'N/A';
                $contacts[] = [
                    'contactId'   => $contact->id,
                    'contactName' => $contactName
                ];
            }
            return Response::success($contacts);
        } else {
            return Response::error('contacts fetching failed', 400);
        }
    }

    public static function getAllCompany($requestParams)
    {
        if (empty($requestParams->api_key)) {
            return Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiEndpoint = 'https://api.hubapi.com/crm/v3/objects/companies?limit=100';
        $header      = [
            'authorization' => 'Bearer ' . $requestParams->api_key
        ];

        $apiResponse = Http::request($apiEndpoint, 'Get', null, $header);

        if (isset($apiResponse->results) && !empty($apiResponse->results)) {
            foreach ($apiResponse->results as $company) {
                $companies[] = [
                    'companyId'   => $company->id,
                    'companyName' => $company->properties->name
                ];
            }
            return Response::success($companies);
        } else {
            return Response::error('fields fetching failed', 400);
        }
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $fieldMap           = $integrationDetails->field_map;
        $integId            = $integrationData->id;
        $apiKey             = $integrationDetails->api_key;

        if (empty($fieldMap) || empty($apiKey)) {
            $error = new WP_Error('REQ_FIELD_EMPTY', __('Access token, fields map are required for hubspot api', 'bit-integrations'));
            LogHandler::save($this->_integrationID, 'record', 'validation', $error);
            return $error;
        }

        $recordApiHelper = new HubspotRecordApiHelper($apiKey);
        $hubspotResponse = $recordApiHelper->executeRecordApi($integId, $integrationDetails, $fieldValues, $fieldMap);

        if (is_wp_error($hubspotResponse)) {
            return $hubspotResponse;
        }
        return $hubspotResponse;
    }
}
