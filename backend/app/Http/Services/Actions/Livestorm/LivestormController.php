<?php

/**
 * Livestorm Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\Livestorm;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for Livestorm integration
 */
class LivestormController
{
    protected $_defaultHeader;
    protected $_apiEndpoint;

    public function __construct()
    {
        $this->_apiEndpoint = "https://api.livestorm.co/v1";
    }

    private function checkValidation($fieldsRequestParams, $customParam = '**')
    {
        if (empty($fieldsRequestParams->api_key) || empty($customParam)) {
            return Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }
    }

    private function setHeaders($apiKey)
    {
        $this->_defaultHeader = [
            "Authorization" => $apiKey,
            "Accept"        => "application/json",
            "Content-Type"  => "application/json"
        ];
    }

    public function authentication($fieldsRequestParams)
    {
        $this->checkValidation($fieldsRequestParams);
        $this->setHeaders($fieldsRequestParams->api_key);
        $apiEndpoint  = $this->_apiEndpoint . "/ping";
        $response     = Http::request($apiEndpoint, 'Get', null, $this->_defaultHeader);

        if (!count((array) $response)) {
            return Response::success('Authentication successful');
        } elseif (isset($response->errors) && $response->errors[0]->title === "Workspace blocked") {
            return Response::error($response->errors[0]->detail, 400);
        } else {
            return Response::error('Authorized failed, Please enter valid API Key', 400);
        }
    }

    public function getAllEvents($fieldsRequestParams)
    {
        $this->checkValidation($fieldsRequestParams);
        $this->setHeaders($fieldsRequestParams->api_key);
        $apiEndpoint  = $this->_apiEndpoint . "/events";
        $response     = Http::request($apiEndpoint, 'Get', null, $this->_defaultHeader);

        if (isset($response->data)) {
            $data = [
                "events"    => [],
                "allFields" => [],
            ];

            foreach ($response->data as $event) {
                array_push(
                    $data['events'],
                    (object) [
                        'id'    => $event->id,
                        'name'  => $event->attributes->title
                    ]
                );
                foreach ($event->attributes->fields as $field) {
                    array_push(
                        $data['allFields'],
                        (object) [
                            'eventId'   => $event->id,
                            'key'       => $field->id,
                            'label'     => ucwords(str_replace('_', ' ', $field->id)),
                            'required'  => $field->required
                        ]
                    );
                }
            }

            return Response::success($data);
        } else {
            return Response::error('Events fetching failed', 400);
        }
    }

    public function getAllSessions($fieldsRequestParams)
    {
        $this->checkValidation($fieldsRequestParams, $fieldsRequestParams->event_id);
        $this->setHeaders($fieldsRequestParams->api_key);
        $apiEndpoint  = $this->_apiEndpoint . "/events/{$fieldsRequestParams->event_id}/sessions";
        $response     = Http::request($apiEndpoint, 'Get', null, $this->_defaultHeader);

        if (isset($response->data)) {
            $sessions = [];
            foreach ($response->data as $session) {
                array_push(
                    $sessions,
                    (object) [
                        'id'        => $session->id,
                        'datetime'  => date('l, F jS Y h:i:s A (T)', $session->attributes->estimated_started_at)
                    ]
                );
            }
            return Response::success($sessions);
        } else {
            return Response::error('Session fetching failed', 400);
        }
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId            = $integrationData->id;
        $apiKey             = $integrationDetails->api_key;
        $fieldMap           = $integrationDetails->field_map;

        if (empty($fieldMap) || empty($apiKey)) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Livestorm api', 'bit-integrations'));
        }

        $recordApiHelper        = new RecordApiHelper($integrationDetails, $integId, $apiKey);
        $livestormApiResponse   = $recordApiHelper->execute($fieldValues, $fieldMap);

        if (is_wp_error($livestormApiResponse)) {
            return $livestormApiResponse;
        }
        return $livestormApiResponse;
    }
}
