<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\ConvertKit\ConvertKitController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('convertKit_authorize', [ConvertKitController::class, 'convertKitAuthorize']);
Route::post('convertKit_headers', [ConvertKitController::class, 'convertKitHeaders']);
Route::post('convertKit_forms', [ConvertKitController::class, 'convertKitForms']);
Route::post('convertKit_tags', [ConvertKitController::class, 'convertKitTags']);
