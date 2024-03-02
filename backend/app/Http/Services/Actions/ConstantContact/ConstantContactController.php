<?php

/**
 * Constant Contact Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\ConstantContact;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BitApps\BTCBI\Http\Controllers\FlowController;
use BitApps\BTCBI\Http\Services\Log\LogHandler;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for Constant Contact integration
 */
class ConstantContactController
{
    protected $_defaultHeader;

    public static function generateTokens($requestsParams)
    {
        if (empty($requestsParams->clientId)
        || empty($requestsParams->clientSecret)
            || empty($requestsParams->redirectURI)
            || empty($requestsParams->code)
        ) {
            Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }

        $auth = $requestsParams->clientId . ':' . $requestsParams->clientSecret;
        // Base64 encode it
        $credentials = base64_encode($auth);

        $authorizationHeader['Accept'] = 'application/json';
        $authorizationHeader['Content-Type'] = 'application/x-www-form-urlencoded';
        $authorizationHeader['Authorization'] = 'Basic ' . $credentials;

        $requestParams = [
            'code'              => $requestsParams->code,
            'redirect_uri'      => "$requestsParams->redirectURI",
            'grant_type'        => 'authorization_code'
        ];

        $apiResponse = Http::request('https://authz.constantcontact.com/oauth2/default/v1/token', 'Post', $requestParams, $authorizationHeader);

        if (is_wp_error($apiResponse) || !empty($apiResponse->error)) {
            Response::error(
                empty($apiResponse->error) ? 'Unknown' : $apiResponse->error,
                400
            );
        }
        $apiResponse->generates_on = \time();
        return Response::success($apiResponse);
    }

    protected static function _refreshAccessToken($apiData)
    {
        if (
            empty($apiData->tokenDetails)
        ) {
            return false;
        }
        $tokenDetails = $apiData->tokenDetails;

        $apiEndpoint = 'https://authz.constantcontact.com/oauth2/default/v1/token';
        $requestParams = [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $tokenDetails->refresh_token,
        ];

        $auth = $apiData->clientId . ':' . $apiData->clientSecret;
        // Base64 encode it
        $credentials = base64_encode($auth);
        $authorizationHeader['Authorization'] = 'Basic ' . $credentials;

        $apiResponse = Http::request($apiEndpoint, 'Post', $requestParams, $authorizationHeader);

        if (is_wp_error($apiResponse) || !empty($apiResponse->error)) {
            return false;
        }
        $tokenDetails->generates_on = \time();
        $tokenDetails->access_token = $apiResponse->access_token;
        return $tokenDetails;
    }

