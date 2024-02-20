<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\CustomAction\CustomActionController;
use BitApps\BTCBI\Util\Route;

Route::post('checking_function_validity', [CustomActionController::class, 'functionValidateHandler']);
