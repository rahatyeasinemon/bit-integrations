<?php

/**
 * discord Integration
 */

namespace BitCode\FI\Actions\Discord;

use WP_Error;
use BitCode\FI\Core\Util\HttpHelper;

/**
 * Provide functionality for discord integration
 */
class DiscordController
{
    public const APIENDPOINT = 'https://discord.com/api/v10';

    /**
     * Process ajax request for generate_token
     *
     * @param Object $requestsParams Params to authorize
     *
     * @return JSON discord api response and status
     */
    public static function checkAuthorizationAndFetchServers($tokenRequestParams)
    {
        if (
            empty($tokenRequestParams->accessToken)
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
            'Authorization' => 'Bot ' . $tokenRequestParams->accessToken,
        ];
        $apiEndpoint = self::APIENDPOINT . '/users/@me/guilds';

        $apiResponse = HttpHelper::get($apiEndpoint, null, $header);

        if (is_wp_error($apiResponse) || !empty($apiResponse->error)) {
            wp_send_json_error(
                empty($apiResponse->error) ? 'Unknown' : $apiResponse->error,
                400
            );
        }
        // $apiResponse->generates_on = \time();
        // var_dump($apiResponse);
        // die;
        wp_send_json_success($apiResponse, 200);
    }
    public static function checkAuthorizationAndFetchChannels($tokenRequestParams)
    {
        if (
            empty($tokenRequestParams->accessToken)
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
            'Authorization' => 'Bearer ' . $tokenRequestParams->accessToken,
            'Accept' => '*/*',
            'verify' => false
        ];
        $apiEndpoint = self::APIENDPOINT . '/conversations.list';

        $apiResponse = HttpHelper::post($apiEndpoint, null, $header);

        if (is_wp_error($apiResponse) || !empty($apiResponse->error)) {
            wp_send_json_error(
                empty($apiResponse->error) ? 'Unknown' : $apiResponse->error,
                400
            );
        }
        $apiResponse->generates_on = \time();
        wp_send_json_success($apiResponse, 200);
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integrationId = $integrationData->id;

        $access_token = $integrationDetails->accessToken;
        $parse_mode = $integrationDetails->parse_mode;
        $server_id = $integrationDetails->server_id;
        $channel_id = $integrationDetails->channel_id;
        $body = $integrationDetails->body;

        if (
            empty($access_token)
            || empty($parse_mode)
            || empty($server_id)
            || empty($channel_id)
            || empty($body)
        ) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Discord api', 'bit-integrations'));
        }
        $recordApiHelper = new RecordApiHelper(self::APIENDPOINT, $access_token, $integrationId);
        $discordApiResponse = $recordApiHelper->execute(
            $integrationDetails,
            $fieldValues
        );

        if (is_wp_error($discordApiResponse)) {
            return $discordApiResponse;
        }
        return $discordApiResponse;
    }
}
