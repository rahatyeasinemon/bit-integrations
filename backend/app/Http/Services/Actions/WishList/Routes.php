<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\WishList\WishListController;
use BitCode\BTCBI\Util\Route;

//WishList
Route::post('wishlist_authorization', [WishListController::class, 'checkAuthorization']);
Route::get('wishlist_get_all_levels', [WishListController::class, 'getAllLevels']);
Route::get('wishlist_get_all_members', [WishListController::class, 'getAllMembers']);
