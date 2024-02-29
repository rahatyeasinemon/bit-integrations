<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Sendy\SendyController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('sendy_authorize', [SendyController::class, 'sendyAuthorize']);
Route::post('get_all_brands', [SendyController::class, 'getAllBrands']);
Route::post('get_all_lists_from_sendy', [SendyController::class, 'getAllLists']);
// Route::post('mautic_get_fields', [ MauticController::class, 'getAllFields']);
// Route::post('mautic_get_tags', [ MauticController::class, 'getAllTags']);
