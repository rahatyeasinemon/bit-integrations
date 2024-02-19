<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Triggers\MetaBox\MetaBoxController;

Route::get('metabox/get', [MetaBoxController::class, 'getAll']);
Route::post('metabox/get/form', [MetaBoxController::class, 'get_a_form']);
