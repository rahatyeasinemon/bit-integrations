<?php

/**
 * Slack Files Api
 */

namespace BitCode\FI\Actions\Freshdesk;

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
     * @param String            $apiEndPoint    FreshDesk API base URL
     * @param Array             $data           Data to pass to API
     *
     * @return Array | Boolean  $uploadResponse FreshDesk API response
     */
    public function uploadFiles($apiEndPoint, $data, $api_key)
    {
        $data['avatar'] = new \CURLFILE("{$data['avatar']}");
        $uploadResponse = HttpHelper::post(
            $apiEndPoint,
            $data,
            [
                'Authorization' => base64_encode("$api_key"),
                'Content-Type'  => 'multipart/form-data',
            ]
        );
        return $uploadResponse;
    }
}
