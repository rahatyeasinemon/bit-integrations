<?php

/**
 * discord Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\Discord;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

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
    public static function handleAuthorize($tokenRequestParams)
    {
        if (
            empty($tokenRequestParams->accessToken)
        ) {
            Response::error(
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
        $apiEndpoint = self::APIENDPOINT . '/users/@me';

        $apiResponse = Http::request($apiEndpoint, 'Get', null, $header);

        if (!isset($apiResponse->id)) {
            Response::error(
                empty($apiResponse->error) ? 'Unknown' : $apiResponse->error,
                400
            );
        }
        Response::success($apiResponse);
    }


    public static function fetchServers($tokenRequestParams)
    {
        if (
            empty($tokenRequestParams->accessToken)
        ) {
            Response::error(
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

        $apiResponse = Http::request($apiEndpoint, 'Get', null, $header);

        if (count($apiResponse) > 0) {
            foreach ($apiResponse as $server) {
                $servers[] = [
                    'id'   => (string) $server->id,
                    'name' => $server->name
                ];
            }
            Response::success($servers);
        } else {
            Response::error('Servers fetching failed', 400);
        }
    }


    public static function fetchChannels($tokenRequestParams)
    {
        if (
            empty($tokenRequestParams->accessToken) || empty($tokenRequestParams->serverId)
        ) {
            Response::error(
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
        $apiEndpoint = self::APIENDPOINT . '/guilds/' . $tokenRequestParams->serverId . '/channels';

        $apiResponse = Http::request($apiEndpoint, 'Get', null, $header);

        if (count($apiResponse) > 0) {
            foreach ($apiResponse as $channel) {
                $channels[] = [
                    'id'   => (string) $channel->id,
                    'name' => $channel->name
                ];
            }
            Response::success($channels);
        } else {
            Response::error('Channels fetching failed', 400);
        }
    }


    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;

        $integrationId = $integrationData->id;

        $access_token = $integrationDetails->accessToken;
        $parse_mode = $integrationDetails->parse_mode;
        $server_id = $integrationDetails->selectedServer;
        $channel_id = $integrationDetails->selectedChannel;
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
