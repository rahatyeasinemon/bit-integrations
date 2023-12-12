<?php

/**
 * Convert Kit Integration
 */

namespace BitCode\FI\Actions\SystemIO;

use WP_Error;
use BitCode\FI\Core\Util\HttpHelper;
use BitCode\FI\Actions\SystemIO\RecordApiHelper;

/**
 * Provide functionality for ZohoCrm integration
 */
class SystemIOController
{
    private $_integrationID;

    public function __construct($integrationID)
    {
        $this->_integrationID = $integrationID;
    }

    public static function _apiEndpoint($method)
    {
        return "https://api.systeme.io/api/{$method}";
    }

    /**
     * Process ajax request
     *
     * @param $requestsParams Params to authorize
     *
     * @return JSON Convert Kit api response and status
     */
    public static function systemIOAuthorize($requestsParams)
    {
        if (empty($requestsParams->api_secret)
        ) {
            wp_send_json_error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $headers = [
            "x-api-key" => $requestsParams->api_secret
        ];

        $apiEndpoint = self::_apiEndpoint('contacts');

        $apiResponse = HttpHelper::get($apiEndpoint, null, $headers);

        if (is_wp_error($apiResponse) || empty($apiResponse->items)) {
            wp_send_json_error(
                empty($apiResponse) ? 'Unknown' : $apiResponse,
                400
            );
        }

        wp_send_json_success(true);
    }


    /**
     * Process ajax request for refresh Forms
     *
     * @param $queryParams Params to fetch form
     *
     * @return JSON convert kit forms data
     */
    public static function systemIOForms($queryParams)
    {
        if (empty($queryParams->api_secret)
        ) {
            wp_send_json_error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }

        $apiEndpoint = self::_apiEndpoint('forms', $queryParams->api_secret);
        $systemIOResponse = HttpHelper::get($apiEndpoint, null);

        $forms = [];
        if (!is_wp_error($systemIOResponse)) {
            $allForms = $systemIOResponse->forms;

            foreach ($allForms as $form) {
                $forms[$form->name] = (object) [
                    'formId' => $form->id,
                    'formName' => $form->name,
                ];
            }
            $response['systemIOForms'] = $forms;
            wp_send_json_success($response);
        }
    }

    /**
     * Process ajax request for refresh Tags
     *
     * @param $queryParams Params to fetch form
     *
     * @return JSON convert kit tags data
     */
    public static function systemIOTags($queryParams)
    {
        if (empty($queryParams->api_secret)
        ) {
            wp_send_json_error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }

        $apiEndpoint = self::_apiEndpoint('tags', $queryParams->api_secret);

        $systemIOResponse = HttpHelper::get($apiEndpoint, null);

        $tags = [];
        if (!is_wp_error($systemIOResponse)) {
            $allTags = $systemIOResponse->tags;

            foreach ($allTags as $key => $tag) {
                $tags[$key] = (object) [
                    'tagId' => $tag->id,
                    'tagName' => $tag->name,
                ];
            }
            $response['systemIOTags'] = $tags;

            wp_send_json_success($response);
        }
    }

    /**
     * Process ajax request for refresh crm modules
     *
     * @param $queryParams Params to fetch headers
     *
     * @return JSON crm module data
     */
    public static function systemIOHeaders($queryParams)
    {
        if (empty($queryParams->api_secret)
        ) {
            wp_send_json_error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }


        $apiEndpoint =  self::_apiEndpoint('custom_fields', $queryParams->api_secret);

        $systemIOResponse = HttpHelper::get($apiEndpoint, null);

        $fields = [];
        if (!is_wp_error($systemIOResponse)) {
            $allFields = $systemIOResponse->custom_fields;

            foreach ($allFields as $field) {
                $fields[$field->key] = (object) [
                    'fieldId' => $field->key,
                    'fieldName' => $field->key,
                    'required' =>  false
                ];
            }
            $fields['FirstName'] = (object) ['fieldId' => 'firstName', 'fieldName' => 'First Name', 'required' => false];
            $fields['Email'] = (object) ['fieldId' => 'email', 'fieldName' => 'Email', 'required' => true];

            $response['systemIOField'] = $fields;
            wp_send_json_success($response);
        }
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;

        $api_secret = $integrationDetails->api_secret;
        $fieldMap = $integrationDetails->field_map;
        $actions = $integrationDetails->actions;
        $formId = $integrationDetails->formId;
        $tags = $integrationDetails->tagIds;

        if (empty($api_secret)
            || empty($fieldMap)
        ) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Sendinblue api', 'bit-integrations'));
        }
        $recordApiHelper = new RecordApiHelper($api_secret, $this->_integrationID);

        $systemIOApiResponse = $recordApiHelper->execute(
            $fieldValues,
            $fieldMap,
            $actions,
            $formId,
            $tags
        );

        if (is_wp_error($systemIOApiResponse)) {
            return $systemIOApiResponse;
        }
        return $systemIOApiResponse;
    }
}
