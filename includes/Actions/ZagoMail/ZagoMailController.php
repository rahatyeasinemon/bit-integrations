<?php

/**
 * Convert Kit Integration
 */

namespace BitCode\FI\Actions\ZagoMail;

use WP_Error;
use BitCode\FI\Core\Util\HttpHelper;
use BitCode\FI\Actions\ZagoMail\RecordApiHelper;

/**
 * Provide functionality for ZohoCrm integration
 */
class ZagoMailController
{
    private $_integrationID;
    private $defaultHeaders;

    public function __construct($integrationID)
    {
        $this->_integrationID = $integrationID;
        // $this->defaultHeaders = [
        //     "Content-Type" => "application/json",
        //     "Accept" => "application/json"
        // ];
    }

    public static function _apiEndpoint($method)
    {
        return "https://api.zagomail.com/{$method}";
    }

    /**
     * Process ajax request
     *
     * @param $requestsParams Params to authorize
     *
     * @return JSON Convert Kit api response and status
     */
    public static function zagoMailAuthorize($requestsParams)
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

        $body = [
            'publicKey' => $requestsParams->api_secret
        ];

        $header["Content-Type"] = "application/json";

        $apiEndpoint = self::_apiEndpoint('lists/all-lists');

        $apiResponse = HttpHelper::post($apiEndpoint, json_encode($body), $header);

        if ($apiResponse->status == 'error' || $apiResponse->status !== 'success') {
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
    public static function zagoMailForms($queryParams)
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

        // $body = [
        //     "publicKey"
        // ]

        $apiEndpoint = self::_apiEndpoint('lists/all-lists');
        $zagoMailResponse = HttpHelper::get($apiEndpoint, null);

        $forms = [];
        if (!is_wp_error($zagoMailResponse)) {
            $allForms = $zagoMailResponse->forms;

            foreach ($allForms as $form) {
                $forms[$form->name] = (object) [
                    'formId' => $form->id,
                    'formName' => $form->name,
                ];
            }
            $response['zagoMailForms'] = $forms;
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
    public static function zagoMailTags($queryParams)
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

        $zagoMailResponse = HttpHelper::get($apiEndpoint, null);

        $tags = [];
        if (!is_wp_error($zagoMailResponse)) {
            $allTags = $zagoMailResponse->tags;

            foreach ($allTags as $key => $tag) {
                $tags[$key] = (object) [
                    'tagId' => $tag->id,
                    'tagName' => $tag->name,
                ];
            }
            $response['zagoMailTags'] = $tags;

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
    public static function zagoMailHeaders($queryParams)
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

        $zagoMailResponse = HttpHelper::get($apiEndpoint, null);

        $fields = [];
        if (!is_wp_error($zagoMailResponse)) {
            $allFields = $zagoMailResponse->custom_fields;

            foreach ($allFields as $field) {
                $fields[$field->key] = (object) [
                    'fieldId' => $field->key,
                    'fieldName' => $field->key,
                    'required' =>  false
                ];
            }
            $fields['FirstName'] = (object) ['fieldId' => 'firstName', 'fieldName' => 'First Name', 'required' => false];
            $fields['Email'] = (object) ['fieldId' => 'email', 'fieldName' => 'Email', 'required' => true];

            $response['zagoMailField'] = $fields;
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

        $zagoMailApiResponse = $recordApiHelper->execute(
            $fieldValues,
            $fieldMap,
            $actions,
            $formId,
            $tags
        );

        if (is_wp_error($zagoMailApiResponse)) {
            return $zagoMailApiResponse;
        }
        return $zagoMailApiResponse;
    }
}
