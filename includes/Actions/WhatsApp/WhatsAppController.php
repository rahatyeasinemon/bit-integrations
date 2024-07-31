<?php

/**
 * WhatsApp Integration
 */

namespace BitCode\FI\Actions\WhatsApp;

use BitCode\FI\Core\Util\HttpHelper;
use WP_Error;

/**
 * Provide functionality for Trello integration
 */
class WhatsAppController
{
    private $baseUrl = 'https://graph.facebook.com/v20.0/';

    public function authorization($requestParams)
    {
        static::checkValidation($requestParams);

        $headers = static::setHeaders($requestParams->token);
        $apiEndpoint = "{$this->baseUrl}{$requestParams->businessAccountID}";
        $response = HttpHelper::get($apiEndpoint, null, $headers);

        if (is_wp_error($response) || !isset($response->id)) {
            wp_send_json_error(isset($response->error->message) ? $response->error->message : 'Authentication failed', 400);
        } else {
            wp_send_json_success('Authentication successful', 200);
        }
    }

    /**
     * Save updated access_token to avoid unnecessary token generation
     *
     * @param object $integrationData Details of flow
     * @param array  $fieldValues     Data to send Mail Chimp
     *
     * @return null
     */
    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId = $integrationData->id;
        $messageTypeId = $integrationDetails->messageTypeId;
        $fieldMap = $integrationDetails->field_map;
        $actions = $integrationDetails->actions;

        if (
            empty($messageTypeId)
            || empty($fieldMap)
        ) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for WhatsApp api', 'bit-integrations'));
        }
        $recordApiHelper = new RecordApiHelper($integrationDetails, $integId);
        $whatsAppApiResponse = $recordApiHelper->execute(
            $fieldValues,
            $fieldMap,
            $messageTypeId,
            $actions
        );

        if (is_wp_error($whatsAppApiResponse)) {
            return $whatsAppApiResponse;
        }

        return $whatsAppApiResponse;
    }

    private static function checkValidation($requestParams)
    {
        if (empty($requestParams->numberID) || empty($requestParams->businessAccountID || empty($requestParams->token))) {
            wp_send_json_error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }
    }

    private static function setHeaders($token)
    {
        return
            [
                'Authorization' => "Bearer {$token}",
                'Content-type'  => 'application/json',
            ];
    }
}
