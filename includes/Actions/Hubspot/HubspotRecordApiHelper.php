<?php

/**
 * HubSpot Record Api
 */

namespace BitCode\FI\Actions\Hubspot;

use BitCode\FI\Log\LogHandler;
use BitCode\FI\Core\Util\Common;
use BitCode\FI\Core\Util\Helper;
use BitCode\FI\Core\Util\HttpHelper;

/**
 * Provide functionality for Record insert,upsert
 */
class HubspotRecordApiHelper
{
    private $defaultHeader;

    public function __construct($accessToken)
    {
        $this->defaultHeader = [
            'Content-Type'  => 'application/json',
            'authorization' => "Bearer {$accessToken}"
        ];
    }

    public function insertContact($data, $actionName, &$typeName)
    {

        $finalData['properties'] = $data;
        if ($actionName === 'contact' && Helper::proActionFeatExists('Hubspot', 'updateContact') && $id = $this->existsContact($data['email'])) {
            $typeName = 'Contact-Update';
            return $this->updateContact($id, $finalData);
        }

        $actionName = $actionName === 'contact' ? 'contacts' : 'companies';
        $apiEndpoint = "https://api.hubapi.com/crm/v3/objects/{$actionName}";

        return HttpHelper::post($apiEndpoint, wp_json_encode($finalData), $this->defaultHeader);
    }

    private function updateContact($id, $finalData)
    {
        $response = apply_filters('btcbi_hubspot_update_contact', $id, $finalData, $this->defaultHeader);

        if (\is_string($response) && $response == $id) {
            return (object) ['errors' => 'Bit Integration Pro plugin is not installed or activate'];
        }

        return $response;
    }

    private function existsContact($email)
    {
        $apiEndpoint = "https://api.hubapi.com/crm/v3/objects/contacts/{$email}?idProperty=email";

        $response = HttpHelper::get($apiEndpoint, null, $this->defaultHeader);

        return isset($response->id) ? $response->id : false;
    }

    public function insertDeal($finalData)
    {
        foreach ($finalData['associations'] as $key => $association) {
            $associations[$key] = $association;
        }

        foreach ($finalData['properties'] as $key => $property) {
            $properties[] = (object) [
                'name'  => $key,
                'value' => $property
            ];
        }

        $data = [
            'properties'   => $properties,
            'associations' => (object) $associations
        ];

        $apiEndpoint = 'https://api.hubapi.com/deals/v1/deal';

        return HttpHelper::post($apiEndpoint, wp_json_encode($data), $this->defaultHeader);
    }

    public function insertTicket($finalData)
    {
        $data = wp_json_encode(['properties' => $finalData]);
        $apiEndpoint = 'https://api.hubapi.com/crm/v3/objects/tickets';

        return HttpHelper::post($apiEndpoint, $data, $this->defaultHeader);
    }

    public function generateReqDataFromFieldMap($data, $fieldMap, $integrationDetails)
    {
        $dataFinal = [];

        foreach ($fieldMap as $value) {
            $triggerValue = $value->formField;
            $actionValue = $value->hubspotField;
            if ($triggerValue === 'custom') {
                $dataFinal[$actionValue] = Common::replaceFieldWithValue($value->customValue, $data);
            } elseif (!\is_null($data[$triggerValue])) {
                $dataFinal[$actionValue] = \is_array($data[$triggerValue]) ? implode(';', $data[$triggerValue]) : $data[$triggerValue];
            }
        }

        $dataFinal = array_merge($dataFinal, static::setActions($integrationDetails));

        return $dataFinal;
    }

    public function formatDealFieldMap($data, $fieldMap, $integrationDetails)
    {
        $dataFinal = [];

        foreach ($fieldMap as $value) {
            $triggerValue = $value->formField;
            $actionValue = $value->hubspotField;
            if ($triggerValue === 'custom') {
                $dataFinal[$actionValue] = Common::replaceFieldWithValue($value->customValue, $data);
            } elseif (!\is_null($data[$triggerValue])) {
                if (!\is_array($data[$triggerValue]) && strtotime($data[$triggerValue])) {
                    $formated = strtotime($data[$triggerValue]);
                    $dataFinal[$actionValue] = $formated;
                } else {
                    $dataFinal[$actionValue] = \is_array($data[$triggerValue]) ? implode(';', $data[$triggerValue]) : $data[$triggerValue];
                }
            }
        }

        if (!empty($integrationDetails->pipeline)) {
            $dataFinal['pipeline'] = $integrationDetails->pipeline;
        }
        if (!empty($integrationDetails->stage)) {
            $dataFinal['dealstage'] = $integrationDetails->stage;
        }

        $dataForAssosciations = [];

        if (isset($integrationDetails->company)) {
            $companyIds = explode(',', $integrationDetails->company);
            $dataForAssosciations['associatedCompanyIds'] = $companyIds;
        }

        if (isset($integrationDetails->contact)) {
            $contactIds = explode(',', $integrationDetails->contact);
            $dataForAssosciations['associatedVids'] = $contactIds;
        }

        $finalData = [];
        $finalData['properties'] = array_merge($dataFinal, static::setActions($integrationDetails));

        if (!empty($dataForAssosciations)) {
            $finalData['associations'] = $dataForAssosciations;
        }

        return $finalData;
    }

