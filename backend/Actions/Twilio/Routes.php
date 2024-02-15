<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\Twilio\TwilioController;
use BitCode\BTCBI\Core\Util\Route;

    //Twilio
    Route::post('twilio_authorization', [TwilioController::class, 'checkAuthorization']);
