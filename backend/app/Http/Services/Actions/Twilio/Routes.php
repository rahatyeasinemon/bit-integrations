<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Twilio\TwilioController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

//Twilio
Route::post('twilio_authorization', [TwilioController::class, 'checkAuthorization']);
