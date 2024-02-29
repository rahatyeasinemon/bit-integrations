<?php

/**
 * EmailOctopus Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\EmailOctopus;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for EmailOctopus integration
 */
class EmailOctopusController
{
    protected $_defaultHeader;

    public function authentication($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->auth_token)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiKey      = $fieldsRequestParams->auth_token;
        $apiEndpoint = 'https://emailoctopus.com/api/1.6/lists?api_key=' . $apiKey;
        $response    = Http::request($apiEndpoint, 'Get', null, null);

        if (!isset($response->error)) {
            foreach ($response->data as $list) {
                $lists[] = [
                    'id'   => $list->id,
                    'name' => $list->name
                ];
            }
            Response::success($lists);
        } else {
            Response::error('Please a enter valid API key', 400);
        }
    }

    public function getAllFields($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->auth_token) || empty($fieldsRequestParams->listId)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiKey      = $fieldsRequestParams->auth_token;
        $listId      = $fieldsRequestParams->listId;
        $apiEndpoint = 'https://emailoctopus.com/api/1.6/lists/' . $listId . '?api_key=' . $apiKey;
        $response    = Http::request($apiEndpoint, 'Get', null, null);

        if (!isset($response->error)) {
            foreach ($response->fields as $field) {
                $fields[] = [
                    'key'      => $field->tag,
                    'label'    => $field->label,
                    'required' => $field->tag === 'EmailAddress' ? true : false
                ];
            }
            Response::success($fields);
        } else {
            Response::error('Groups fetch failed', 400);
        }
    }

    public function getAllTags($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->auth_token) || empty($fieldsRequestParams->listId)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiKey      = $fieldsRequestParams->auth_token;
        $listId      = $fieldsRequestParams->listId;
        $apiEndpoint = 'https://emailoctopus.com/api/1.6/lists/' . $listId . '/tags?api_key=' . $apiKey;
        $response    = Http::request($apiEndpoint, 'Get', null, null);

        foreach ($response->data as $tag) {
            $tags[] = [
                'name' => $tag->tag
            ];
        }

        if (isset($response->error)) {
            Response::error('Groups fetching failed', 400);
        } else {
            Response::success($tags);
        }
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId            = $integrationData->id;
        $auth_token         = $integrationDetails->auth_token;
        $selectedList       = $integrationDetails->selectedList;
        $selectedTags       = $integrationDetails->selectedTags;
        $fieldMap           = $integrationDetails->field_map;

        if (empty($fieldMap) || empty($auth_token) || empty($selectedList)) {
            return new WP_Error('REQ_FIELD_EMPTY', __('fields are required for EmailOctopus api', 'bit-integrations'));
        }

        $recordApiHelper         = new RecordApiHelper($integrationDetails, $integId);
        $emailOctopusApiResponse = $recordApiHelper->execute($selectedTags, $fieldValues, $fieldMap, $selectedList);

        if (is_wp_error($emailOctopusApiResponse)) {
            return $emailOctopusApiResponse;
        }
        return $emailOctopusApiResponse;
    }
}
