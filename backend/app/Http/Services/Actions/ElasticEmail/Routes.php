<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\ElasticEmail\ElasticEmailController;
use BitCode\BTCBI\Util\Route;

Route::post('elasticemail_authorize', [ElasticEmailController::class, 'elasticEmailAuthorize']);
Route::get('get_all_lists', [ElasticEmailController::class, 'getAllLists']);
