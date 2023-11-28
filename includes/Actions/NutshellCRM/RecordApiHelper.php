<?php

/**
 * NutshellCRM Record Api
 */

namespace BitCode\FI\Actions\NutshellCRM;

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

    public function __construct($integrationDetails, $integId, $userName, $apiToken)
    {
        $this->integrationDetails = $integrationDetails;
        $this->integrationId      = $integId;
        $this->apiUrl             = "https://app.nutshell.com/api/v1/json";
        $this->defaultHeader      = [
                "Authorization" => 'Basic ' . base64_encode("$userName:$apiToken"),
                "Content-type"  => "application/json",
        ];
    }

    public function addPeople($finalData)
    {
        if (empty($finalData['first_name'] || $finalData['email'])) {
            return ['success' => false, 'message' => 'Required field Full Name is empty', 'code' => 400];
        }

        $staticFieldsKeys = ['first_name','email','last_name','phone','address_1','city','state','postalCode','country',];

        foreach ($finalData as $key => $value) {
            if (in_array($key, $staticFieldsKeys)) {
                if (($key == 'address_1' || $key == 'city' || $key == 'state' || $key == 'postalCode' || $key == 'country')) {

                    $requestParams['address'][$key] =   $value;
                } elseif ($key == 'first_name') {
                    $requestParams['name']['givenName'] =   $value;
                } elseif ($key == 'last_name') {
                    $requestParams['name']['familyName'] =   $value;
                } else {
                    $requestParams[$key] = $value;
                }
            } else {
                $requestParams['customFields'][] = (object) [
                    $key   => $value,
                ];
            }
        }

        $this->type                     = 'People';
        $this->typeName                 = 'People created';

        $body = [
            'method'    => 'newContact',
            'id'        => 'randomstring',
            'params'    => (object) [
                'company' => $requestParams
            ]
        ];

        $apiEndpoint                    = $this->apiUrl;

        return HttpHelper::post($apiEndpoint, json_encode($body), $this->defaultHeader);
    }

    public function addCompany($finalData)
    {
        if (empty($finalData['name'])) {
            return ['success' => false, 'message' => 'Required field Full Name is empty', 'code' => 400];
        }

        $staticFieldsKeys = ['name','url','phone','address_1','city','state','postalCode','country',];

        foreach ($finalData as $key => $value) {
            if (in_array($key, $staticFieldsKeys)) {
                if (($key == 'address_1' || $key == 'city' || $key == 'state' || $key == 'postalCode' || $key == 'country')) {
                    $requestParams['address'][$key] =   $value;
                } else {
                    $requestParams[$key] = $value;
                }
            } else {
                $requestParams['customFields'][] = (object) [
                    $key   => $value,
                ];
            }
        }

        if ($this->integrationDetails->actions->Contact) {
            $requestParams['contacts'][] = (object)[
                "id" => ($this->integrationDetails->selectedContact)
            ];
        }
        if ($this->integrationDetails->actions->CompanyType) {
            $requestParams['accountTypeId'] = ($this->integrationDetails->selectedCompanyType);
        }

        $this->type                     = 'People';
        $this->typeName                 = 'People created';

        $body = [
            'method'    => 'newAccount',
            'id'        => 'randomstring',
            'params'    => (object) [
                'account' => $requestParams
            ]
        ];

        $apiEndpoint                    = $this->apiUrl;

        return HttpHelper::post($apiEndpoint, json_encode($body), $this->defaultHeader);

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
            $actionValue  = $value->nutshellCRMFormField;
            $dataFinal[$actionValue] = ($triggerValue === 'custom') ? $value->customValue : $data[$triggerValue];
        }
        return $dataFinal;
    }

    public function execute($fieldValues, $fieldMap, $actionName)
    {
        $finalData   = $this->generateReqDataFromFieldMap($fieldValues, $fieldMap);
        if ($actionName === "people") {
            $apiResponse = $this->addPeople($finalData);
        } elseif ($actionName === "company") {
            $apiResponse = $this->addCompany($finalData);
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
