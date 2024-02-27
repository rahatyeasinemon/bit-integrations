<?php

namespace BitApps\BTCBI\Http\Services\Actions\Mailify;

use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BitApps\BTCBI\Http\Services\Log\LogHandler;

class RecordApiHelper
{
    private $_integrationID;
    private $_integrationDetails;
    private $_defaultHeader;

    public function __construct($integrationDetails, $integId, $accountId, $apiKey)
    {
        $this->_integrationDetails = $integrationDetails;
        $this->_integrationID      = $integId;
        $this->_defaultHeader      = [
            'Authorization' => 'Basic ' . base64_encode("$accountId:$apiKey"),
            'Content-Type'  => 'application/json'
        ];
    }

    //for updating contact data through email id.
    public function updateContact($id, $data, $existContact, $selectedList)
    {
        $contactData = $data;
        $apiEndpoint = "https://mailifyapis.com/v1/lists/{$selectedList}/contacts/{$id}";
        return  Http::request($apiEndpoint, 'PUT', json_encode($contactData), $this->_defaultHeader);
    }

    public function addContact($selectedList, $finalData)
    {
        $apiEndpoint = "https://mailifyapis.com/v1/lists/{$selectedList}/contacts";
        $res =  Http::request($apiEndpoint, 'Post', json_encode($finalData), $this->_defaultHeader);
        return $res;
    }

    public function generateReqDataFromFieldMap($data, $fieldMap)
    {
        $dataFinal = [];
        foreach ($fieldMap as $value) {
            $triggerValue = $value->formField;
            $actionValue  = $value->mailifyField;

            if ($triggerValue === 'custom') {
                $dataFinal[$actionValue] = $value->customValue;
            } elseif (!is_null($data[$triggerValue])) {
                if ($actionValue === 'EMAIL_ID') {
                    $dataFinal['email'] = $data[$triggerValue];
                } elseif ($actionValue === 'PHONE_ID') {
                    $dataFinal['phone'] = $data[$triggerValue];
                } else {
                    $dataFinal[$actionValue] = $data[$triggerValue];
                }
            }
        }

        return $dataFinal;
    }

    //Check if a contact exists through email.
    private function existContact($selectedList, $email)
    {
        $apiEndpoint = "https://mailifyapis.com/v1/lists/{$selectedList}/contacts?email={$email}";
        return Http::request($apiEndpoint, 'Get', null, $this->_defaultHeader);
    }

    public function execute($selectedList, $fieldValues, $fieldMap, $actions)
    {
        $finalData   = (object) $this->generateReqDataFromFieldMap($fieldValues, $fieldMap);
        $existContact = $this->existContact($selectedList, $finalData->email);

        if ($existContact === 'null') {
            $apiResponse = $this->addContact($selectedList, $finalData);

            if ($apiResponse) {
                $res = ['message' => 'Contact added successfully'];
                LogHandler::save($this->_integrationID, json_encode(['type' => 'contact', 'type_name' => 'Contact added']), 'success', json_encode($res));
            } else {
                LogHandler::save($this->_integrationID, json_encode(['type' => 'contact', 'type_name' => 'Adding Contact']), 'error', json_encode($apiResponse));
            }
        } else {
            if ($actions->update) {
                $apiResponse = $this->updateContact($existContact[0]->id, $finalData, $existContact[0], $selectedList);
                if ($apiResponse) {
                    LogHandler::save($this->_integrationID, json_encode(['type' => 'contact', 'type_name' => 'updating Contact']), 'error', json_encode($apiResponse));
                } else {
                    $res = ['message' => 'Contact updated successfully'];
                    LogHandler::save($this->_integrationID, json_encode(['type' => 'contact', 'type_name' => 'Contact updated']), 'success', json_encode($res));
                }
            } else {
                LogHandler::save($this->_integrationID, ['type' => 'contact', 'type_name' => 'Adding Contact'], 'error', 'Email address already exists in the system');
                wp_send_json_error('Email address already exists in the system', 400);
            }
        }

        return $apiResponse;
    }
}
