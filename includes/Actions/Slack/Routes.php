<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\Slack\SlackController;
use BitApps\BTCBI_PRO\Core\Util\Route;

//Slack
Route::post('slack_authorization_and_fetch_channels', [SlackController::class, 'checkAuthorizationAndFetchChannels']);
