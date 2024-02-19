<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\Twilio\TwilioController;
use BitCode\BTCBI\Util\Route;

//Twilio
Route::post('twilio_authorization', [TwilioController::class, 'checkAuthorization']);
