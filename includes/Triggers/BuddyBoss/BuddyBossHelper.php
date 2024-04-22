<?php

namespace BitApps\BTCBI_PRO\Triggers\BuddyBoss;

class BuddyBossHelper
{
    public static function getBuddyBossProfileField()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'bp_xprofile_fields';
        $results = $wpdb->get_results("SELECT id, type , name FROM $table_name");
        return $results;
    }
}
