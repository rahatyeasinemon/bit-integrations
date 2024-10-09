<?php

/**
 * ConstantContact    Record Api
 */

namespace BitCode\FI\Actions\ConstantContact;

use BitCode\FI\Log\LogHandler;
use BitCode\FI\Core\Util\Common;
use BitCode\FI\Core\Util\HttpHelper;

/**
 * Provide functionality for Record insert, upsert
 */
class RecordApiHelper
{
    private $_integrationDetails;

    private $_integrationID;

    private $_defaultHeader;

    private $baseUrl = 'https://api.cc.email/v3/';

    public function __construct($integrationDetails, $integId)
    {
        $this->_integrationDetails = $integrationDetails;
        $this->_integrationID = $integId;
        $this->_defaultHeader = [
            'Authorization' => "Bearer {$this->_integrationDetails->tokenDetails->access_token}",
            'content-type'  => 'application/json'
        ];
    }

    public function generateReqDataFromFieldMap($data, $fieldMap)
    {
        $dataFinal = [];
        foreach ($fieldMap as $value) {
            $triggerValue = $value->formField;
            $actionValue = $value->constantContactFormField;

            if ($triggerValue === 'custom') {
                $dataFinal[$actionValue] = Common::replaceFieldWithValue($value->customValue, $data);
            } elseif (!\is_null($data[$triggerValue])) {
                $dataFinal[$actionValue] = $data[$triggerValue];
            }
        }

        return $dataFinal;
    }

    public function execute($listIds, $tagIds, $sourceType, $fieldValues, $fieldMap, $addressFields, $phoneFields, $addressType, $phoneType, $update)
    {
        $finalData = $this->generateReqDataFromFieldMap($fieldValues, $fieldMap);

        if (empty($finalData['email_address'])) {
            return ['success' => false, 'message' => __('Required field Email is empty', 'bit-integrations'), 'code' => 400];
        }

        $this->mapFields($addressFields, $finalData, $fieldValues, 'street_addresses', $addressType, 'constantContactAddressField');
        $this->mapFields($phoneFields, $finalData, $fieldValues, 'phone_numbers', $phoneType, 'constantContactPhoneField');

        $contact = $this->existContact($finalData['email_address']);

        if ($contact && !$update) {
            LogHandler::save($this->_integrationID, wp_json_encode(['source_type' => 'contact', 'type_name' => 'add-contact']), 'error', __('Email already exists', 'bit-integrations'));

            return $contact;
        }

        $apiResponse = $contact && $update
            ? $this->updateContact($contact, $listIds, $tagIds, $sourceType, $finalData)
            : $this->addContact($listIds, $tagIds, $sourceType, $finalData);

        $this->logApiResponse($apiResponse, $update, $contact);

        return $apiResponse;
    }

    private function updateContact($contact, $listIds, $tagIds, $sourceType, $finalData)
    {
        return $this->sendContactRequest('contacts/' . $contact->id, $listIds, $tagIds, $sourceType, $finalData, 'update_source');
    }

    private function addContact($listIds, $tagIds, $sourceType, $finalData)
    {
        return $this->sendContactRequest('contacts', $listIds, $tagIds, $sourceType, $finalData, 'create_source');
    }

    private function sendContactRequest($endpoint, $listIds, $tagIds, $sourceType, $finalData, $sourceKey)
    {
        $requestParams = [
            'email_address'    => (object) ['address' => $finalData['email_address']],
            $sourceKey         => $sourceType,
            'list_memberships' => $this->splitValues($listIds),
            'taggings'         => $this->splitValues($tagIds),
        ];

        $apiEndpoint = $this->baseUrl . $endpoint;
        $requestParams = $this->prepareCustomFields($finalData, $requestParams);
        $method = $sourceKey === 'create_source' ? 'post' : 'put';

        return HttpHelper::$method($apiEndpoint, wp_json_encode((object) $requestParams), $this->_defaultHeader);
    }

    private function splitValues($values)
    {
        return !empty($values) ? explode(',', $values) : [];
    }

    private function prepareCustomFields($data, $requestParams)
    {
        $customFields = [];
        foreach ($data as $key => $value) {
            if ($key !== 'email_address') {
                if (str_contains($key, 'custom-')) {
                    $customFields[] = [
                        'custom_field_id' => str_replace('custom-', '', $key),
                        'value'           => $value,
                    ];
                } else {
                    $requestParams[$key] = $value;
                }
            }
        }

        $requestParams['custom_fields'] = $customFields;

        return $requestParams;
    }

