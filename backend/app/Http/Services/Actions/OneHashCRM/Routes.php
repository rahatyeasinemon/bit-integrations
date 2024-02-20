<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\OneHashCRM\OneHashCRMController;
use BitApps\BTCBI\Util\Route;

Route::post('onehashcrm_authentication', [OneHashCRMController::class, 'authentication']);
