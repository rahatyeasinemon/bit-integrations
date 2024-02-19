<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\Groundhogg\GroundhoggController;
use BitCode\BTCBI\Util\Route;

Route::post('groundhogg_authorization_and_fetch_contacts', [ GroundhoggController::class, 'fetchAllContacts' ]);
Route::post('groundhogg_fetch_all_tags', [GroundhoggController::class, 'groundhoggFetchAllTags']);
