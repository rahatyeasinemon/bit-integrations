<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\ElasticEmail\ElasticEmailController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('elasticemail_authorize', [ElasticEmailController::class, 'elasticEmailAuthorize']);
Route::get('get_all_lists', [ElasticEmailController::class, 'getAllLists']);
