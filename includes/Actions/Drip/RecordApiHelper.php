<?php

/**
 * Drip Record Api
 */

namespace BitCode\FI\Actions\Drip;

use BitCode\FI\Core\Util\Common;
use BitCode\FI\Core\Util\HttpHelper;
use BitCode\FI\Log\LogHandler;

/**
 * Provide functionality for Record insert,update, exist
 */
class RecordApiHelper
{
    private $_defaultHeader;

    private $_integrationID;

    public function __construct($api_token, $integId)
    {
        $this->_defaultHeader = [
            'Authorization' => 'Basic ' . base64_encode("{$api_token}:"),
            'Content-Type'  => 'application/json'
        ];

        $this->_integrationID = $integId;
    }

    public function upsertSubscriber($accountId, $finalData)
    {
        if (empty($accountId)) {
            return ['success' => false, 'message' => 'Account id is Required', 'code' => 400];
        }

        $apiEndpoints = 'https://api.getdrip.com/v2/' . $accountId . '/subscribers';

        if (empty($finalData['email'])) {
            return ['success' => false, 'message' => 'Required field Email is empty', 'code' => 400];
        }

        $subscriberData = $customFieldsData = [];
        $staticFieldsKey = ['email', 'first_name', 'last_name', 'address1', 'address2', 'city', 'state', 'zip', 'country', 'phone', 'time_zone', 'ip_address'];

        foreach ($finalData as $key => $value) {
            if (\in_array($key, $staticFieldsKey)) {
                $subscriberData[$key] = $value;
            } else {
                $customFieldsData[$key] = $value;
            }
        }

        if (!empty($customFieldsData)) {
            $subscriberData['custom_fields'] = (object) $customFieldsData;
        }

        $requestParams = (object) [
            'subscribers' => [
                (object) $subscriberData
            ]
        ];

        return HttpHelper::post($apiEndpoints, wp_json_encode($requestParams), $this->_defaultHeader);
    }

    public function generateReqDataFromFieldMap($data, $fieldMap)
    {
        $dataFinal = [];
        foreach ($fieldMap as $value) {
            $triggerValue = $value->formField;
            $actionValue = $value->dripField;
            if ($triggerValue === 'custom') {
                $dataFinal[$actionValue] = Common::replaceFieldWithValue($value->customValue, $data);
            } elseif (!\is_null($data[$triggerValue])) {
                $dataFinal[$actionValue] = $data[$triggerValue];
            }
        }

        return $dataFinal;
    }

    public function execute($fieldValues, $fieldMap, $accountId)
    {
        $finalData = $this->generateReqDataFromFieldMap($fieldValues, $fieldMap);
        $apiResponse = $this->upsertSubscriber($accountId, $finalData);

        if (isset($apiResponse->subscribers)) {
            $res = ['message' => 'Subscriber upserted successfully'];
            LogHandler::save($this->_integrationID, wp_json_encode(['type' => 'subscriber', 'type_name' => 'Subscriber upsert']), 'success', wp_json_encode($res));
        } else {
            LogHandler::save($this->_integrationID, wp_json_encode(['type' => 'subscriber', 'type_name' => 'Subscriber upsert']), 'error', wp_json_encode($apiResponse));
        }

        return $apiResponse;
    }
}
