<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Actions\Discord\DiscordController;
use BitCode\FI\Core\Util\Route;

//Discord
Route::post('discord_authorization_and_fetch_servers', [DiscordController::class, 'checkAuthorizationAndFetchServers']);
Route::post('discord_authorization_and_fetch_channels', [DiscordController::class, 'checkAuthorizationAndFetchChannels']);
