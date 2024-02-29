<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\OneHashCRM\OneHashCRMController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('onehashcrm_authentication', [OneHashCRMController::class, 'authentication']);
