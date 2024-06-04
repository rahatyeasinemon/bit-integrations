<?php

namespace BitCode\FI\controller;

use BitCode\FI\Config;

final class BtcbiAnalyticsController
{
    public function filterTrackingData($additional_data)
    {
        global $wpdb;
        $flowTable = $wpdb->prefix . Config::VAR_PREFIX . 'flow';
        $logTable = $wpdb->prefix . Config::VAR_PREFIX . 'log';

        $flow = $wpdb->get_results("
                    SELECT 
                        flow.name as ActionName, 
                        flow.triggered_entity as TriggerName, 
                        flow.status,
                        COUNT(log.flow_id) AS count
                    FROM 
                        {$flowTable} flow
                    LEFT JOIN 
                        {$logTable} log ON flow.id = log.flow_id
                    GROUP BY
                        log.flow_id
                ");

        $additional_data['flows'] = $flow;

        // error_log(print_r($additional_data['flows'], true));

        return $additional_data;
    }
}
