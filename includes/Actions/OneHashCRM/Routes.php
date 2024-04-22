<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\OneHashCRM\OneHashCRMController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('onehashcrm_authentication', [OneHashCRMController::class, 'authentication']);
