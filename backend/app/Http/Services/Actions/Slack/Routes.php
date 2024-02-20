<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Slack\SlackController;
use BitApps\BTCBI\Util\Route;

//Slack
Route::post('slack_authorization_and_fetch_channels', [SlackController::class, 'checkAuthorizationAndFetchChannels']);
