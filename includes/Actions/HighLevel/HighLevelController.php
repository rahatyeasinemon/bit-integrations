<?php

/**
 * HighLevel Integration
 */

namespace BitCode\FI\Actions\HighLevel;

use BitCode\FI\Core\Util\HttpHelper;
use WP_Error;

/**
 * Provide functionality for HighLevel integration
 */
class HighLevelController
{
    private $_integrationID;

    public function __construct($integrationID)
    {
        $this->_integrationID = $integrationID;
    }

    public static function highLevelAuthorization($requestsParams)
    {
        if (empty($requestsParams->api_key)) {
            wp_send_json_error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $header['Authorization'] = 'Bearer ' . $requestsParams->api_key;

        $apiEndpoint = 'https://rest.gohighlevel.com/v1/contacts/?limit=1';

        $response = HttpHelper::get($apiEndpoint, null, $header);

        error_log(print_r(['response' => $response], true));

        if (!isset($response->contacts)) {
            wp_send_json_error(empty($apiResponse) ? 'Unknown' : $apiResponse, 400);
        }

        wp_send_json_success($response);
    }

    public static function getCustomFields($requestsParams)
    {
        if (empty($requestsParams->api_key)) {
            wp_send_json_error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $api_key = $requestsParams->api_key;
        $apiEndpoint = 'https://rest.gohighlevel.com/v1/custom-fields';
        $header = ['Authorization' => 'Bearer ' . $api_key];

        $response = HttpHelper::get($apiEndpoint, null, $header);

        error_log(print_r(['response' => $response], true));

        if (!isset($response->customFields)) {
            wp_send_json_error('Custom fields fetching failed', 400);
        }

        $rawCustomFields = $response->customFields;
        $customFields = [];

        if (!empty($rawCustomFields)) {
            foreach ($rawCustomFields as $item) {
                $customFields[] = (object) [
                    'key'      => $item->id,
                    'label'    => $item->name,
                    'required' => false,
                ];
            }
        }

        error_log(print_r(['custom fields' => $customFields], true));

        wp_send_json_success($customFields, 200);
    }

    public static function getAllTags($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->apiToken) || empty($fieldsRequestParams->selectedAccountId)) {
            wp_send_json_error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiToken = $fieldsRequestParams->apiToken;
        $accountId = $fieldsRequestParams->selectedAccountId;
        $apiEndpoints = 'https://api.gethighLevel.com/v2/' . $accountId . '/tags';
        $header = [
            'Authorization' => 'Basic ' . base64_encode("{$apiToken}:")
        ];

        $response = HttpHelper::get($apiEndpoints, null, $header);

        if (isset($response->tags)) {
            wp_send_json_success($response->tags, 200);
        }

        wp_send_json_error('Tags fetching failed', 400);
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $api_key = $integrationDetails->api_key;
        $fieldMap = $integrationDetails->field_map;
        $accountId = $integrationDetails->selectedAccountId;
        $selectedStatus = $integrationDetails->selectedStatus;
        $selectedTags = $integrationDetails->selectedTags;
        $selectedRemoveTags = $integrationDetails->selectedRemoveTags;

        if (empty($api_key) || empty($fieldMap) || empty($accountId)) {
            return new WP_Error('REQ_FIELD_EMPTY', sprintf(__('module, fields are required for %s api', 'bit-integrations'), 'HighLevel'));
        }

        $recordApiHelper = new RecordApiHelper($api_key, $this->_integrationID);

        $highLevelApiResponse = $recordApiHelper->execute(
            $fieldValues,
            $fieldMap,
            $accountId,
            $selectedStatus,
            $selectedTags,
            $selectedRemoveTags
        );

        if (is_wp_error($highLevelApiResponse)) {
            return $highLevelApiResponse;
        }

        return $highLevelApiResponse;
    }
}
