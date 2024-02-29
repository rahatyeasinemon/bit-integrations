<?php

/**
 * Fluent Support Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\FluentSupport;

use WP_Error;
use BitApps\BTCBI\Util\IpTool;
use FluentSupport\App\Models\Agent;
use FluentSupport\App\Models\MailBox;

use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BitApps\BTCBI\Http\Services\Actions\FluentSupport\RecordApiHelper;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

/**
 * Provide functionality for Fluent Support integration
 */
class FluentSupportController
{
    public function checkAuthorization()
    {
        if (!is_plugin_active('fluent-support/fluent-support.php')) {
            Response::error(
                __(
                    'Fluent Support Plugin is not active or not installed',
                    'bit-integrations'
                ),
                400
            );
        } else {
            return true;
        }
    }

    public function getAllSupportStaff($tokenRequestParams)
    {
        $supportStaff = Agent::get();

        if (is_wp_error($supportStaff)) {
            Response::error(
                empty($supportStaff->error) ? 'Unknown' : $supportStaff->error,
                400
            );
        }
        Response::success(is_string($supportStaff) ? json_decode($supportStaff) : $supportStaff);
    }

    public function getAllBusinessInboxes()
    {
        $businessInboxes = MailBox::all();

        if (is_wp_error($businessInboxes)) {
            Response::error(
                empty($businessInboxes->error) ? 'Unknown' : $businessInboxes->error,
                400
            );
        }
        Response::success(is_string($businessInboxes) ? json_decode($businessInboxes) : $businessInboxes);
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integrationId = $integrationData->id;
        $fieldMap = $integrationDetails->field_map;

        if (empty($integrationDetails)) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, required fields are empty', 'bit-integrations'));
        }
        $recordApiHelper = new RecordApiHelper($integrationId);
        $fluentSupportApiResponse = $recordApiHelper->execute(
            $fieldValues,
            $fieldMap,
            $integrationDetails
        );

        if (is_wp_error($fluentSupportApiResponse)) {
            return $fluentSupportApiResponse;
        }
        return $fluentSupportApiResponse;
    }
}
