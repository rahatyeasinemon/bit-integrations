<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Twilio\TwilioController;
use BitApps\BTCBI\Util\Route;

//Twilio
Route::post('twilio_authorization', [TwilioController::class, 'checkAuthorization']);
