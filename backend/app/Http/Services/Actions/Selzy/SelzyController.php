<?php

/**
 * Selzy Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\Selzy;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BitApps\BTCBI\Http\Services\Actions\Selzy\RecordApiHelper;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for Selzy integration
 */

class SelzyController
{
    private $baseUrl = 'https://api.selzy.com/en/api/';

    public function handleAuthorize($requestParams)
    {
        if (empty($requestParams->authKey)) {
            Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $apiEndpoint = $this->baseUrl . 'getLists?format=json&api_key=' . $requestParams->authKey;
        $response = Http::request($apiEndpoint, 'Get', null);
        if ($response->code === "invalid_api_key") {
            Response::error(
                __(
                    'Invalid token',
                    'bit-integrations'
                ),
                400
            );
        }
        Response::success($response);
    }

    public function getAllTags($requestParams)
    {
        if (empty($requestParams->authKey)) {
            Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $apiEndpoint = $this->baseUrl . 'getTags?format=json&api_key=' . $requestParams->authKey;
        $response = Http::request($apiEndpoint, 'Get', null);
        if ($response->code === "invalid_api_key") {
            Response::error(
                __(
                    'Invalid token',
                    'bit-integrations'
                ),
                400
            );
        }
        Response::success($response);
    }

    public function getAllCustomFields($requestParams)
    {
        if (empty($requestParams->authKey)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiEndpoint = "https://api.selzy.com/en/api/getFields?format=json&api_key=$requestParams->authKey";

        $response = Http::request($apiEndpoint, 'Get', null);

        if ($response->code === "invalid_api_key") {
            Response::error(__('Invalid token', 'bit-integrations'), 400);
        }

        if (!empty($response->result)) {
            foreach ($response->result as $customField) {
                $customFields[] = [
                  'key'      => $customField->name,
                  'label'    => $customField->name,
                  'required' => false
                ];
            }
            Response::success($customFields);
        }
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId = $integrationData->id;
        $authKey = $integrationDetails->authKey;
        $listIds = $integrationDetails->listIds;
        $tags = $integrationDetails->tags;
        $method = $integrationDetails->method;
        $option = $integrationDetails->option;
        $overwrite = $integrationDetails->overwrite;
        $field_map = $integrationDetails->field_map;
        $actions = $integrationDetails->actions;

        if (!$actions->option) {
            $option = 0;
        }
        if (!$actions->overwrite) {
            $overwrite = 0;
        }

        if (
            empty($field_map)
            || empty($authKey)
        ) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Selzy api', 'bit-integrations'));
        }
        $recordApiHelper = new RecordApiHelper($integrationDetails, $integId);
        $selzyApiResponse = $recordApiHelper->execute(
            $method,
            $listIds,
            $tags,
            $option,
            $overwrite,
            $fieldValues,
            $field_map,
            $authKey
        );

        if (is_wp_error($selzyApiResponse)) {
            return $selzyApiResponse;
        }
        return $selzyApiResponse;
    }
}