    public static function refreshList($queryParams)
    {
        if (empty($queryParams->tokenDetails)
            || empty($queryParams->clientId)
            || empty($queryParams->clientSecret)
        ) {
            Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $response = [];

        if ((intval($queryParams->tokenDetails->generates_on) + (1435 * 60)) < time()) {
            $refreshedToken = ConstantContactController::_refreshAccessToken($queryParams);

            if ($refreshedToken) {
                $response['tokenDetails'] = $refreshedToken;
            } else {
                Response::error(
                    __('Failed to refresh access token', 'bit-integrations'),
                    400
                );
            }
        }
        $apiEndpoint = 'https://api.cc.email/v3/contact_lists';

        $authorizationHeader['Authorization'] = "Bearer {$queryParams->tokenDetails->access_token}";
        $apiResponse = Http::request($apiEndpoint, 'Get', null, $authorizationHeader);

        $allList = [];
        if (!is_wp_error($apiResponse) && empty($apiResponse->response->error)) {
            $contactLists = $apiResponse->lists;
            foreach ($contactLists as $contactList) {
                $allList[] = [
                    'listId'   => $contactList->list_id,
                    'listName' => $contactList->name
                ];
            }
            uksort($allList, 'strnatcasecmp');

            $response['contactList'] = $allList;
        } else {
            Response::error(
                $apiResponse->response->error->message,
                400
            );
        }
        if (!empty($response['tokenDetails']) && $response['tokenDetails'] && !empty($queryParams->integId)) {
            static::_saveRefreshedToken($queryParams->integId, $response['tokenDetails'], $response);
        }
        return Response::success($response);
    }

    public static function refreshTags($queryParams)
    {
        if (empty($queryParams->tokenDetails)
            || empty($queryParams->clientId)
            || empty($queryParams->clientSecret)
        ) {
            Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $response = [];
        if ((intval($queryParams->tokenDetails->generates_on) + (1435 * 60)) < time()) {
            $refreshedToken = ConstantContactController::_refreshAccessToken($queryParams);

            if ($refreshedToken) {
                $response['tokenDetails'] = $refreshedToken;
            } else {
                Response::error(
                    __('Failed to refresh access token', 'bit-integrations'),
                    400
                );
            }
        }
        $apiEndpoint = 'https://api.cc.email/v3/contact_tags';

        $authorizationHeader['Authorization'] = "Bearer {$queryParams->tokenDetails->access_token}";
        $apiResponse = Http::request($apiEndpoint, 'Get', null, $authorizationHeader);
        $allTag = [];
        if (!is_wp_error($apiResponse) && empty($apiResponse->response->error)) {
            $contactTags = $apiResponse->tags;
            foreach ($contactTags as $contactTag) {
                $allTag[] = [
                    'tagId'   => $contactTag->tag_id,
                    'tagName' => $contactTag->name
                ];
            }
            uksort($allTag, 'strnatcasecmp');

            $response['contactTag'] = $allTag;
        } else {
            Response::error(
                $apiResponse->response->error->message,
                400
            );
        }

        if (!empty($response['tokenDetails']) && $response['tokenDetails'] && !empty($queryParams->integId)) {
            static::_saveRefreshedToken($queryParams->integId, $response['tokenDetails'], $response);
        }
        return Response::success($response);
    }

    public static function getCustomFields($queryParams)
    {
        if (empty($queryParams->tokenDetails)
            || empty($queryParams->clientId)
            || empty($queryParams->clientSecret)
        ) {
            Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        $response = [];
        if ((intval($queryParams->tokenDetails->generates_on) + (1435 * 60)) < time()) {
            $refreshedToken = ConstantContactController::_refreshAccessToken($queryParams);

            if ($refreshedToken) {
                $response['tokenDetails'] = $refreshedToken;
            } else {
                Response::error(
                    __('Failed to refresh access token', 'bit-integrations'),
                    400
                );
            }
        }
        $apiEndpoint = 'https://api.cc.email/v3/contact_custom_fields';

        $authorizationHeader['Authorization'] = "Bearer {$queryParams->tokenDetails->access_token}";
        $apiResponse = Http::request($apiEndpoint, 'Get', null, $authorizationHeader);
        $allCFields = [];
        if (!is_wp_error($apiResponse) && empty($apiResponse->response->error)) {
            $customFields = $apiResponse->custom_fields;
            foreach ($customFields as $cField) {
                $allCFields[] = [
                    'label'             => $cField->label,
                    'key'               => 'custom-' . $cField->custom_field_id,
                    'required'          => false
                ];
            }
            uksort($allCFields, 'strnatcasecmp');

            $response['customFields'] = $allCFields;
        } else {
            Response::error(
                $apiResponse->response->error->message,
                400
            );
        }

        if (!empty($response['tokenDetails']) && $response['tokenDetails'] && !empty($queryParams->integId)) {
            static::_saveRefreshedToken($queryParams->integId, $response['tokenDetails'], $response);
        }
        return Response::success($response);
    }

    private static function _saveRefreshedToken($integrationID, $tokenDetails)
    {
        if (empty($integrationID)) {
            return;
        }

        $flow = new FlowController();
        $cContactDetails = $flow->get(['id' => $integrationID]);

        if (is_wp_error($cContactDetails)) {
            return;
        }
        $newDetails = json_decode($cContactDetails[0]->flow_details);

        $newDetails->tokenDetails = $tokenDetails;
        $flow->update($integrationID, ['flow_details' => \json_encode($newDetails)]);
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId = $integrationData->id;
        $auth_token = $integrationDetails->tokenDetails->access_token;
        $listIds = $integrationDetails->list_ids;
        $tagIds = $integrationDetails->tag_ids;
        $fieldMap = $integrationDetails->field_map;
        $source_type = $integrationDetails->source_type;
        $addressFields = $integrationDetails->address_field;
        $phoneFields = $integrationDetails->phone_field;
        $addressType = $integrationDetails->address_type;
        $phoneType = $integrationDetails->phone_type;

        if (
            empty($fieldMap)
             || empty($auth_token)
        ) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Constant Contact api', 'bit-integrations'));
        }

        if ((intval($integrationDetails->tokenDetails->generates_on) + (1435 * 60)) < time()) {
            $requiredParams['clientId'] = $integrationDetails->clientId;
            $requiredParams['clientSecret'] = $integrationDetails->clientSecret;
            $requiredParams['tokenDetails'] = $integrationDetails->tokenDetails;

            $newTokenDetails = ConstantContactController::_refreshAccessToken((object) $requiredParams);

            if ($newTokenDetails) {
                ConstantContactController::_saveRefreshedToken($integId, $newTokenDetails);
                $integrationDetails->tokenDetails->access_token = $newTokenDetails->access_token;
            } else {
                LogHandler::save($integId, 'token', 'error', 'Failed to refresh access token');
                return;
            }
        }

        $recordApiHelper = new RecordApiHelper($integrationDetails, $integId);

        $constantContactApiResponse = $recordApiHelper->execute(
            $listIds,
            $tagIds,
            $source_type,
            $fieldValues,
            $fieldMap,
            $addressFields,
            $phoneFields,
            $addressType,
            $phoneType
        );

        if (is_wp_error($constantContactApiResponse)) {
            return $constantContactApiResponse;
        }
        return $constantContactApiResponse;
    }
}
