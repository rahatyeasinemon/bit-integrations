<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\Slack\SlackController;
use BitCode\BTCBI\Util\Route;

//Slack
Route::post('slack_authorization_and_fetch_channels', [SlackController::class, 'checkAuthorizationAndFetchChannels']);
