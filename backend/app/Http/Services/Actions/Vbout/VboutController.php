<?php

/**
 * Vbout Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\Vbout;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;

/**
 * Provide functionality for Vbout integration
 */
class VboutController
{
    private $baseUrl = 'https://api.vbout.com/1/';

    public function handleAuthorize($requestParams)
    {
        if (empty($requestParams->auth_token)) {
            wp_send_json_error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $apiEndpoint = $this->baseUrl . 'app/me.json?key=' . $requestParams->auth_token;

        $response = Http::request($apiEndpoint, 'Post', null);
        if ($response->response->header->status !== "ok") {
            wp_send_json_error(
                __(
                    'Invalid token',
                    'bit-integrations'
                ),
                400
            );
        }
        wp_send_json_success($response, 200);
    }

    public function fetchAllLists($requestParams)
    {
        if (empty($requestParams->auth_token)) {
            wp_send_json_error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $apiEndpoint = $this->baseUrl . 'emailmarketing/getlists.json?key=' . $requestParams->auth_token;

        $response = Http::request($apiEndpoint, 'Post', null);

        $formattedResponse = [];
        if ($response->response->header->status !== "ok") {
            wp_send_json_error(
                'The token is invalid',
                400
            );
        } else {
            foreach ($response->response->data->lists->items as $value) {
                $formattedResponse[] =
    [
        'list_id' => $value->id,
        'name' => $value->name,
        'fields' => $value->fields
    ];
            }
        }

        wp_send_json_success($formattedResponse, 200);
    }

    public function vboutRefreshFields($requestParams)
    {
        if (empty($requestParams->auth_token)) {
            wp_send_json_error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $apiEndpoint = $this->baseUrl . 'emailmarketing/getlist.json?key=' . $requestParams->auth_token . '&id=' . $requestParams->list_id;

        $response = Http::request($apiEndpoint, 'Post', null);

        $formattedResponse = [];
        if ($response->response->header->status !== "ok") {
            wp_send_json_error(
                'The token is invalid',
                400
            );
        } else {
            foreach ($response->response->data->list->fields as $key => $value) {
                $formattedResponse[] = [
                    'key' => $key,
                    'label' => $value,
                    'required' => $value === 'Email Address' ? true : false,
                ];
            }
        }

        wp_send_json_success($formattedResponse, 200);
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId = $integrationData->id;
        $auth_token = $integrationDetails->auth_token;
        $listId = $integrationDetails->list_id;
        $fieldMap = $integrationDetails->field_map;
        $contactStatus = $integrationDetails->contact_status;

        if (
            empty($fieldMap)
             || empty($auth_token)
        ) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for MailerLite api', 'bit-integrations'));
        }
        $recordApiHelper = new RecordApiHelper($integrationDetails, $integId);
        $vboutApiResponse = $recordApiHelper->execute(
            $listId,
            $contactStatus,
            $fieldValues,
            $fieldMap,
            $auth_token
        );

        if (is_wp_error($vboutApiResponse)) {
            return $vboutApiResponse;
        }
        return $vboutApiResponse;
    }
}
