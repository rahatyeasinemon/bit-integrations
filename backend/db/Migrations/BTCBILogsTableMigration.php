<?php

use BitApps\BTCBI\Config;
use BTCBI\Deps\BitApps\WPDatabase\Blueprint;
use BTCBI\Deps\BitApps\WPDatabase\Connection;
use BTCBI\Deps\BitApps\WPKit\Migration\Migration;
use BTCBI\Deps\BitApps\WPDatabase\Schema;

if (! defined('ABSPATH')) {
    exit;
}

final class BTCBILogsTableMigration extends Migration
{
    public function up(): void
    {
        Schema::withPrefix(Connection::wpPrefix() . Config::VAR_PREFIX)->create(
            'flow_logs',
            function (Blueprint $table): void {
                $table->id();
                $table->bigint('flow_id')->unsigned()->foreign('flows', 'id')->onDelete()->cascade();
                $table->bigint('job_id');
                $table->string('api_type');
                $table->string('response_type');
                $table->longtext('response_obj');
                $table->timestamps();
            }
        );
    }

    public function down(): void
    {
        Schema::drop('flow_logs');
    }
}
