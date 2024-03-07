<?php

namespace BitApps\BTCBI\Http\Services\Actions\SliceWp;

use BTCBI\Deps\BitApps\WPKit\Http\Response;
use WP_Error;

class SliceWpController
{
    public static function pluginActive($option = null)
    {
        if (is_plugin_active('slicewp/index.php')) {
            return $option === 'get_name' ? 'slicewp/index.php' : true;
        }
        return false;
    }

    public static function authorizeSliceWp()
    {
        if (self::pluginActive()) {
            return Response::success(true);
        }
        return Response::error(__('SliceWp affiliate must be activated!', 'bit-integrations'));
    }

    public function execute($integrationData, $fieldValues)
    {

        $integrationDetails = $integrationData->flow_details;
        $integId = $integrationData->id;
        $mainAction = $integrationDetails->mainAction;
        $fieldMap = $integrationDetails->field_map;
        if (
            empty($integId) ||
            empty($mainAction)
        ) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for sliceWp affiliate api', 'bit-integrations'));
        }
        $recordApiHelper = new RecordApiHelper($integrationDetails, $integId);
        $sliceWpApiResponse = $recordApiHelper->execute(
            $mainAction,
            $fieldValues,
            $fieldMap,
            $integrationDetails
        );

        if (is_wp_error($sliceWpApiResponse)) {
            return $sliceWpApiResponse;
        }
        return $sliceWpApiResponse;
    }
}
