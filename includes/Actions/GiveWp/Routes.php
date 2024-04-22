<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\GiveWp\GiveWpController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('giveWp_authorize', [GiveWpController::class, 'authorizeGiveWp']);