    public function formatTicketFieldMap($data, $fieldMap, $integrationDetails)
    {
        $dataFinal = [];

        foreach ($fieldMap as $value) {
            $triggerValue = $value->formField;
            $actionValue = $value->hubspotField;
            if ($triggerValue === 'custom') {
                $dataFinal[$actionValue] = Common::replaceFieldWithValue($value->customValue, $data);
            } elseif (!\is_null($data[$triggerValue])) {
                $dataFinal[$actionValue] = \is_array($data[$triggerValue]) ? implode(';', $data[$triggerValue]) : $data[$triggerValue];
            }
        }

        if (!empty($integrationDetails->pipeline)) {
            $dataFinal['hs_pipeline'] = $integrationDetails->pipeline;
        }
        if (!empty($integrationDetails->stage)) {
            $dataFinal['hs_pipeline_stage'] = $integrationDetails->stage;
        }

        $dataFinal = array_merge($dataFinal, static::setActions($integrationDetails));

        return $dataFinal;
    }

    public function executeRecordApi($integId, $integrationDetails, $fieldValues, $fieldMap)
    {
        $actionName = $integrationDetails->actionName;
        $type = '';
        $typeName = '';

        if ($actionName === 'contact' || $actionName === 'company') {
            $finalData = $this->generateReqDataFromFieldMap($fieldValues, $fieldMap, $integrationDetails);
            $type = $actionName;
            $typeName = "{$actionName}-add";
            $apiResponse = $this->insertContact($finalData, $actionName, $typeName);
        } elseif ($actionName === 'deal') {
            $finalData = $this->formatDealFieldMap($fieldValues, $fieldMap, $integrationDetails);
            $apiResponse = $this->insertDeal($finalData);
            $type = 'deal';
            $typeName = 'deal-add';
        } elseif ($actionName === 'ticket') {
            $finalData = $this->formatTicketFieldMap($fieldValues, $fieldMap, $integrationDetails);
            $apiResponse = $this->insertTicket($finalData);
            $type = 'ticket';
            $typeName = 'ticket-add';
        }

        if (!isset($apiResponse->properties)) {
            LogHandler::save($integId, wp_json_encode(['type' => $type, 'type_name' => $typeName]), 'error', wp_json_encode($apiResponse));
        } else {
            LogHandler::save($integId, wp_json_encode(['type' => $type, 'type_name' => $typeName]), 'success', wp_json_encode($apiResponse));
        }

        return $apiResponse;
    }

    private static function setActions($integrationDetails)
    {
        $actions = [];

        if (isset($integrationDetails->contact_owner)) {
            $actions['hubspot_owner_id'] = $integrationDetails->contact_owner;
        }

        if ($integrationDetails->actionName === 'contact' || $integrationDetails->actionName === 'company') {
            if (isset($integrationDetails->lead_status)) {
                $actions['hs_lead_status'] = $integrationDetails->lead_status;
            }

            if (isset($integrationDetails->lifecycle_stage)) {
                $actions['lifecyclestage'] = $integrationDetails->lifecycle_stage;
            }
        }

        if ($integrationDetails->actionName === 'company') {
            if (isset($integrationDetails->company_type)) {
                $actions['type'] = $integrationDetails->company_type;
            }

            if (isset($integrationDetails->industry)) {
                $actions['industry'] = $integrationDetails->industry;
            }
        }

        if ($integrationDetails->actionName === 'company') {
            if (isset($integrationDetails->deal_type)) {
                $dealType = $integrationDetails->deal_type;
                $actions['dealtype'] = $dealType;
            }

            if (isset($integrationDetails->priority)) {
                $priority = $integrationDetails->priority;
                $actions['hs_priority'] = $priority;
            }
        }

        if ($integrationDetails->actionName === 'ticket' && isset($integrationDetails->priority)) {
            $priority = $integrationDetails->priority;
            if ($priority == 'low') {
                $priority = 'LOW';
            } elseif ($priority == 'medium') {
                $priority = 'MEDIUM';
            } else {
                $priority = 'HIGH';
            }
            $actions['hs_ticket_priority'] = $priority;
        }

        return $actions;
    }
}
