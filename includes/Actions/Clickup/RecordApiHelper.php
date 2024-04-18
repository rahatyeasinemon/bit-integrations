<?php

/**
 * Clickup Record Api
 */

namespace BitCode\FI\Actions\Clickup;

use BitCode\FI\Log\LogHandler;
use BitCode\FI\Core\Util\HttpHelper;

/**
 * Provide functionality for Record insert, upsert
 */
class RecordApiHelper
{
    private $integrationDetails;
    private $integrationId;
    private $apiUrl;
    private $defaultHeader;
    private $type;
    private $typeName;

    public function __construct($integrationDetails, $integId)
    {
        $this->integrationDetails = $integrationDetails;
        $this->integrationId      = $integId;
        $this->apiUrl             = "https://api.clickup.com/api/v2/";
        $this->defaultHeader      = [
            "Authorization" => $integrationDetails->api_key,
            'content-type' => 'application/json'
        ];
    }

    public function addTask($finalData, $fieldValues)
    {
        if (!isset($finalData['name'])) {
            return ['success' => false, 'message' => 'Required field task name is empty', 'code' => 400];
        }
        $staticFieldsKeys = ['name', 'description', "start_date", 'due_date'];

        foreach ($finalData as $key => $value) {
            if (in_array($key, $staticFieldsKeys)) {
                if ($key === 'start_date' || $key === 'due_date') {
                    $requestParams[$key] = strtotime($value) * 1000;
                } else {
                    $requestParams[$key] = $value;
                }
            } else {
                $requestParams['custom_fields'][] = (object) [
                    'id' => $key,
                    'value' => $value,
                ];
            }
        }

        $this->type     = 'Task';
        $this->typeName = 'Task created';
        $listId         = $this->integrationDetails->selectedList;
        $apiEndpoint    = $this->apiUrl . "list/{$listId}/task";
        $response       = HttpHelper::post($apiEndpoint, json_encode($requestParams), $this->defaultHeader);

        return empty($this->integrationDetails->attachment) ? $response : $this->uploadFile($fieldValues[$this->integrationDetails->attachment], $response->id);
    }

    public function generateReqDataFromFieldMap($data, $fieldMap)
    {
        $dataFinal = [];
        foreach ($fieldMap as $value) {
            $triggerValue = $value->formField;
            $actionValue  = $value->clickupFormField;
            if ($triggerValue === 'custom') {
                if ($actionValue === 'fields') {
                    $dataFinal[$value->customFieldKey] = self::formatPhoneNumber($value->customValue);
                } else {
                    $dataFinal[$actionValue] = self::formatPhoneNumber($value->customValue);
                }
            } elseif (!is_null($data[$triggerValue])) {
                if ($actionValue === 'fields') {
                    $dataFinal[$value->customFieldKey] = self::formatPhoneNumber($data[$triggerValue]);
                } else {
                    $dataFinal[$actionValue] = self::formatPhoneNumber($data[$triggerValue]);
                }
            }
        }
        return $dataFinal;
    }

    private function uploadFile($files, $taskId)
    {
        $result = null;
        foreach ($files as $file) {
            if (is_array($file)) {
                $result =  static::uploadFile($file, $taskId);
            } else {
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $this->apiUrl . "task/{$taskId}/attachment");
                curl_setopt($curl, CURLOPT_USERAGENT, 'Opera/9.80 (Windows NT 6.2; Win64; x64) Presto/2.12.388 Version/12.15');
                curl_setopt($curl, CURLOPT_HTTPHEADER, array('User-Agent: Opera/9.80 (Windows NT 6.2; Win64; x64) Presto/2.12.388 Version/12.15', 'Content-Type: multipart/form-data', "Authorization: {$this->integrationDetails->api_key}"));
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, array('attachment' => curl_file_create($file)));
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                $result = curl_exec($curl);
                curl_close($curl);
            }
        }

        return $result;
    }

    private static function formatPhoneNumber($field)
    {
        if (is_array($field) || is_object($field)  || !preg_match('/^\+?[0-9\s\-\(\)]+$/', $field)) {
            return $field;
        }

        $leadingPlus      = $field[0] === '+' ? '+' : '';
        $cleanedNumber    = preg_replace('/[^\d]/', '', $field);
        return $leadingPlus . trim($cleanedNumber);
    }

    public function execute($fieldValues, $fieldMap, $actionName)
    {
        $finalData   = $this->generateReqDataFromFieldMap($fieldValues, $fieldMap);
        if ($actionName === 'task') {
            $apiResponse = $this->addTask($finalData, $fieldValues);
            $apiResponse = json_decode($apiResponse) ?? $apiResponse;
        }

        if (!empty($apiResponse->id)) {
            $res = [$this->typeName . ' successfully'];
            LogHandler::save($this->integrationId, json_encode(['type' => $this->type, 'type_name' => $this->typeName]), 'success', json_encode($res));
        } else {
            LogHandler::save($this->integrationId, json_encode(['type' => $this->type, 'type_name' => $this->type . ' creating']), 'error', json_encode($apiResponse));
        }
        return $apiResponse;
    }
}
