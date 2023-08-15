<?php

/**
 * OneHashCRM Record Api
 */

namespace BitCode\FI\Actions\OneHashCRM;

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

    public function __construct($integrationDetails, $integId, $apiKey, $apiSecret, $domain)
    {
        $this->integrationDetails = $integrationDetails;
        $this->integrationId      = $integId;
        $this->apiUrl             = "https://{$domain}.onehash.ai/api/resource";
        $this->defaultHeader      = [
            "Authorization" => "token {$apiKey}:$apiSecret",
            "Content-type"  => "application/json",
        ];
    }

    public function addCustomer($finalData)
    {
        if (empty($finalData['customer_name'])) {
            return ['success' => false, 'message' => 'Required field Full Name is empty', 'code' => 400];
        } elseif (!isset($this->integrationDetails->selectedCustomerType) || empty($this->integrationDetails->selectedCustomerType)) {
            return ['success' => false, 'message' => 'Required field Customer Type is empty', 'code' => 400];
        }

        $finalData['customer_type']     = $this->integrationDetails->selectedCustomerType;
        $finalData['customer_group']    = "All Customer Groups";
        $finalData['territory']         = "All Territories";
        $this->type                     = 'Customer';
        $this->typeName                 = 'Customer created';
        $apiEndpoint                    = $this->apiUrl . "/Customer";
        return HttpHelper::post($apiEndpoint, json_encode($finalData), $this->defaultHeader);
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

        $finalData['status'] = 1;
        $finalData['source'] = 1;

        if (isset($this->integrationDetails->selectedCustomer) && !empty($this->integrationDetails->selectedCustomer)) {
            $finalData['client_id'] = ($this->integrationDetails->selectedCustomer);
        }
        if (isset($this->integrationDetails->actions->leadIsPublic) && !empty($this->integrationDetails->actions->leadIsPublic)) {
            $finalData['is_public'] = $this->integrationDetails->actions->leadIsPublic;
        }
        if (isset($this->integrationDetails->actions->contactedToday) && !empty($this->integrationDetails->actions->contactedToday)) {
            $finalData['contacted_today'] = $this->integrationDetails->actions->contactedToday;
        }

        $this->type     = 'Lead';
        $this->typeName = 'Lead created';
        $apiEndpoint = $this->apiUrl . "/leads";
        return HttpHelper::post($apiEndpoint, $finalData, $this->defaultHeader);
    }

    public function generateReqDataFromFieldMap($data, $fieldMap)
    {
        $dataFinal = [];
        foreach ($fieldMap as $value) {
            $triggerValue = $value->formField;
            $actionValue  = $value->oneHashCRMFormField;
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
