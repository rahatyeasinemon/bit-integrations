<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\Discord\DiscordController;
use BitCode\BTCBI\Core\Util\Route;

//Discord
Route::post('handle_authorize', [DiscordController::class, 'handleAuthorize']);
Route::post('discord_fetch_servers', [DiscordController::class, 'fetchServers']);
Route::post('discord_fetch_channels', [DiscordController::class, 'fetchChannels']);
