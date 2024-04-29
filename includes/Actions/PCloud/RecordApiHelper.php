<?php

namespace BitCode\FI\Actions\PCloud;

use BitCode\FI\Core\Util\HttpHelper;
use BitCode\FI\Log\LogHandler;
use CURLFile;

class RecordApiHelper
{
    protected $token;
    protected $errorApiResponse   = [];
    protected $successApiResponse = [];

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function uploadFile($folder, $filePath)
    {
        if ($filePath === '') return false;

        if (is_array($filePath)) {
            foreach ($filePath as $item) {
                $response = HttpHelper::post(
                    'https://api.pcloud.com/uploadfile?folderid=' . $folder,
                    [
                        'filename' => new CURLFile($item)
                    ],
                    [
                        'Content-Type'  => 'multipart/form-data',
                        'Authorization' => 'Bearer ' . $this->token
                    ]
                );
            }
        } else {
            $response = HttpHelper::post(
                'https://api.pcloud.com/uploadfile?folderid=' . $folder,
                [
                    'filename' => new CURLFile($filePath)
                ],
                [
                    'Content-Type'  => 'multipart/form-data',
                    'Authorization' => 'Bearer ' . $this->token
                ]
            );
        }

        return $response;
    }

    public function handleAllFiles($folderWithFiles, $actions)
    {
        foreach ($folderWithFiles as $folderWithFile) {
            if ($folderWithFile == '') continue;
            foreach ($folderWithFile as $folder => $singleFilePath) {
                if ($singleFilePath == '') continue;
                error_log(print_r($singleFilePath, true));
                $response = $this->uploadFile($folder, is_array($singleFilePath) ? $singleFilePath[0] : $singleFilePath);
                $this->storeInState($response);
                $this->deleteFile($singleFilePath[0], $actions);
            }
        }
    }

    protected function storeInState($response)
    {
        if (isset($response->metadata[0]->id)) {
            $this->successApiResponse[] = $response;
        } else {
            $this->errorApiResponse[] =  $response;
        }
    }

    public function deleteFile($filePath, $actions)
    {
        if (isset($actions->delete_from_wp) && $actions->delete_from_wp) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    public function executeRecordApi($integrationId, $fieldValues, $fieldMap, $actions)
    {
        foreach ($fieldMap as $value) {
            if (!is_null($fieldValues[$value->formField])) {
                $folderWithFiles[] = [$value->pCloudFormField => $fieldValues[$value->formField]];
            }
        }

        $this->handleAllFiles($folderWithFiles, $actions);

        if (count($this->successApiResponse) > 0) {
            LogHandler::save($integrationId, wp_json_encode(['type' => 'PCloud', 'type_name' => "file_upload"]), 'success', 'All Files Uploaded. ' . json_encode($this->successApiResponse));
        }
        if (count($this->errorApiResponse) > 0) {
            LogHandler::save($integrationId, wp_json_encode(['type' => 'PCloud', 'type_name' => "file_upload"]), 'error', 'Some Files Can\'t Upload. ' . json_encode($this->errorApiResponse));
        }
        return;
    }
}
