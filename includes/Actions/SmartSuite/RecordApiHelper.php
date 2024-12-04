<?php

/**
 * SmartSuite Record Api
 */

namespace BitCode\FI\Actions\SmartSuite;

use BitCode\FI\Core\Util\HttpHelper;
use BitCode\FI\Log\LogHandler;

/**
 * Provide functionality for Record insert, upsert
 */
class RecordApiHelper
{
    private $integrationDetails;

    private $integrationId;

    private $tokenDetails;

    private $apiUrl;

    private $defaultHeader;

    private $type;

    private $typeName;

    public function __construct($integrationDetails, $integId, $tokenDetails, $apiKey, $apiSecret)
    {
        $this->integrationDetails = $integrationDetails;
        $this->integrationId = $integId;
        $this->tokenDetails = $tokenDetails;
        $this->apiUrl = 'https://app.smartsuite.com/api/v1/';
        $this->defaultHeader = [
            'ACCOUNT-ID'    => $apiKey,
            'Authorization' => 'Token ' . $apiSecret,
            //  'Authorization' => "Bearer {$tokenDetails->access_token}",
            'Content-Type' => 'application/json'
        ];
    }

    public function addCampaign($finalData)
    {
        error_log(print_r($finalData, true));
        if (empty($finalData['name'])) {
            return ['success' => false, 'message' => __('Required field Name is empty', 'bit-integrations'), 'code' => 400];
        }

        $this->type = 'Campaign';
        $this->typeName = 'Campaign created';
        $apiEndpoint = $this->apiUrl . '/campaigns';

        return HttpHelper::post($apiEndpoint, wp_json_encode($finalData), $this->defaultHeader);
    }

    public function addContact($finalData)
    {
        $requestParams = [];
        foreach ($finalData as $key => $value) {
            $requestParams['name'] = $value;
        }
        $requestParams['logo_icon'] = 'overline';
        $requestParams['logo_color'] = '#3A86FF';

        $apiEndpoint = $this->apiUrl . 'solutions/';
        $apiResponse = HttpHelper::post($apiEndpoint, wp_json_encode($requestParams), $this->defaultHeader);

        error_log(print_r('emon test', true));
        error_log(print_r($apiResponse, true));

        return $apiResponse;
    }

    public function generateReqDataFromFieldMap($data, $fieldMap)
    {
        $dataFinal = [];
        foreach ($fieldMap as $value) {
            $triggerValue = $value->formField;
            $actionValue = $value->smartSuiteFormField;
            $dataFinal[$actionValue] = ($triggerValue === 'custom') ? $value->customValue : $data[$triggerValue];
        }

        return $dataFinal;
    }

    public function execute($fieldValues, $fieldMap, $actionName)
    {
        $finalData = $this->generateReqDataFromFieldMap($fieldValues, $fieldMap);
        if (0 && $actionName === 'campaign') {
            $apiResponse = $this->addCampaign($finalData);
        } else {
            $apiResponse = $this->addContact($finalData);
        }

        if (1 || isset($apiResponse->id)) {
            $res = [$this->typeName . ' successfully'];
            LogHandler::save($this->integrationId, wp_json_encode(['type' => $this->type, 'type_name' => $this->typeName]), 'success', wp_json_encode($res));
        } else {
            LogHandler::save($this->integrationId, wp_json_encode(['type' => $this->type, 'type_name' => $this->type . ' creating']), 'error', wp_json_encode($apiResponse->message));
        }

        return $apiResponse;
    }
}
