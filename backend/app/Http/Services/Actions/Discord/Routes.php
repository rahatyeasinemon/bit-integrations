<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\Discord\DiscordController;
use BitCode\BTCBI\Util\Route;

//Discord
Route::post('handle_authorize', [DiscordController::class, 'handleAuthorize']);
Route::post('discord_fetch_servers', [DiscordController::class, 'fetchServers']);
Route::post('discord_fetch_channels', [DiscordController::class, 'fetchChannels']);
