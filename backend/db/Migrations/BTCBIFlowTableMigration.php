<?php

use BitApps\BTCBI\Config;
use BTCBI\Deps\BitApps\WPDatabase\Blueprint;
use BTCBI\Deps\BitApps\WPDatabase\Connection;
use BTCBI\Deps\BitApps\WPDatabase\Schema;
use BTCBI\Deps\BitApps\WPKit\Migration\Migration;

if (!defined('ABSPATH')) {
    exit;
}

final class BTCBIFlowTableMigration extends Migration
{
    public function up(): void
    {
        Schema::withPrefix(Connection::wpPrefix() . Config::VAR_PREFIX)->create(
            'flows',
            function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('triggered_entity');
                $table->string('triggered_entity_id');
                $table->longtext('flow_details');
                $table->boolean('status');
                $table->bigint('user_id');
                $table->int('user_ip');
                $table->timestamps();
            }
        );
    }

    public function down(): void
    {
        Schema::drop('flows');
    }
}
