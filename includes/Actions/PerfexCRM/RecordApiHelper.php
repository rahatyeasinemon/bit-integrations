<?php

/**
 * PerfexCRM Record Api
 */

namespace BitCode\FI\Actions\PerfexCRM;

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

    public function __construct($integrationDetails, $integId, $apiToken, $domain)
    {
        $this->integrationDetails = $integrationDetails;
        $this->integrationId      = $integId;
        $this->apiUrl             = "{$domain}/api";
        $this->defaultHeader      = [
            "authtoken"     => $apiToken,
            "Content-type"  => "application/json",
            "Content-type"  => "application/x-www-form-urlencoded",
        ];
    }

    public function addCustomer($finalData)
    {
        if (empty($finalData['company'])) {
            return ['success' => false, 'message' => 'Required field Company is empty', 'code' => 400];
        }

        $this->type     = 'Customer';
        $this->typeName = 'Customer created';
        $apiEndpoint = $this->apiUrl . "/customers";
        return HttpHelper::post($apiEndpoint, $finalData, $this->defaultHeader);
    }

    public function addContact($finalData)
    {
        if (empty($finalData['firstname'])) {
            return ['success' => false, 'message' => 'Required field First Name is empty', 'code' => 400];
        } elseif (empty($finalData['lastname'])) {
            return ['success' => false, 'message' => 'Required field Last Name is empty', 'code' => 400];
        } elseif (empty($finalData['email'])) {
            return ['success' => false, 'message' => 'Required field Email is empty', 'code' => 400];
        } elseif (isset($this->integrationDetails->selectedCustomer) && empty($this->integrationDetails->selectedCustomer)) {
            return ['success' => false, 'message' => 'Required field Customer is empty', 'code' => 400];
        }

        $finalData['customer_id'] = ($this->integrationDetails->selectedCustomer);
        $finalData['send_set_password_email'] = 'on';

        if (isset($this->integrationDetails->selectedDirection) && !empty($this->integrationDetails->selectedDirection)) {
            $finalData['direction'] = ($this->integrationDetails->selectedDirection);
        }
        if (isset($this->integrationDetails->selectedPermission) && !empty($this->integrationDetails->selectedPermission)) {
            $finalData['permissions'] = explode(',', $this->integrationDetails->selectedPermission);
        }
        if (isset($this->integrationDetails->actions->contactIsPrimary) && !empty($this->integrationDetails->actions->contactIsPrimary)) {
            $finalData['is_primary'] = $this->integrationDetails->actions->contactIsPrimary ? 'on' : $this->integrationDetails->actions->contactIsPrimary;
        }

        $this->type     = 'Contact';
        $this->typeName = 'Contact created';
        $apiEndpoint = $this->apiUrl . "/contacts";
        return HttpHelper::post($apiEndpoint, $finalData, $this->defaultHeader);
    }

    public function addLead($finalData)
    {
        if (empty($finalData['name'])) {
            return ['success' => false, 'message' => 'Required field name is empty', 'code' => 400];
        }

        $finalData['currency'] = isset($this->integrationDetails->selectedCurrency) && !empty($this->integrationDetails->selectedCurrency) ? $this->integrationDetails->selectedCurrency : 'USD';

        if (isset($this->integrationDetails->selectedTag) && !empty($this->integrationDetails->selectedTag)) {
            $finalData['tags'] = ($this->integrationDetails->selectedTag);
        }
        if (isset($this->integrationDetails->selectedType) && !empty($this->integrationDetails->selectedType)) {
            $finalData['type'] = ($this->integrationDetails->selectedType);
        }
        if (isset($this->integrationDetails->selectedCRMOwner) && !empty($this->integrationDetails->selectedCRMOwner)) {
            $finalData['owner'] = ($this->integrationDetails->selectedCRMOwner);
        }

        $this->type     = 'Company';
        $this->typeName = 'Company created';
        $apiEndpoint = $this->apiUrl . "company/v4";
        return HttpHelper::post($apiEndpoint, json_encode($finalData), $this->defaultHeader);
    }

    public function addProject($finalData)
    {
        if (empty($finalData['title'])) {
            return ['success' => false, 'message' => 'Required field title is empty', 'code' => 400];
        }

        $finalData['currency'] = isset($this->integrationDetails->selectedCurrency) && !empty($this->integrationDetails->selectedCurrency) ? $this->integrationDetails->selectedCurrency : 'USD';
        $finalData['status'] = isset($this->integrationDetails->selectedStatus) && !empty($this->integrationDetails->selectedStatus) ? $this->integrationDetails->selectedStatus : 'Open';
        $finalData['source'] = isset($this->integrationDetails->selectedSource) && !empty($this->integrationDetails->selectedSource) ? $this->integrationDetails->selectedSource : 'Ads';
        $finalData['priority'] = isset($this->integrationDetails->selectedPriority) && !empty($this->integrationDetails->selectedPriority) ? $this->integrationDetails->selectedPriority : 'High';

        if (isset($this->integrationDetails->selectedTag) && !empty($this->integrationDetails->selectedTag)) {
            $finalData['tags'] = ($this->integrationDetails->selectedTag);
        }
        if (isset($this->integrationDetails->selectedType) && !empty($this->integrationDetails->selectedType)) {
            $finalData['type'] = ($this->integrationDetails->selectedType);
        }
        if (isset($this->integrationDetails->selectedCRMContact) && !empty($this->integrationDetails->selectedCRMContact)) {
            $finalData['primaryContact'] = ($this->integrationDetails->selectedCRMContact);
        }
        if (isset($this->integrationDetails->selectedCRMOwner) && !empty($this->integrationDetails->selectedCRMOwner)) {
            $finalData['owner'] = ($this->integrationDetails->selectedCRMOwner);
        }
        if (isset($this->integrationDetails->selectedCRMPipeline) && !empty($this->integrationDetails->selectedCRMPipeline)) {
            $finalData['pipeline'] = ($this->integrationDetails->selectedCRMPipeline);
        }
        if (isset($this->integrationDetails->selectedCRMStage) && !empty($this->integrationDetails->selectedCRMStage)) {
            $finalData['stage'] = ($this->integrationDetails->selectedCRMStage);
        }

        $this->type     = 'Deal';
        $this->typeName = 'Deal created';
        $apiEndpoint = $this->apiUrl . "deal/v4";
        return HttpHelper::post($apiEndpoint, json_encode($finalData), $this->defaultHeader);
    }

    public function generateReqDataFromFieldMap($data, $fieldMap)
    {
        $dataFinal = [];
        foreach ($fieldMap as $value) {
            $triggerValue = $value->formField;
            $actionValue  = $value->perfexCRMFormField;
            $dataFinal[$actionValue] = ($triggerValue === 'custom') ? $value->customValue : $data[$triggerValue];
        }
        return $dataFinal;
    }

    public function execute($fieldValues, $fieldMap, $actionName)
    {
        $finalData   = $this->generateReqDataFromFieldMap($fieldValues, $fieldMap);
        if ($actionName === "customer") {
            $apiResponse = $this->addCustomer($finalData);
        } elseif ($actionName === "contact") {
            $apiResponse = $this->addContact($finalData);
        } elseif ($actionName === "lead") {
            $apiResponse = $this->addLead($finalData);
        } elseif ($actionName === "project") {
            $apiResponse = $this->addProject($finalData);
        }

        if ($apiResponse->status) {
            $res = [$this->typeName . '  successfully'];
            LogHandler::save($this->integrationId, json_encode(['type' => $this->type, 'type_name' => $this->typeName]), 'success', json_encode($res));
        } else {
            LogHandler::save($this->integrationId, json_encode(['type' => $this->type, 'type_name' => $this->type . ' creating']), 'error', json_encode($apiResponse));
        }
        return $apiResponse;
    }
}
