<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\OneHashCRM\OneHashCRMController;
use BitCode\BTCBI\Util\Route;

Route::post('onehashcrm_authentication', [OneHashCRMController::class, 'authentication']);
