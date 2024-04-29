<?php

/**
 * Discord Files Api
 */

namespace BitCode\FI\Actions\Discord;

use CURLFile;
use BitCode\FI\Core\Util\HttpHelper;

/**
 * Provide functionality for Upload files
 */
final class FilesApiHelper
{
    private $_defaultHeader;
    private $_payloadBoundary;

    public function __construct()
    {
        $this->_payloadBoundary = wp_generate_password(24);
        $this->_defaultHeader['Content-Type'] = 'multipart/form-data; boundary=' . $this->_payloadBoundary;
    }

    /**
     * Helps to execute upload files api
     *
     * @param String $apiEndPoint discord API base URL
     * @param Array  $data        Data to pass to API
     *
     * @return Array $uploadResponse discord API response
     */
    public function uploadFiles($apiEndPoint, $data, $_accessToken, $channel_id)
    {
        $uploadFileEndpoint = $apiEndPoint . '/channels/' . $channel_id . '/messages';

        if (is_array($data['file'])) {
            $file = $data['file'][0];
        } else {
            $file = $data['file'];
        }

        if (empty($file)) {
            return false;
        }

        $response = HttpHelper::post(
            $uploadFileEndpoint,
            [
                'filename' => new CURLFile($file)
            ],
            [
                'Content-Type'  => 'multipart/form-data',
                'Authorization' => 'Bot ' . $_accessToken
            ]
        );

        return $response;
    }
}
