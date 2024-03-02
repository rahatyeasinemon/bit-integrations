<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\CustomAction\CustomActionController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('checking_function_validity', [CustomActionController::class, 'functionValidateHandler']);
