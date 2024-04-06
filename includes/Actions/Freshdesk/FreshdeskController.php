<?php

/**
 * freshdesk Integration
 */

namespace BitCode\FI\Actions\Freshdesk;

use WP_Error;
use BitCode\FI\Core\Util\IpTool;
use BitCode\FI\Core\Util\HttpHelper;

use BitCode\FI\Actions\Freshdesk\RecordApiHelper;

/**
 * Provide functionality for Freshdesk integration
 */
class FreshdeskController
{

    /**
     * Process ajax request for generate_token
     *
     * @param Object $requestsParams Params to authorize
     *
     * @return JSON Freshdesk api response and status
     */

    public function checkAuthorizationAndFetchTickets($tokenRequestParams)
    {

        if (
            empty($tokenRequestParams->app_domain)
            || empty($tokenRequestParams->api_key)
        ) {
            wp_send_json_error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $header = [
            'Authorization' => base64_encode("$tokenRequestParams->api_key"),
            'Content-Type' => 'application/json'
        ];

        $apiEndpoint = $tokenRequestParams->app_domain . '/api/v2/tickets';

        $apiResponse = HttpHelper::get($apiEndpoint, null, $header);

        if (is_wp_error($apiResponse) || empty($apiResponse[0]->id)) {
            wp_send_json_error(
                empty($apiResponse->error) ? 'Unknown' : $apiResponse->error,
                400
            );
        }

        wp_send_json_success($apiResponse, 200);
    }

    /**
     * Process ajax request for Fetch Ticket fields
     *
     * @param Object $requestsParams Params to authorize
     *
     * @return JSON Freshdesk api response and status
     */

    public function getAllTicketFields($tokenRequestParams)
    {

        if (
            empty($tokenRequestParams->app_domain)
            || empty($tokenRequestParams->api_key)
        ) {
            wp_send_json_error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $header = [
            'Authorization' => base64_encode("$tokenRequestParams->api_key"),
            'Content-Type' => 'application/json'
        ];

        $apiEndpoint        = $tokenRequestParams->app_domain . '/api/v2/ticket_fields';
        $apiResponse        = HttpHelper::get($apiEndpoint, null, $header);
        $excludingFields    = ['company', 'requester', 'ticket_type', 'source', 'status', 'priority', 'group', 'agent', 'product'];

        if (is_wp_error($apiResponse) || empty($apiResponse[0]->id)) {
            wp_send_json_error(
                empty($apiResponse->error) ? 'Unknown' : $apiResponse->error,
                400
            );
        }

        $responseData = [
            'ticketFields' => [(object) ['key' => 'email', 'label' => 'Email', 'required' => true]]
        ];

        foreach ($apiResponse as $value) {
            if (!in_array($value->name, $excludingFields)) {
                $responseData['ticketFields'][] = (object) ['key' => $value->name, 'label' => $value->label, 'required' => $value->required_for_agents];
            }

            if ($value->name == 'ticket_type') {
                $responseData['ticketType'] = $value->choices;
            }
            if ($value->name == 'source') {
                $responseData['sources'] = $value->choices;
            }
            if ($value->name == 'group') {
                $responseData['groups'] = $value->choices;
            }
            if ($value->name == 'agent') {
                $responseData['agents'] = $value->choices;
            }
            if ($value->name == 'product') {
                $responseData['products'] = $value->choices;
            }
        }

        wp_send_json_success($responseData, 200);
    }

    /**
     * Process ajax request for refresh telegram get Updates
     *
     * @param Object $requestsParams Params to get update
     *
     * @return JSON Freshdesk get Updates data
     */



    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integrationId = $integrationData->id;
        $api_key = $integrationDetails->api_key;
        $status = (int)$integrationDetails->status;
        $priority = (int)$integrationDetails->priority;
        $fieldMap = $integrationDetails->field_map;
        $fieldMapContact = $integrationDetails->field_map_contact;

        if (
            empty($api_key) ||
            empty($integrationDetails)
            || empty($status)
            || empty($priority)
            || empty($fieldMap)

        ) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Freshdesk api', 'bit-integrations'));
        }
        $app_base_domamin = $integrationDetails->app_domain;
        $apiEndpoint = $integrationDetails->app_domain . '/api/v2/tickets';
        $recordApiHelper = new RecordApiHelper($api_key, $integrationId);
        $freshdeskApiResponse = $recordApiHelper->execute(
            $apiEndpoint,
            $fieldValues,
            $fieldMap,
            $fieldMapContact,
            $integrationDetails,
            $app_base_domamin
        );

        if (is_wp_error($freshdeskApiResponse)) {
            return $freshdeskApiResponse;
        }
        return $freshdeskApiResponse;
    }
}
