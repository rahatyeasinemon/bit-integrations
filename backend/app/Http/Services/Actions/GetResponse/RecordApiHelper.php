<?php

/**
 * GetResponse    Record Api
 */

namespace BitApps\BTCBI\Http\Services\Actions\GetResponse;

use BitApps\BTCBI\Util\Common;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BitApps\BTCBI\Http\Services\Log\LogHandler;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for Record insert, upsert
 */
class RecordApiHelper
{
    private $_integrationID;
    private $baseUrl = 'https://api.getresponse.com/v3/';

    public function __construct($integrationDetails, $integId)
    {
        $this->_integrationDetails = $integrationDetails;
        $this->_integrationID      = $integId;
        $this->_defaultHeader      = [
            'X-Auth-Token' => 'api-key ' . $this->_integrationDetails->auth_token
        ];
    }

    public function existSubscriber($auth_token, $email)
    {
        if (empty($auth_token)) {
            return Response::error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }

        $apiEndpoint = $this->baseUrl . "contacts?query[email]=$email";

        $response = Http::request($apiEndpoint, 'Get', null, $this->_defaultHeader);

        if (empty($response)) {
            return false;
        } else {
            return $response;
        }
    }

    public function addContactToCampaign($auth_token, $selectedTags, $finalData, $campaign)
    {
        $apiEndpoint = $this->baseUrl . 'contacts';
        $tags         = [];

        if (!empty($selectedTags)) {
            $splitSelectedTags = explode(',', $selectedTags);
            foreach ($splitSelectedTags as $tag) {
                $tags[] = (object) ["tagId" => $tag];
            }
        }

        if (empty($finalData['email'])) {
            return ['success' => false, 'message' => 'Required field Email is empty', 'code' => 400];
        }

        $requestParams = [
            'email'    => $finalData['email'],
            'campaign' => $campaign,
        ];

        if (!empty($tags)) {
            $requestParams['tags'] = $tags;
        }

        foreach ($finalData as $key => $value) {
            if ($key !== 'email') {
                if ($key === 'name') {
                    $requestParams[$key] = $value;
                } else {
                    $requestParams['customFieldValues'][] = (object) ['customFieldId' => $key, 'value' => (array) $value];
                }
            }
        }

        $email   = $finalData['email'];
        $isExist = $this->existSubscriber($auth_token, $email);

        if ($isExist && !empty($this->_integrationDetails->actions->update)) {
            $contactId    = $isExist[0]->contactId;
            $apiEndpoint = $this->baseUrl . "contacts/$contactId";
            $response     = Http::request($apiEndpoint, 'Post', $requestParams, $this->_defaultHeader);
        } else {
            $response = Http::request($apiEndpoint, 'Post', $requestParams, $this->_defaultHeader);
        }
        return $response;
    }

    public function generateReqDataFromFieldMap($data, $fieldMap)
    {
        $dataFinal = [];

        foreach ($fieldMap as $value) {
            $triggerValue = $value->formField;
            $actionValue  = $value->getResponseFormField;
            if ($triggerValue === 'custom') {
                $dataFinal[$actionValue] = Common::replaceFieldWithValue($value->customValue, $data);
            } elseif (!is_null($data[$triggerValue])) {
                $dataFinal[$actionValue] = $data[$triggerValue];
            }
        }
        return $dataFinal;
    }

    public function execute(
        $selectedTag,
        $type,
        $fieldValues,
        $fieldMap,
        $auth_token,
        $campaign
    ) {
        $finalData   = $this->generateReqDataFromFieldMap($fieldValues, $fieldMap);
        $apiResponse = $this->addContactToCampaign($auth_token, $selectedTag, $finalData, $campaign);

        if ($apiResponse->contactId) {
            $updatedContactId = $apiResponse->contactId;
            $apiResponse      = null;
        }

        if ($apiResponse == null) {
            $res = ['message' => $updatedContactId ? 'Contact updated successfully' : 'Contact created successfully'];
            LogHandler::save($this->_integrationID, json_encode(['type' => 'contact', 'type_name' => $updatedContactId ? 'update-contact' : 'add-contact']), 'success', json_encode($res));
        } else {
            LogHandler::save($this->_integrationID, json_encode(['type' => 'contact', 'type_name' => $apiResponse->code == 1008 ? 'update-contact' : 'add-contact']), 'error', json_encode($apiResponse));
        }
        return $apiResponse;
    }
}
