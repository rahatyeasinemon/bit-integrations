<?php

/**
 * WishList Integration
 *
 */

namespace BitApps\BTCBI\Http\Services\Actions\WishList;

use WP_Error;
use BitApps\BTCBI\Http\Services\Actions\WishList\RecordApiHelper;
use BitApps\BTCBI\Http\Services\Log\LogHandler;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

include('wlmapiclass.php');

final class WishListController
{
    public static function checkAuthorization($tokenRequestParams)
    {
        if (
            empty($tokenRequestParams->baseUrl)
            || empty($tokenRequestParams->apiKey)
        ) {
            return Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $base_url = $tokenRequestParams->baseUrl;
        $api_key = $tokenRequestParams->apiKey;
        $api = new wlmapiclass($base_url, $api_key);
        $api->return_format = 'json';
        $apiResponse = $api->get('/levels');
        $apiResponse = json_decode($apiResponse);
        if (!(property_exists($apiResponse, 'success'))) {
            return Response::error(
                'Unauthorize',
                400
            );
        } else {
            $apiResponse->generates_on = \time();
            return Response::success(true);
        }
    }

    /**
     * Process request for getting levels from wishlist member
     *
     * @param $queryParams Mandatory params to get levels
     *
     * @return JSON wishlist levels data
     */

    public static function getAllLevels($queryParams)
    {
        if (
            empty($queryParams->baseUrl)
            || empty($queryParams->apiKey)
        ) {
            return Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }

        $base_url = $queryParams->baseUrl;
        $api_key = $queryParams->apiKey;
        $api = new wlmapiclass($base_url, $api_key);
        $api->return_format = 'json';
        $apiResponse = $api->get('/levels');
        $apiResponse = json_decode($apiResponse)->levels->level;

        foreach ($apiResponse as $level) {
            $data[] = (object) [
                'id' => $level->id,
                'name' => $level->name
            ];
        }
        $response['levellists'] = $data;
        return Response::success($response);
    }

    public static function getAllMembers($queryParams)
    {
        if (
            empty($queryParams->baseUrl)
            || empty($queryParams->apiKey)
        ) {
            return Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }

        $base_url = $queryParams->baseUrl;
        $api_key = $queryParams->apiKey;
        $api = new wlmapiclass($base_url, $api_key);
        $api->return_format = 'json';
        $apiResponse = $api->get('/members');
        $apiResponse = json_decode($apiResponse)->members->member;

        foreach ($apiResponse as $member) {
            $data[] = (object) [
                'id' => $member->id,
                'name' => $member->user_login,
                'email' => $member->user_email,
            ];
        }
        $response['memberlists'] = $data;
        return Response::success($response);
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $fieldMap = $integrationDetails->field_map;
        $defaultDataConf = $integrationDetails->default;
        $baseUrl = $integrationDetails->baseUrl;
        $apiKey = $integrationDetails->apiKey;
        $levelLists = $defaultDataConf->levellists;
        $integrationId = $integrationData->id;

        if (
            empty($baseUrl)
            || empty($apiKey)
            || empty($fieldMap)
        ) {
            $error = new WP_Error('REQ_FIELD_EMPTY', __('baseUrl, apiKey, fields are required for Wish List API', 'bit-integrations'));
            LogHandler::save($integrationId, 'record', 'validation', $error);
            return $error;
        }
        if (empty($levelLists)) {
            $error = new WP_Error('REQ_FIELD_EMPTY', __('level List are required for Wish List API', 'bit-integrations'));
            LogHandler::save($integrationId, 'record', 'validation', $error);
            return $error;
        }
        $actions = $integrationDetails->actions;
        $recordApiHelper = new RecordApiHelper($integrationDetails, $baseUrl, $apiKey);
        $wishlistResponse = $recordApiHelper->executeRecordApi(
            $integrationId,
            $defaultDataConf,
            $levelLists,
            $fieldValues,
            $fieldMap,
            $actions
        );
        if (is_wp_error($wishlistResponse)) {
            return $wishlistResponse;
        }
        return $wishlistResponse;
    }
}
