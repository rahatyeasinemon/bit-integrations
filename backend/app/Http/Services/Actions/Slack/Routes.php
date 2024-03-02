<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Slack\SlackController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

//Slack
Route::post('slack_authorization_and_fetch_channels', [SlackController::class, 'checkAuthorizationAndFetchChannels']);
