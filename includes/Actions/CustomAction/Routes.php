<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\CustomAction\CustomActionController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('checking_function_validity', [CustomActionController::class, 'functionValidateHandler']);
