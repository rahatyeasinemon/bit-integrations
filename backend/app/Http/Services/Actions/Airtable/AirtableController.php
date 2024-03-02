<?php

/**
 * Airtable Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\Airtable;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for Airtable integration
 */
class AirtableController
{
    protected $_defaultHeader;

    public function authentication($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->auth_token)) {
            return Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiKey      = $fieldsRequestParams->auth_token;
        $apiEndpoint = 'https://api.airtable.com/v0/meta/bases';
        $header      = [
            'Authorization' => "Bearer {$apiKey}"
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $header);

        if (isset($response->bases)) {
            foreach ($response->bases as $base) {
                if ($base->permissionLevel === 'create') {
                    $bases[] = [
                        'id'   => $base->id,
                        'name' => $base->name
                    ];
                }
            }
            return Response::success($bases);
        } else {
            return Response::error('Authentication failed', 400);
        }
    }

    public function getAllTables($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->auth_token) || empty($fieldsRequestParams->baseId)) {
            return Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiKey      = $fieldsRequestParams->auth_token;
        $baseId      = $fieldsRequestParams->baseId;
        $apiEndpoint = "https://api.airtable.com/v0/meta/bases/{$baseId}/tables/";
        $header      = [
            'Authorization' => "Bearer {$apiKey}"
        ];

        $response = Http::request($apiEndpoint, 'Get', null, $header);

        if (isset($response->tables)) {
            foreach ($response->tables as $table) {
                $tables[] = [
                    'id'   => $table->id,
                    'name' => $table->name,
                ];
            }
            return Response::success($tables);
        } else {
            return Response::error('Tables fetching failed', 400);
        }
    }

    public function getAllFields($fieldsRequestParams)
    {
        if (empty($fieldsRequestParams->auth_token) || empty($fieldsRequestParams->baseId) || empty($fieldsRequestParams->tableId)) {
            return Response::error(__('Requested parameter is empty', 'bit-integrations'), 400);
        }

        $apiKey      = $fieldsRequestParams->auth_token;
        $baseId      = $fieldsRequestParams->baseId;
        $tableId     = $fieldsRequestParams->tableId;
        $apiEndpoint = "https://api.airtable.com/v0/meta/bases/{$baseId}/tables/";
        $header      = [
            'Authorization' => "Bearer {$apiKey}"
        ];

        $response      = Http::request($apiEndpoint, 'Get', null, $header);
        $acceptedTypes = ['singleLineText', 'multilineText', 'singleSelect', 'multipleSelects', 'multipleAttachments', 'date', 'phoneNumber', 'email', 'url', 'number', 'currency', 'percent', 'duration', 'rating', 'barcode'];

        if (isset($response->tables)) {
            foreach ($response->tables as $table) {
                if ($table->id === $tableId) {
                    foreach ($table->fields as $field) {
                        if (in_array($field->type, $acceptedTypes)) {
                            $fields[] = [
                                'key'      => $field->id . '{btcbi}' . $field->type,
                                'label'    => $field->name,
                                'required' => false
                            ];
                        }
                    }
                }
            }
            return Response::success($fields);
        } else {
            return Response::error('Table fields fetching failed', 400);
        }
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId            = $integrationData->id;
        $auth_token         = $integrationDetails->auth_token;
        $fieldMap           = $integrationDetails->field_map;

        if (empty($fieldMap) || empty($auth_token)) {
            return new WP_Error('REQ_FIELD_EMPTY', __('fields are required for Airtable api', 'bit-integrations'));
        }

        $recordApiHelper     = new RecordApiHelper($integrationDetails, $integId);
        $airtableApiResponse = $recordApiHelper->execute($fieldValues, $fieldMap);

        if (is_wp_error($airtableApiResponse)) {
            return $airtableApiResponse;
        }
        return $airtableApiResponse;
    }
}
