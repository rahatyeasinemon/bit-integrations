<?php

namespace BitApps\BTCBI\Http\Services\Actions\Memberpress;

use BTCBI\Deps\BitApps\WPKit\Http\Response;
use WP_Error;
use MeprOptions;

class MemberpressController
{
    // private $_integrationID;

    // public function __construct($integrationID)
    // {
    //     $this->_integrationID = $integrationID;
    // }

    public static function pluginActive($option = null)
    {
        if (is_plugin_active('memberpress/memberpress.php')) {
            return $option === 'get_name' ? 'memberpress/memberpress.php' : true;
        } else {
            return false;
        }
    }

    public static function authorizeMemberpress()
    {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        if (self::pluginActive()) {
            return Response::success(true);
        }
        return Response::error(__('Memberpress must be activated!', 'bit-integrations'));
    }

    public function getAllMembership($label = null, $option_code = 'MPPRODUCT', $args = [])
    {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        if (self::pluginActive()) {
            $posts = get_posts([
                'post_type' => 'memberpressproduct',
                'posts_per_page' => 999,
                'post_status' => 'publish',
                'meta_query' => [
                    'relation' => 'OR',
                    [
                        'key' => '_mepr_product_period_type',
                        'value' => 'lifetime',
                        'compare' => '!=',
                    ],
                    [
                        'key' => '_mepr_product_period_type',
                        'value' => 'lifetime',
                        'compare' => '=',
                    ],
                ],
            ]);

            foreach ($posts as $post) {
                $allMembership[] = [
                    'membershipId' => $post->ID,
                    'membershipTitle' => $post->post_title,
                ];
            }
            return Response::success($allMembership);
        }
        return Response::error(__('Memberpress must be activated!', 'bit-integrations'));
    }

    public static function allPaymentGateway()
    {
        if (!self::pluginActive()) {
            return Response::error(__('Memberpress must be activated!', 'bit-integrations'));
        }
        $mepr_options = MeprOptions::fetch();


        $pms = array_keys($mepr_options->integrations);
        $initGateways[] = [
            "paymentId" => 'manual',
            "paymentTitle" => 'Manual',
          ];
        $gateways = [];

        if(!empty($pms) && is_array($pms)) {
            foreach ($pms as $pm_id) {
                $obj = $mepr_options->payment_method($pm_id);
                $gateways[] = [
                    'paymentId' => $obj->id,
                    'paymentTitle' => $obj->name,
                ];
            }
        }
        $finalGateways = array_merge($gateways, $initGateways);
        return Response::success($finalGateways);
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId = $integrationData->id;
        $mainAction = $integrationDetails->mainAction;
        $fieldMap = $integrationDetails->field_map;
        $selectedMembership = $integrationDetails->selectedMembership;
        if (
            empty($integId) ||
            empty($mainAction) || empty($selectedMembership)
        ) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for memberpress api', 'bit-integrations'));
        }
        $recordApiHelper = new RecordApiHelper($integrationDetails, $integId);
        $memberpressApiResponse = $recordApiHelper->execute(
            $mainAction,
            $fieldValues,
            $fieldMap,
            $integrationDetails
        );

        if (is_wp_error($memberpressApiResponse)) {
            return $memberpressApiResponse;
        }
        return $memberpressApiResponse;
    }
}
