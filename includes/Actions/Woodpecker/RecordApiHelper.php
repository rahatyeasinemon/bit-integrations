<?php

/**
 * Woodpecker Record Api
 */

namespace BitCode\FI\Actions\Woodpecker;

use BitCode\FI\Core\Util\HttpHelper;
use BitCode\FI\Log\LogHandler;

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

    public function __construct($integrationDetails, $integId, $apiKey)
    {
        $this->integrationDetails = $integrationDetails;
        $this->integrationId      = $integId;
        $this->apiUrl             = "https://api.woodpecker.co/rest/v1";
        $this->defaultHeader      = [
            "Authorization" => "Basic $apiKey",
            "Content-type"  => "application/json",
        ];
    }

    private function setData($finalData)
    {
        $formData = [];
        $customfields = [];
        $addressKeys = ['city', 'country', 'region', 'state_region', 'street', 'zip', '_dirty'];
        foreach ($finalData as $key => $value) {
            if (array_search($key, $addressKeys) !== false) {
                $formData['address'][$key] = $value;
            } elseif (strpos($key, 'custom_field_') !== false) {
                $custom_key = explode('custom_field_', $key);
                $customfields[$custom_key[1]] = $value;
            } else {
                $formData[$key] = $value;
            }
        }

        if (isset($this->integrationDetails->selectedTags) && !empty($this->integrationDetails->selectedTags)) {
            $formData['tags'] = explode(',', $this->integrationDetails->selectedTags);
        }
        if (!empty($customfields)) {
            $formData['custom'] = (object) $customfields;
        }

        return $formData;
    }

    public function addProspects($finalData, $actionName, $actions)
    {
        if (empty($finalData['email'])) {
            return ['success' => false, 'message' => 'Required field Email is empty', 'code' => 400];
        }

        $requestData = [];
        $requestData['update'] = $actions->update ? true : false;
        $requestData['prospects'] = [(object) $finalData];

        if ($actionName === "adding_prospects_to_the_prospects_list") {
            $apiEndpoint    = $this->apiUrl . "/add_prospects_list";
        } else {
            $apiEndpoint    = $this->apiUrl . "/add_prospects_campaign";
        }


        $this->type     = 'Prospects';
        $this->typeName = 'Prospects created';
        return HttpHelper::post($apiEndpoint, json_encode($requestData), $this->defaultHeader);
    }

    public function addContact($finalData)
    {
        if (empty($finalData['firstname'])) {
            return ['success' => false, 'message' => 'Required field First Name is empty', 'code' => 400];
        }
        if (empty($finalData['email'])) {
            return ['success' => false, 'message' => 'Required field Email Address is empty', 'code' => 400];
        }

        $this->type     = 'Contact';
        $this->typeName = 'Contact created';
        $formData       = $this->setData($finalData);
        $apiEndpoint    = $this->apiUrl . "/contacts";
        return HttpHelper::post($apiEndpoint, json_encode($formData), $this->defaultHeader);
    }

    public function addOpprtunity($finalData)
    {
        if (empty($finalData['name'])) {
            return ['success' => false, 'message' => 'Required Opportunity Name is empty', 'code' => 400];
        } elseif (!isset($this->integrationDetails->selectedAccount) || empty($this->integrationDetails->selectedAccount)) {
            return ['success' => false, 'message' => 'Required Account field is empty', 'code' => 400];
        } elseif (!isset($this->integrationDetails->selectedPipeline) || empty($this->integrationDetails->selectedPipeline)) {
            return ['success' => false, 'message' => 'Required Pipeline field is empty', 'code' => 400];
        } elseif (!isset($this->integrationDetails->selectedStage) || empty($this->integrationDetails->selectedStage)) {
            return ['success' => false, 'message' => 'Required Stage field is empty', 'code' => 400];
        }

        $this->type             = 'Opportunity';
        $this->typeName         = 'Opportunity created';
        $formData               = $this->setData($finalData);
        $formData['account']    = $this->integrationDetails->selectedAccount;
        $formData['stage']      = $this->integrationDetails->selectedStage;
        $apiEndpoint            = $this->apiUrl . "/opportunities";
        return HttpHelper::post($apiEndpoint, json_encode($formData), $this->defaultHeader);
    }

    public function generateReqDataFromFieldMap($data, $fieldMap)
    {
        $dataFinal = [];
        foreach ($fieldMap as $value) {
            $triggerValue = $value->formField;
            $actionValue  = $value->woodpeckerFormField;
            $dataFinal[$actionValue] = ($triggerValue === 'custom') ? $value->customValue : $data[$triggerValue];
        }
        return $dataFinal;
    }

    public function execute($fieldValues, $fieldMap, $actionName, $actions)
    {
        $finalData   = $this->generateReqDataFromFieldMap($fieldValues, $fieldMap);
        if ($actionName === "adding_prospects_to_the_prospects_list" || $actionName === "adding_prospects_to_the_campaign") {
            $apiResponse = $this->addProspects($finalData, $actionName, $actions);
        } elseif ($actionName === "contacts") {
            $apiResponse = $this->addContact($finalData);
        } elseif ($actionName === "opportunities") {
            $apiResponse = $this->addOpprtunity($finalData);
        }

        if (isset($apiResponse->status) && $apiResponse->status->status === "ERROR") {
            LogHandler::save($this->integrationId, json_encode(['type' => $this->type, 'type_name' => $this->type . ' creating']), 'error', json_encode($apiResponse));
        } else {
            $res = [$this->typeName . '  successfully'];
            LogHandler::save($this->integrationId, json_encode(['type' => $this->type, 'type_name' => $this->typeName]), 'success', json_encode($res));
        }
        return $apiResponse;
    }
}
