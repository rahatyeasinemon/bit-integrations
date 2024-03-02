<?php

/**
 * ZohoAnalytics Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\ZohoAnalytics;

use WP_Error;
use BitApps\BTCBI\Util\IpTool;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BitApps\BTCBI\Util\ApiResponse as UtilApiResponse;
use BitApps\BTCBI\Http\Services\Log\LogHandler;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for ZohoCrm integration
 */
class ZohoAnalyticsController
{
    private $_integrationID;

    public function __construct($integrationID)
    {
        $this->_integrationID = $integrationID;
        $this->_logResponse = new UtilApiResponse();
    }

    /**
     * Process ajax request for generate_token
     *
     * @return JSON zoho crm api response and status
     */
    public static function generateTokens($requestsParams)
    {
        if (empty($requestsParams->{'accounts-server'})
                || empty($requestsParams->dataCenter)
                || empty($requestsParams->clientId)
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

        $apiEndpoint = \urldecode($requestsParams->{'accounts-server'}) . '/oauth/v2/token';
        $requestParams = [
            'grant_type' => 'authorization_code',
            'client_id' => $requestsParams->clientId,
            'client_secret' => $requestsParams->clientSecret,
            'redirect_uri' => \urldecode($requestsParams->redirectURI),
            'code' => $requestsParams->code
        ];
        $apiResponse = Http::request($apiEndpoint, 'Post', $requestParams);

        if (is_wp_error($apiResponse) || !empty($apiResponse->error)) {
            Response::error(
                empty($apiResponse->error) ? 'Unknown' : $apiResponse->error,
                400
            );
        }
        $apiResponse->generates_on = \time();
        return Response::success($apiResponse);
    }

    /**
     * Process ajax request for refresh crm modules
     *
     * @return JSON crm module data
     */
    public static function refreshWorkspacesAjaxHelper($queryParams)
    {
        if (empty($queryParams->tokenDetails)
                || empty($queryParams->dataCenter)
                || empty($queryParams->clientId)
                || empty($queryParams->clientSecret)
                || empty($queryParams->ownerEmail)
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
        if ((intval($queryParams->tokenDetails->generates_on) + (55 * 60)) < time()) {
            $response['tokenDetails'] = self::_refreshAccessToken($queryParams);
        }

        $workspacesMetaApiEndpoint = "https://analyticsapi.zoho.{$queryParams->dataCenter}/api/{$queryParams->ownerEmail}?ZOHO_ACTION=MYWORKSPACELIST&ZOHO_OUTPUT_FORMAT=JSON&ZOHO_ERROR_FORMAT=JSON&ZOHO_API_VERSION=1.0";

        $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
        $workspacesMetaResponse = Http::request($workspacesMetaApiEndpoint, 'Get', null, $authorizationHeader);

        $allWorkspaces = [];
        if (!is_wp_error($workspacesMetaResponse) && empty($workspacesMetaResponse->response->error)) {
            $workspaces = $workspacesMetaResponse->response->result;
            foreach ($workspaces as $workspace) {
                $allWorkspaces[] = $workspace->workspaceName;
            }
            usort($allWorkspaces, 'strnatcasecmp');
            $response['workspaces'] = $allWorkspaces;
        } else {
            Response::error(
                $workspacesMetaResponse->response->error->message,
                400
            );
        }
        if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
            self::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['workspaces']);
        }
        return Response::success($response);
    }

