<?php

/**
 * Convert Kit Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\ConvertKit;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BitApps\BTCBI\Http\Services\Actions\ConvertKit\RecordApiHelper;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for ZohoCrm integration
 */
class ConvertKitController
{
    private $_integrationID;

    public function __construct($integrationID)
    {
        $this->_integrationID = $integrationID;
    }

    public static function _apiEndpoint($method, $apiSecret)
    {
        return "https://api.convertkit.com/v3/{$method}?api_secret={$apiSecret}";
    }

    /**
     * Process ajax request
     *
     * @param $requestsParams Params to authorize
     *
     * @return JSON Convert Kit api response and status
     */
    public static function convertKitAuthorize($requestsParams)
    {
        if (empty($requestsParams->api_secret)) {
            Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }

        $apiEndpoint = self::_apiEndpoint('account', $requestsParams->api_secret);
        $apiResponse = Http::request($apiEndpoint, 'Get', null);

        if (is_wp_error($apiResponse) || empty($apiResponse) || !empty($apiResponse->error) || empty($apiResponse->primary_email_address)) {
            Response::error(
                !empty($apiResponse->error) ? $apiResponse->message : 'Unknown',
                400
            );
        }

        Response::success(true);
    }


    /**
     * Process ajax request for refresh Forms
     *
     * @param $queryParams Params to fetch form
     *
     * @return JSON convert kit forms data
     */
    public static function convertKitForms($queryParams)
    {
        if (empty($queryParams->api_secret)) {
            Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }

        $apiEndpoint = self::_apiEndpoint('forms', $queryParams->api_secret);
        $convertKitResponse = Http::request($apiEndpoint, 'Get', null);

        $forms = [];
        if (!is_wp_error($convertKitResponse)) {
            $allForms = $convertKitResponse->forms;

            foreach ($allForms as $form) {
                $forms[$form->name] = (object) [
                    'formId' => $form->id,
                    'formName' => $form->name,
                ];
            }
            $response['convertKitForms'] = $forms;
            Response::success($response);
        }
    }

    /**
     * Process ajax request for refresh Tags
     *
     * @param $queryParams Params to fetch form
     *
     * @return JSON convert kit tags data
     */
    public static function convertKitTags($queryParams)
    {
        if (empty($queryParams->api_secret)) {
            Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }

        $apiEndpoint = self::_apiEndpoint('tags', $queryParams->api_secret);

        $convertKitResponse = Http::request($apiEndpoint, 'Get', null);

        $tags = [];
        if (!is_wp_error($convertKitResponse)) {
            $allTags = $convertKitResponse->tags;

            foreach ($allTags as $key => $tag) {
                $tags[$key] = (object) [
                    'tagId' => $tag->id,
                    'tagName' => $tag->name,
                ];
            }
            $response['convertKitTags'] = $tags;

            Response::success($response);
        }
    }

    /**
     * Process ajax request for refresh crm modules
     *
     * @param $queryParams Params to fetch headers
     *
     * @return JSON crm module data
     */
    public static function convertKitHeaders($queryParams)
    {
        if (empty($queryParams->api_secret)) {
            Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }


        $apiEndpoint =  self::_apiEndpoint('custom_fields', $queryParams->api_secret);

        $convertKitResponse = Http::request($apiEndpoint, 'Get', null);

        $fields = [];
        if (!is_wp_error($convertKitResponse)) {
            $allFields = $convertKitResponse->custom_fields;

            foreach ($allFields as $field) {
                $fields[$field->key] = (object) [
                    'fieldId' => $field->key,
                    'fieldName' => $field->key,
                    'required' =>  false
                ];
            }
            $fields['FirstName'] = (object) ['fieldId' => 'firstName', 'fieldName' => 'First Name', 'required' => false];
            $fields['Email'] = (object) ['fieldId' => 'email', 'fieldName' => 'Email', 'required' => true];

            $response['convertKitField'] = $fields;
            Response::success($response);
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

        if (
            empty($api_secret)
            || empty($fieldMap)
        ) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Sendinblue api', 'bit-integrations'));
        }
        $recordApiHelper = new RecordApiHelper($api_secret, $this->_integrationID);

        $convertKitApiResponse = $recordApiHelper->execute(
            $fieldValues,
            $fieldMap,
            $actions,
            $formId,
            $tags
        );

        if (is_wp_error($convertKitApiResponse)) {
            return $convertKitApiResponse;
        }
        return $convertKitApiResponse;
    }
}
