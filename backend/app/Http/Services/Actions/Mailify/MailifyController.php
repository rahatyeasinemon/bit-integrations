<?php

namespace BitApps\BTCBI\Http\Services\Actions\Mailify;

use WP_Error;
use BitApps\BTCBI\Http\Controllers\FlowController;
use BitApps\BTCBI\Http\Services\Actions\Mailify\RecordApiHelper;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

class MailifyController
{
    private $integrationID;

    public function __construct($integrationID)
    {
        $this->integrationID = $integrationID;
    }

    public static function authorization($requestParams)
    {
        if (empty($requestParams->account_id) || empty($requestParams->api_key)) {
            return Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiEndpoint = "https://mailifyapis.com/v1/users";
        $header["Authorization"] = 'Basic ' . base64_encode("$requestParams->account_id:$requestParams->api_key");

        $response = Http::request($apiEndpoint, 'Get', null, $header);

        if (!isset($response->users)) {
            Response::error(
                empty($response->message) ? 'Unknown' : $response->message,
                400
            );
        }
        Response::success(true);
    }

    public static function mailifyHeaders($requestParams)
    {
        if (
            empty($requestParams->account_id) || empty($requestParams->api_key)
        ) {
            Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }

        $listId = $requestParams->list_id;
        $apiEndpoint = "https://mailifyapis.com/v1/lists/{$listId}/fields";
        $headers = [
            'accountId' => $requestParams->account_id,
            'apiKey' => $requestParams->api_key,
        ];

        $mailifyResponse = Http::request($apiEndpoint, 'Get', null, $headers);

        $fields = [];
        if (!is_wp_error($mailifyResponse->fields)) {
            $allFields = $mailifyResponse->fields;
            $unwantedFieldKeys = ['id', 'CREATION_DATE_ID', 'MODIFICATION_DATE_ID'];
            foreach ($allFields as $field) {
                if (!in_array($field->id, $unwantedFieldKeys)) {
                    $fields[$field->caption] = (object) [
                        'fieldName' => $field->caption,
                        'fieldValue' => $field->id,
                        'required' =>  strtolower($field->caption) == 'email' ? true : false
                    ];
                }
            }

            $response['mailifyField'] = $fields;
            return Response::success($response);
        }
    }

    public static function getAllList($requestParams)
    {
        if (empty($requestParams->account_id) || empty($requestParams->api_key)) {
            return Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $headers = [
            'accountId' => $requestParams->account_id,
            'apiKey' => $requestParams->api_key,
        ];
        $apiEndpoint = 'https://mailifyapis.com/v1/lists';
        $apiResponse = Http::request($apiEndpoint, 'Get', null, $headers);
        $lists       = [];

        foreach ($apiResponse as $item) {
            $lists[] = [
                'listId' => $item->id,
                'listName'   => $item->name
            ];
        }

        if ((count($lists)) > 0) {
            return Response::success($lists);
        } else {
            return Response::error('List fetching failed', 400);
        }
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId            = $integrationData->id;
        $selectedList       = $integrationDetails->listId;
        $actions            = $integrationDetails->actions;
        $fieldMap           = $integrationDetails->field_map;
        $accountId          = $integrationDetails->account_id;
        $apiKey             = $integrationDetails->api_key;

        if (empty($fieldMap) || empty($accountId) || empty($apiKey) || empty($selectedList)) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Mailify api', 'bit-integrations'));
        }

        $recordApiHelper    = new RecordApiHelper($integrationDetails, $integId, $accountId, $apiKey);
        $mailifyApiResponse = $recordApiHelper->execute(
            $selectedList,
            $fieldValues,
            $fieldMap,
            $actions
        );

        if (is_wp_error($mailifyApiResponse) || (isset($mailifyApiResponse['success']) && !$mailifyApiResponse['success'])) {
            return $mailifyApiResponse;
        }

        return $mailifyApiResponse;
    }
}
