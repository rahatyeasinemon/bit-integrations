<?php

namespace BitApps\BTCBI\Http\Services\Actions\Mailup;

use BitApps\BTCBI\Util\Common;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BitApps\BTCBI\Http\Services\Log\LogHandler;

class RecordApiHelper
{
    private $_integrationID;

    public function __construct($integrationDetails, $integId, $access_token)
    {
        $this->_integrationDetails = $integrationDetails;
        $this->_integrationID      = $integId;
        $this->_defaultHeader      = [
            'Authorization' => 'Bearer ' . $access_token,
            'Content-Type'  => 'application/json'
        ];
    }

    public function addSubscriber($selectedList, $selectedGroups, $finalData)
    {
        $apiEndpoint = "https://services.mailup.com/API/v1.1/Rest/ConsoleService.svc/Console/List/{$selectedList}/Recipient";

        if (!empty($selectedGroups)) {
            $apiEndpoint = "https://services.mailup.com/API/v1.1/Rest/ConsoleService.svc/Console/Group/{$selectedGroups}/Recipient";
        }

        if (!empty($this->_integrationDetails->actions->doubleOptIn)) {
            $apiEndpoint = $apiEndpoint . '?ConfirmEmail=true';
        }

        if (empty($finalData['Email'])) {
            return ['success' => false, 'message' => 'Required field Email is empty', 'code' => 400];
        }

        $requestParams = [];
        $customFields  = [];

        foreach ($finalData as $key => $value) {
            if ($key == 'Email') {
                $requestParams[$key] = $value;
            } else {
                $customFields[] = (object) [
                    "Id"    => $key,
                    "Value" => $value
                ];
            }
        }

        if (!empty($customFields)) {
            $requestParams['Fields'] = $customFields;
        }
        return Http::request($apiEndpoint, 'Post', json_encode($requestParams), $this->_defaultHeader);
    }

    public function generateReqDataFromFieldMap($data, $fieldMap)
    {
        $dataFinal = [];
        foreach ($fieldMap as $value) {
            $triggerValue = $value->formField;
            $actionValue  = $value->mailupFormField;
            if ($triggerValue === 'custom') {
                $dataFinal[$actionValue] = Common::replaceFieldWithValue($value->customValue, $data);
            } elseif (!is_null($data[$triggerValue])) {
                $dataFinal[$actionValue] = $data[$triggerValue];
            }
        }
        return $dataFinal;
    }

    public function execute($selectedList, $selectedGroup, $fieldValues, $fieldMap)
    {
        $finalData   = $this->generateReqDataFromFieldMap($fieldValues, $fieldMap);
        $apiResponse = $this->addSubscriber($selectedList, $selectedGroup, $finalData);

        if (gettype($apiResponse) === "integer") {
            $res = ['message' => 'Subscriber added successfully'];
            LogHandler::save($this->_integrationID, json_encode(['type' => 'subscriber', 'type_name' => 'Subscriber added']), 'success', json_encode($res));
        } else {
            LogHandler::save($this->_integrationID, json_encode(['type' => 'subscriber', 'type_name' => 'Adding Subscriber']), 'error', json_encode($apiResponse));
        }
        return $apiResponse;
    }
}
