<?php

/**
 * MailBluster Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\MailBluster;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for MailBluster integration
 */
class MailBlusterController
{
    private $baseUrl = 'https://api.mailbluster.com/api/';
    protected $_defaultHeader;

    public function authentication($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->auth_token)) {
            return Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }

        $apiEndpoint = $this->baseUrl . 'fields';
        $apiKey       = $fieldsRequestParams->auth_token;
        $header       = [
            'Authorization' => $apiKey,
        ];

        $response     = Http::request($apiEndpoint, 'Get', null, $header);
        $customFields = [];

        foreach ($response->fields as $field) {
            $customFields[] = [
                'key'      => $field->fieldMergeTag,
                'label'    => $field->fieldLabel,
                'required' => false
            ];
        }

        if (property_exists($response, 'fields')) {
            return Response::success($customFields);
        } else {
            return Response::error('Please enter valid API key', 400);
        }
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId            = $integrationData->id;
        $auth_token         = $integrationDetails->auth_token;
        $selectedTags       = $integrationDetails->selectedTags;
        $fieldMap           = $integrationDetails->field_map;
        $subscribed         = $integrationDetails->subscribed;

        if (
            empty($fieldMap)
            || empty($auth_token) || empty($subscribed)
        ) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for GetResponse api', 'bit-integrations'));
        }
        $recordApiHelper = new RecordApiHelper($integrationDetails, $integId);
        $mailBlusterApiResponse = $recordApiHelper->execute(
            $selectedTags,
            $fieldValues,
            $fieldMap,
            $subscribed
        );

        if (is_wp_error($mailBlusterApiResponse)) {
            return $mailBlusterApiResponse;
        }
        return $mailBlusterApiResponse;
    }
}
