<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\BuddyBoss\BuddyBossController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('buddyBoss_authorize', [BuddyBossController::class, 'authorizeBuddyBoss']);
Route::post('fetch_all_group', [BuddyBossController::class, 'getAllGroups']);
Route::post('fetch_all_user', [BuddyBossController::class, 'getAllUser']);
Route::post('fetch_all_forum', [BuddyBossController::class, 'getAllForums']);
Route::post('fetch_all_topic', [BuddyBossController::class, 'getAllTopics']);
