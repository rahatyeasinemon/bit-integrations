<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Groundhogg\GroundhoggController;
use BitApps\BTCBI\Util\Route;

Route::post('groundhogg_authorization_and_fetch_contacts', [ GroundhoggController::class, 'fetchAllContacts' ]);
Route::post('groundhogg_fetch_all_tags', [GroundhoggController::class, 'groundhoggFetchAllTags']);
