<?php

/**
 * Salesflare Record Api
 */

namespace BitCode\FI\Actions\Salesflare;

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
        $this->apiUrl             = "https://api.salesflare.com";
        $this->defaultHeader      = [
            "Authorization" => "Bearer {$apiKey}",
            "Content-type"  => "application/json",
        ];
    }

    public function addAccount($finalData)
    {
        if (empty($finalData['name'])) {
            return ['success' => false, 'message' => 'Required field Account Name is empty', 'code' => 400];
        }

        $formData = [];
        $addressKeys = ['city', 'country', 'region', 'state_region', 'street', 'zip', '_dirty'];
        foreach ($finalData as $key => $value) {
            if (array_search($key, $addressKeys) !== false) {
                $formData['address'][$key] = $value;
            } else {
                $formData[$key] = $value;
            }
        }

        if (isset($this->integrationDetails->selectedTags) && !empty($this->integrationDetails->selectedTags)) {
            $formData['tags'] = explode(',', $this->integrationDetails->selectedTags);
        }

        var_dump($this->integrationDetails->selectedTags);
        die;

        $this->type                     = 'Account';
        $this->typeName                 = 'Account created';
        $apiEndpoint                    = $this->apiUrl . "/account";
        return HttpHelper::post($apiEndpoint, json_encode($finalData), $this->defaultHeader);
    }

    public function addContact($finalData)
    {
        if (empty($finalData['first_name'])) {
            return ['success' => false, 'message' => 'Required field First Name is empty', 'code' => 400];
        }

        if (isset($this->integrationDetails->selectedContactStatus) && !empty($this->integrationDetails->selectedContactStatus)) {
            $finalData['status'] = ($this->integrationDetails->selectedContactStatus);
        }

        $finalData['is_primary_contact']    = true;
        $this->type                         = 'Contact';
        $this->typeName                     = 'Contact created';
        $apiEndpoint                        = $this->apiUrl . "/Contact";
        return HttpHelper::post($apiEndpoint, json_encode($finalData), $this->defaultHeader);
    }

    public function addLead($finalData)
    {
        if (empty($finalData['lead_name'])) {
            return ['success' => false, 'message' => 'Required Person Name is empty', 'code' => 400];
        } elseif (empty($finalData['company_name'])) {
            return ['success' => false, 'message' => 'Required Organization Name is empty', 'code' => 400];
        } elseif (!isset($this->integrationDetails->selectedLeadStatus) || empty($this->integrationDetails->selectedLeadStatus)) {
            return ['success' => false, 'message' => 'Required Lead Status is empty', 'code' => 400];
        }

        if (isset($this->integrationDetails->selectedLeadSource) && !empty($this->integrationDetails->selectedLeadSource)) {
            $finalData['source'] = ($this->integrationDetails->selectedLeadSource);
        }
        if (isset($this->integrationDetails->actions->organizationLead) && !empty($this->integrationDetails->actions->organizationLead)) {
            $finalData['organization_lead'] = $this->integrationDetails->actions->organizationLead;
        }
        if (isset($this->integrationDetails->selectedLeadAddressType) && !empty($this->integrationDetails->selectedLeadAddressType)) {
            $finalData['address_type'] = $this->integrationDetails->selectedLeadAddressType;
        }
        if (isset($this->integrationDetails->selectedLeadType) && !empty($this->integrationDetails->selectedLeadType)) {
            $finalData['type'] = $this->integrationDetails->selectedLeadType;
        }
        if (isset($this->integrationDetails->selectedRequestType) && !empty($this->integrationDetails->selectedRequestType)) {
            $finalData['request_type'] = $this->integrationDetails->selectedRequestType;
        }
        if (isset($this->integrationDetails->selectedMarketSegment) && !empty($this->integrationDetails->selectedMarketSegment)) {
            $finalData['market_segment'] = $this->integrationDetails->selectedMarketSegment;
        }

        $finalData['status']    = $this->integrationDetails->selectedLeadStatus;
        $finalData['territory'] = "All Territories";
        $this->type             = 'Lead';
        $this->typeName         = 'Lead created';
        $apiEndpoint            = $this->apiUrl . "/Lead";
        return HttpHelper::post($apiEndpoint, json_encode($finalData), $this->defaultHeader);
    }

    public function generateReqDataFromFieldMap($data, $fieldMap)
    {
        $dataFinal = [];
        foreach ($fieldMap as $value) {
            $triggerValue = $value->formField;
            $actionValue  = $value->salesflareFormField;
            $dataFinal[$actionValue] = ($triggerValue === 'custom') ? $value->customValue : $data[$triggerValue];
        }
        return $dataFinal;
    }

    public function execute($fieldValues, $fieldMap, $actionName)
    {
        $finalData   = $this->generateReqDataFromFieldMap($fieldValues, $fieldMap);
        if ($actionName === "accounts") {
            $apiResponse = $this->addAccount($finalData);
        } elseif ($actionName === "contacts") {
            $apiResponse = $this->addContact($finalData);
        } elseif ($actionName === "opportunities") {
            $apiResponse = $this->addLead($finalData);
        }

        if (isset($apiResponse->data)) {
            $res = [$this->typeName . '  successfully'];
            LogHandler::save($this->integrationId, json_encode(['type' => $this->type, 'type_name' => $this->typeName]), 'success', json_encode($res));
        } else {
            LogHandler::save($this->integrationId, json_encode(['type' => $this->type, 'type_name' => $this->type . ' creating']), 'error', json_encode($apiResponse));
        }
        return $apiResponse;
    }
}
