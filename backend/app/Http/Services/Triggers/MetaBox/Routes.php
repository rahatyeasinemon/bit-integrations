<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Route;
use BitApps\BTCBI\Http\Services\Triggers\MetaBox\MetaBoxController;

Route::get('metabox/get', [MetaBoxController::class, 'getAll']);
Route::post('metabox/get/form', [MetaBoxController::class, 'get_a_form']);