    public static function refreshUsersAjaxHelper($queryParams)
    {
        if (empty($queryParams->tokenDetails)
                || empty($queryParams->dataCenter)
                || empty($queryParams->clientId)
                || empty($queryParams->clientSecret)
                || empty($queryParams->ownerEmail)
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
        if ((intval($queryParams->tokenDetails->generates_on) + (55 * 60)) < time()) {
            $response['tokenDetails'] = self::_refreshAccessToken($queryParams);
        }

        $usersMetaApiEndpoint = "https://analyticsapi.zoho.{$queryParams->dataCenter}/api/{$queryParams->ownerEmail}?ZOHO_ACTION=GETUSERS&ZOHO_OUTPUT_FORMAT=JSON&ZOHO_ERROR_FORMAT=JSON&ZOHO_API_VERSION=1.0";

        $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
        $usersMetaResponse = Http::request($usersMetaApiEndpoint, 'Get', null, $authorizationHeader);

        $allusers = [];
        if (!is_wp_error($usersMetaResponse) && empty($usersMetaResponse->response->error)) {
            $users = $usersMetaResponse->response->result;
            foreach ($users as $user) {
                $allusers[] = $user->emailId;
            }
            usort($allusers, 'strnatcasecmp');
            $response['users'] = $allusers;
        } else {
            Response::error(
                $usersMetaResponse->response->error->message,
                400
            );
        }
        if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
            self::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['users']);
        }
        return Response::success($response);
    }

    /**
     * Process ajax request for refesh crm layouts
     *
     * @return JSON crm layout data
     */
    public static function refreshTablesAjaxHelper($queryParams)
    {
        if (empty($queryParams->workspace)
                || empty($queryParams->tokenDetails)
                || empty($queryParams->dataCenter)
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
        if ((intval($queryParams->tokenDetails->generates_on) + (55 * 60)) < time()) {
            $response['tokenDetails'] = self::_refreshAccessToken($queryParams);
        }

        $tablesMetaApiEndpoint = "https://analyticsapi.zoho.{$queryParams->dataCenter}/api/{$queryParams->ownerEmail}/{$queryParams->workspace}?ZOHO_ACTION=VIEWLIST&ZOHO_OUTPUT_FORMAT=JSON&ZOHO_ERROR_FORMAT=JSON&ZOHO_API_VERSION=1.0";

        $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
        $tablesMetaResponse = Http::request($tablesMetaApiEndpoint, 'Get', null, $authorizationHeader);

        $allTables = [];
        if (!is_wp_error($tablesMetaResponse)) {
            $tables = $tablesMetaResponse->response->result;
            foreach ($tables as $table) {
                if ($table->viewType === 'Table') {
                    $allTables[] = $table->viewName;
                }
            }
            usort($allTables, 'strnatcasecmp');
            $response['tables'] = $allTables;
        } else {
            Response::error(
                $fieldsMetaResponse->status === 'error' ? $fieldsMetaResponse->message : 'Unknown',
                400
            );
        }
        if (!empty($response['tokenDetails']) && $response['tokenDetails'] && !empty($queryParams->id)) {
            $response['queryWorkspace'] = $queryParams->workspace;
            self::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response);
        }
        return Response::success($response);
    }

    /**
     * Process ajax request for refesh crm layouts
     *
     * @return JSON crm layout data
     */
    public static function refreshTableHeadersAjaxHelper($queryParams)
    {
        if (empty($queryParams->workspace)
                || empty($queryParams->table)
                || empty($queryParams->tokenDetails)
                || empty($queryParams->dataCenter)
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
        if ((intval($queryParams->tokenDetails->generates_on) + (55 * 60)) < time()) {
            $response['tokenDetails'] = self::_refreshAccessToken($queryParams);
        }

        $tableHeadersMetaApiEndpoint = "https://analyticsapi.zoho.{$queryParams->dataCenter}/api/{$queryParams->ownerEmail}/{$queryParams->workspace}/{$queryParams->table}?ZOHO_ACTION=EXPORT&ZOHO_OUTPUT_FORMAT=JSON&ZOHO_ERROR_FORMAT=JSON&ZOHO_API_VERSION=1.0";

        $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
        $tableHeadersMetaResponse = Http::request($tableHeadersMetaApiEndpoint, 'Get', null, $authorizationHeader);

        if (gettype($tableHeadersMetaResponse) === 'string') {
            $tableHeadersMetaResponse = json_decode(preg_replace("/\\\'/", "'", $tableHeadersMetaResponse));
        }

        if (!is_wp_error($tableHeadersMetaResponse)) {
            $allHeaders = array_diff($tableHeadersMetaResponse->response->result->column_order, ['Auto Number']);
            usort($allHeaders, 'strnatcasecmp');
            $response['table_headers'] = $allHeaders;
        } else {
            Response::error(
                $tableHeadersMetaResponse->status === 'error' ? $tableHeadersMetaResponse->message : 'Unknown',
                400
            );
        }
        if (!empty($response['tokenDetails']) && $response['tokenDetails'] && !empty($queryParams->id)) {
            $response['queryModule'] = $queryParams->module;
            self::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response);
        }
        return Response::success($response);
    }

    /**
     * Helps to refresh zoho crm access_token
     *
     * @param  Array $apiData Contains required data for refresh access token
     * @return JSON  $tokenDetails API token details
     */
    protected static function _refreshAccessToken($apiData)
    {
        if (!is_object($apiData) ||
            empty($apiData->dataCenter)
            || empty($apiData->clientId)
            || empty($apiData->clientSecret)
            || empty($apiData->tokenDetails)
        ) {
            return false;
        }
        $tokenDetails = $apiData->tokenDetails;

        $dataCenter = $apiData->dataCenter;
        $apiEndpoint = "https://accounts.zoho.{$dataCenter}/oauth/v2/token";
        $requestParams = [
            'grant_type' => 'refresh_token',
            'client_id' => $apiData->clientId,
            'client_secret' => $apiData->clientSecret,
            'refresh_token' => $tokenDetails->refresh_token,
        ];

        $apiResponse = Http::request($apiEndpoint, 'Post', $requestParams);
        if (is_wp_error($apiResponse) || !empty($apiResponse->error)) {
            return false;
        }
        $tokenDetails->generates_on = \time();
        $tokenDetails->access_token = $apiResponse->access_token;
        return $tokenDetails;
    }

    /**
     * Save updated access_token to avoid unnecessary token generation
     *
     * @param Integer $fromID        ID of Integration related form
     * @param Integer $integrationID ID of Zoho crm Integration
     * @param Obeject $tokenDetails  refreshed token info
     *
     * @return null
     */
    protected static function _saveRefreshedToken($formID, $integrationID, $tokenDetails, $others = null)
    {
        if (empty($formID) || empty($integrationID)) {
            return;
        }

        $integrationHandler = new IntegrationHandler($formID, IpTool::getUserDetail());
        $zanalyticsDetails = $integrationHandler->getAIntegration($integrationID);

        if (is_wp_error($zanalyticsDetails)) {
            return;
        }
        $newDetails = json_decode($zanalyticsDetails[0]->integration_details);

        $newDetails->tokenDetails = $tokenDetails;
        if (!empty($others['workspaces'])) {
            $newDetails->default->workspaces = $others['workspaces'];
        }
        if (!empty($others['tables'])) {
            $newDetails->default->tables = $others['tables'];
        }
        if (!empty($others['table_headers'])) {
            $newDetails->default->tables->headers->{$others['table']} = $others['table_headers'];
        }

        $integrationHandler->updateIntegration($integrationID, $zanalyticsDetails[0]->integration_name, 'Zoho Analytics', \json_encode($newDetails), 'form');
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;

        $tokenDetails = $integrationDetails->tokenDetails;
        $workspace = $integrationDetails->workspace;
        $table = $integrationDetails->table;
        $ownerEmail = $integrationDetails->ownerEmail;
        $dataCenter = $integrationDetails->dataCenter;
        $fieldMap = $integrationDetails->field_map;
        $actions = $integrationDetails->actions;
        $defaultDataConf = $integrationDetails->default;
        if (empty($tokenDetails)
            || empty($workspace)
            || empty($table)
            || empty($fieldMap)
        ) {
            $error = new WP_Error('REQ_FIELD_EMPTY', __('workspace, table, fields are required for zoho analytics api', 'bit-integrations'));
            // $this->_logResponse->apiResponse($logID, $this->_integrationID, 'record', 'validation', $error);
            LogHandler::save($this->_integrationID, wp_json_encode(['type' => 'record', 'type_name' => 'validation']), 'error', wp_json_encode($error));




            return $error;
        }

        if ((intval($tokenDetails->generates_on) + (55 * 60)) < time()) {
            $requiredParams['clientId'] = $integrationDetails->clientId;
            $requiredParams['clientSecret'] = $integrationDetails->clientSecret;
            $requiredParams['dataCenter'] = $integrationDetails->dataCenter;
            $requiredParams['tokenDetails'] = $tokenDetails;
            $newTokenDetails = self::_refreshAccessToken((object) $requiredParams);
            if ($newTokenDetails) {
                self::_saveRefreshedToken($this->_formID, $this->_integrationID, $newTokenDetails);
                $tokenDetails = $newTokenDetails;
            }
        }

        // $actions = $integrationDetails->actions;
        $recordApiHelper = new RecordApiHelper($tokenDetails, $this->_integrationID, $logID);

        $zanalyticsApiResponse = $recordApiHelper->execute(
            $workspace,
            $table,
            $ownerEmail,
            $dataCenter,
            $actions,
            $defaultDataConf,
            $fieldValues,
            $fieldMap
        );

        if (is_wp_error($zanalyticsApiResponse)) {
            return $zanalyticsApiResponse;
        }
        return $zanalyticsApiResponse;
    }
}
