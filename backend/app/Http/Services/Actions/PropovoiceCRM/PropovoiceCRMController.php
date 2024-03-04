<?php

namespace BitApps\BTCBI\Http\Services\Actions\PropovoiceCRM;

use WP_Error;
use BitApps\BTCBI\Util\IpTool;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BitApps\BTCBI\Http\Services\Actions\PropovoiceCRM\RecordApiHelper;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

class PropovoiceCRMController
{
    public static function pluginActive()
    {
        if(class_exists('Ndpv')) {
            return true;
        }
        return false;
    }

    public static function authorizePropovoiceCrm()
    {
        if (self::pluginActive()) {
            return Response::success(true);
        }
        return Response::error(__('Propovoice CRM must be activated!', 'bit-integrations'));
    }

    public static function leadTags()
    {
        global $wpdb;
        $tags = $wpdb->get_results("SELECT term_id, name FROM $wpdb->terms WHERE term_id IN (SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'ndpv_tag')");
        return Response::success($tags);
    }

    public static function leadLabel()
    {
        global $wpdb;
        $labels = $wpdb->get_results("SELECT term_id, name FROM $wpdb->terms WHERE term_id IN (SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'ndpv_lead_level')");
        return Response::success($labels);
    }


    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integrationId = $integrationData->id;
        $fieldMap = $integrationDetails->field_map;
        $mainAction = $integrationDetails->mainAction;

        if (
            empty($integrationDetails)
            || empty($mainAction)
            || empty($fieldMap)

        ) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for Propovoice CRM', 'bit-integrations'));
        }
        $recordApiHelper = new RecordApiHelper($integrationId);
        $propovoiceCrmApiResponse = $recordApiHelper->execute(
            $fieldValues,
            $fieldMap,
            $integrationDetails,
            $mainAction
        );

        if (is_wp_error($propovoiceCrmApiResponse)) {
            return $propovoiceCrmApiResponse;
        }
        return $propovoiceCrmApiResponse;
    }
}
