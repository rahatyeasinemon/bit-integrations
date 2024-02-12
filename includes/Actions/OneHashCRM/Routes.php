<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\OneHashCRM\OneHashCRMController;
use BitCode\BTCBI\Core\Util\Route;


Route::post('onehashcrm_authentication', [OneHashCRMController::class, 'authentication']);
