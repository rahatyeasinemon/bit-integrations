<?php

/**
 * Mailjet Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\Mailjet;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for Mailjet integration
 */
class MailjetController
{
    public function authentication($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->secretKey) && empty($fieldsRequestParams->apiKey)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiEndpoint = 'https://api.mailjet.com/v3/REST/contactslist/';
        $apiKey       = $fieldsRequestParams->apiKey;
        $secretKey    = $fieldsRequestParams->secretKey;
        $header       = [
            'Authorization' => 'Basic ' . base64_encode("$apiKey:$secretKey")
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $header);

        if (!empty($response)) {
            foreach ($response->Data as $list) {
                $lists[] = [
                    'id'   => (string) $list->ID,
                    'name' => $list->Name
                ];
            }
            Response::success($lists);
        } else {
            Response::error('Please enter valid API key', 400);
        }
    }

    public function getCustomFields($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->secretKey) && empty($fieldsRequestParams->apiKey)) {
            Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiEndpoint = 'https://api.mailjet.com/v3/REST/contactmetadata?Limit=1000';
        $apiKey       = $fieldsRequestParams->apiKey;
        $secretKey    = $fieldsRequestParams->secretKey;
        $header       = [
            'Authorization' => 'Basic ' . base64_encode("$apiKey:$secretKey")
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $header);

        foreach ($response->Data as $customField) {
            $customFields[] = [
                'key'      => $customField->Name,
                'label'    => ucfirst(str_replace("_", " ", $customField->Name)),
                'required' => false
            ];
        }

        if (!empty($customFields)) {
            Response::success($customFields);
        } else {
            Response::error('Custom fields fetch failed', 400);
        }
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId            = $integrationData->id;
        $apiKey             = $integrationDetails->apiKey;
        $secretKey          = $integrationDetails->secretKey;
        $selectedLists      = $integrationDetails->selectedLists;
        $fieldMap           = $integrationDetails->field_map;

        if (empty($fieldMap) || empty($secretKey) || empty($apiKey) || empty($selectedLists)) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Mailjet api', 'bit-integrations'));
        }

        $recordApiHelper    = new RecordApiHelper($integrationDetails, $integId, $apiKey, $secretKey);
        $mailjetApiResponse = $recordApiHelper->execute(
            $selectedLists,
            $fieldValues,
            $fieldMap
        );

        if (is_wp_error($mailjetApiResponse)) {
            return $mailjetApiResponse;
        }
        return $mailjetApiResponse;
    }
}
