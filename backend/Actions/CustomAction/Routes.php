<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\CustomAction\CustomActionController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('checking_function_validity', [CustomActionController::class, 'functionValidateHandler']);
