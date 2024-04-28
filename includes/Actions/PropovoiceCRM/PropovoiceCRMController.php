<?php

namespace BitCode\FI\Actions\PropovoiceCRM;

use WP_Error;
use BitCode\FI\Core\Util\IpTool;
use BitCode\FI\Core\Util\HttpHelper;
use BitCode\FI\Actions\PropovoiceCRM\RecordApiHelper;

class PropovoiceCRMController
{
    public static function pluginActive()
    {
        if (class_exists('Ndpv')) {
            return true;
        }
        return false;
    }

    public static function authorizePropovoiceCrm()
    {
        if (self::pluginActive()) {
            wp_send_json_success(true, 200);
        }
        wp_send_json_error(__('Propovoice CRM must be activated!', 'bit-integrations'));
    }

    public static function leadTags()
    {
        global $wpdb;
        $tags = $wpdb->get_results($wpdb->prepare("SELECT term_id, name FROM %s WHERE term_id IN (SELECT term_taxonomy_id FROM %s WHERE taxonomy = 'ndpv_tag')", $wpdb->terms, $wpdb->term_taxonomy));
        wp_send_json_success($tags, 200);
    }

    public static function leadLabel()
    {
        global $wpdb;
        $labels = $wpdb->get_results($wpdb->prepare("SELECT term_id, name FROM %s WHERE term_id IN (SELECT term_taxonomy_id FROM %s WHERE taxonomy = 'ndpv_lead_level')", $wpdb->terms, $wpdb->term_taxonomy));
        wp_send_json_success($labels, 200);
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
