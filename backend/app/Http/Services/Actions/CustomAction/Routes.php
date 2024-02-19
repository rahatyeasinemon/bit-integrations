<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\CustomAction\CustomActionController;
use BitCode\BTCBI\Util\Route;

Route::post('checking_function_validity', [CustomActionController::class, 'functionValidateHandler']);
