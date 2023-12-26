<?php

/**
 * Discord Record Api
 */

namespace BitCode\FI\Actions\Discord;

use BitCode\FI\Core\Util\HttpHelper;
use BitCode\FI\Core\Util\Common;
use BitCode\FI\Log\LogHandler;

/**
 * Provide functionality for Record insert, upsert
 */
class RecordApiHelper
{
    private $_defaultHeader;
    private $_integrationID;
    private $_apiEndPoint;
    private $_accessToken;

    public function __construct($apiEndPoint, $access_token, $integId)
    {
        $this->_defaultHeader['Content-Type'] = 'application/x-www-form-urlencoded';
        $this->_integrationID = $integId;
        $this->_apiEndPoint = $apiEndPoint;
        $this->_accessToken = $access_token;
    }

    public function sendMessages($data, $channel_id)
    {
        $header = [
            'Authorization' => 'Bot ' . $this->_accessToken,
            'Accept' => 'application/json',
            // 'verify' => false
        ];
        // var_dump($data, $header);
        // die;

        $insertRecordEndpoint = $this->_apiEndPoint . '/channels/' . $channel_id . '/messages';
        // return
        $response = HttpHelper::post($insertRecordEndpoint, $data, $header);
        var_dump($response);
        die;
    }

    public function execute($integrationDetails, $fieldValues)
    {
        $msg = Common::replaceFieldWithValue($integrationDetails->body, $fieldValues);
        $messagesBody = str_replace(['<p>', '</p>'], ' ', $msg);

        if (!empty($integrationDetails->actions->attachments)) {
            foreach ($fieldValues as $fieldKey => $fieldValue) {
                if ($integrationDetails->actions->attachments === $fieldKey) {
                    $file = $fieldValue;
                }
            }

            if (
                !empty($file)
                && (
                    (is_array($file))
                )) {
                $data = [
                    'content' => $messagesBody,
                    'parse_mode' => $integrationDetails->parse_mode,
                    'file' => is_array($file) ? $file[0] : $file
                ];

                $sendPhotoApiHelper = new FilesApiHelper($this->_accessToken);
                $recordApiResponse = $sendPhotoApiHelper->uploadFiles($this->_apiEndPoint, $data, $this->_accessToken);
            } else {
                $data = [
                    'content' => $messagesBody,
                    'parse_mode' => $integrationDetails->parse_mode
                ];
                $recordApiResponse = $this->sendMessages($data, $integrationDetails->selectedChannel);
            }

            $type = 'insert';
        } else {
            $data = [
                'content' => $messagesBody,
                'parse_mode' => $integrationDetails->parse_mode
            ];
            $recordApiResponse = $this->sendMessages($data, $integrationDetails->selectedChannel);
            $type = 'insert';
        }

        $recordApiResponse = is_string($recordApiResponse) ? json_decode($recordApiResponse) : $recordApiResponse;

        if ($recordApiResponse && $recordApiResponse->ok) {
            LogHandler::save($this->_integrationID, ['type' => 'record', 'type_name' => $type], 'success', $recordApiResponse);
        } else {
            LogHandler::save($this->_integrationID, ['type' => 'record', 'type_name' => $type], 'error', $recordApiResponse);
        }
        return $recordApiResponse;
    }
}
