<?php

namespace BitApps\BTCBI\Http\Services\Actions\Lemlist;

use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BitApps\BTCBI\Http\Services\Log\LogHandler;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

class RecordApiHelper
{
    private $_integrationID;
    private $_integrationDetails;
    private $_defaultHeader;

    public function __construct($integrationDetails, $integId, $apiKey)
    {
        $this->_integrationDetails = $integrationDetails;
        $this->_integrationID      = $integId;
        $this->_defaultHeader      = [
            'Authorization' => 'Basic ' . base64_encode(":$apiKey"),
            'Content-Type'  => 'application/json'
        ];
    }

    public function updateLead($email, $data, $selectedCampaign)
    {
        $contactData = $data;
        $apiEndpoint = "https://api.lemlist.com/api/campaigns/{$selectedCampaign}/leads/{$email}";
        return  Http::request($apiEndpoint, 'PATCH', json_encode($contactData), $this->_defaultHeader);
    }

    public function addLead($selectedCampaign, $finalData)
    {
        $apiEndpoint = "https://api.lemlist.com/api/campaigns/{$selectedCampaign}/leads";
        $res =  Http::request($apiEndpoint, 'Post', json_encode($finalData), $this->_defaultHeader);
        return $res;
    }

    public function generateReqDataFromFieldMap($data, $fieldMap)
    {
        $dataFinal = [];
        foreach ($fieldMap as $value) {
            $triggerValue = $value->formField;
            $actionValue  = $value->lemlistField;

            if ($triggerValue === 'custom') {
                $dataFinal[$actionValue] = $value->customValue;
            } elseif (!is_null($data[$triggerValue])) {
                $dataFinal[$actionValue] = $data[$triggerValue];
            }
        }

        return $dataFinal;
    }

    private function existLead($selectedCampaign, $email)
    {
        $apiEndpoint = "https://api.lemlist.com/api/leads/{$email}?campaignId={$selectedCampaign}";
        return Http::request($apiEndpoint, 'Get', null, $this->_defaultHeader);
    }

    public function execute($selectedCampaign, $fieldValues, $fieldMap, $actions)
    {
        $finalData   = (object) $this->generateReqDataFromFieldMap($fieldValues, $fieldMap);
        $existLead = $this->existLead($selectedCampaign, $finalData->email);

        if (!$existLead->_id) {
            $apiResponse = $this->addLead($selectedCampaign, $finalData);

            if ($apiResponse->_id) {
                $res = ['message' => 'Lead added successfully'];
                LogHandler::save($this->_integrationID, json_encode(['type' => 'Lead', 'type_name' => 'Lead added']), 'success', json_encode($res));
            } else {
                LogHandler::save($this->_integrationID, json_encode(['type' => 'Lead', 'type_name' => 'Adding Lead']), 'error', json_encode($apiResponse));
            }
        } else {
            if ($actions->update) {
                $apiResponse = $this->updateLead($existLead->email, $finalData, $selectedCampaign);

                if ($apiResponse->_id) {
                    $res = ['message' => 'Lead updated successfully'];
                    LogHandler::save($this->_integrationID, json_encode(['type' => 'Lead', 'type_name' => 'Lead updated']), 'success', json_encode($res));
                } else {
                    LogHandler::save($this->_integrationID, json_encode(['type' => 'Lead', 'type_name' => 'updating Lead']), 'error', json_encode($apiResponse));
                }
            } else {
                LogHandler::save($this->_integrationID, ['type' => 'Lead', 'type_name' => 'Adding Lead'], 'error', 'Email address already exists in the system');
                Response::error('Email address already exists in the system', 400);
            }
        }

        return $apiResponse;
    }
}
