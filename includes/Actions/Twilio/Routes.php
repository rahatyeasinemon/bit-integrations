<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\Twilio\TwilioController;
use BitApps\BTCBI_PRO\Core\Util\Route;

//Twilio
Route::post('twilio_authorization', [TwilioController::class, 'checkAuthorization']);