    private function logApiResponse($apiResponse, $update, $contactId = null)
    {
        $type = $update && $contactId ? 'update-contact' : 'add-contact';

        if (is_wp_error($apiResponse) || empty($apiResponse->contact_id) || isset($apiResponse->error_key)) {
            $logLevel = 'error';
        } else {
            $logLevel = 'success';
        }

        LogHandler::save($this->_integrationID, wp_json_encode(['source_type' => 'contact', 'type_name' => $type]), $logLevel, wp_json_encode($apiResponse));
    }

    private function mapFields($fields, &$finalData, $fieldValues, $key, $type, $formFieldKey)
    {
        if (!empty($fields)) {
            $mappedFields = [];
            foreach ($fields as $field) {
                $mappedFields[$field->{$formFieldKey}] = $field->formField === 'custom'
                    ? Common::replaceFieldWithValue($field->customValue, $fieldValues)
                    : ($fieldValues[$field->formField] ?? null);
            }
            $mappedFields['kind'] = $type;
            $finalData[$key] = [$mappedFields];
        }
    }

    // private function updateContact(
    //     $contactId,
    //     $listIds,
    //     $tagIds,
    //     $source_type,
    //     $finalData
    // ) {
    //     $apiEndpoints = $this->baseUrl . 'contacts/' . $contactId;
    //     $splitListIds = [];
    //     $splitTagIds = [];

    //     if (!empty($listIds)) {
    //         $splitListIds = explode(',', $listIds);
    //     }

    //     if (!empty($tagIds)) {
    //         $splitTagIds = explode(',', $tagIds);
    //     }
    //     if (empty($finalData['email_address'])) {
    //         return ['success' => false, 'message' => __('Required field Email is empty', 'bit-integrations'), 'code' => 400];
    //     }

    //     $requestParams = [
    //         'email_address' => (object) [
    //             'address' => $finalData['email_address'],
    //         ],
    //         'update_source'    => $source_type,
    //         'list_memberships' => $splitListIds,
    //         'taggings'         => $splitTagIds
    //     ];

    //     $customFields = [];
    //     foreach ($finalData as $key => $value) {
    //         if ($key !== 'email_address') {
    //             if (str_contains($key, 'custom-')) {
    //                 $replacedStr = str_replace('custom-', '', $key);
    //                 $customFields[] = [
    //                     'custom_field_id' => $replacedStr,
    //                     'value'           => $value
    //                 ];
    //             } else {
    //                 $requestParams[$key] = $value;
    //             }
    //         }
    //     }
    //     $requestParams['custom_fields'] = $customFields;

    //     return HttpHelper::put($apiEndpoints, wp_json_encode((object) $requestParams), $this->_defaultHeader);
    // }

    // private function addContact(
    //     $listIds,
    //     $tagIds,
    //     $source_type,
    //     $finalData
    // ) {
    //     $apiEndpoints = $this->baseUrl . 'contacts';
    //     $splitListIds = [];
    //     $splitTagIds = [];

    //     if (!empty($listIds)) {
    //         $splitListIds = explode(',', $listIds);
    //     }

    //     if (!empty($tagIds)) {
    //         $splitTagIds = explode(',', $tagIds);
    //     }
    //     if (empty($finalData['email_address'])) {
    //         return ['success' => false, 'message' => __('Required field Email is empty', 'bit-integrations'), 'code' => 400];
    //     }

    //     $requestParams = [
    //         'email_address' => (object) [
    //             'address' => $finalData['email_address'],
    //         ],
    //         'create_source'    => $source_type,
    //         'list_memberships' => $splitListIds,
    //         'taggings'         => $splitTagIds
    //     ];

    //     $customFields = [];
    //     foreach ($finalData as $key => $value) {
    //         if ($key !== 'email_address') {
    //             if (str_contains($key, 'custom-')) {
    //                 $replacedStr = str_replace('custom-', '', $key);
    //                 $customFields[] = [
    //                     'custom_field_id' => $replacedStr,
    //                     'value'           => $value
    //                 ];
    //             } else {
    //                 $requestParams[$key] = $value;
    //             }
    //         }
    //     }

    //     $requestParams['custom_fields'] = $customFields;

    //     return HttpHelper::post($apiEndpoints, wp_json_encode((object) $requestParams), $this->_defaultHeader);
    // }

    private function existContact($email)
    {
        $apiEndpoints = $apiEndpoints = $this->baseUrl . 'contacts?email=' . $email;
        $apiResponse = HttpHelper::get($apiEndpoints, null, $this->_defaultHeader);

        if (is_wp_error($apiResponse) || empty($apiResponse->contacts) || isset($apiResponse->error_key) || (\gettype($apiResponse) === 'array' && $apiResponse[0]->error_key)) {
            return false;
        }

        return $apiResponse->contacts[0] ?? false;
    }
}
