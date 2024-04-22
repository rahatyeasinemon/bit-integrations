<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\WishList\WishListController;
use BitApps\BTCBI_PRO\Core\Util\Route;

//WishList
Route::post('wishlist_authorization', [WishListController::class, 'checkAuthorization']);
Route::get('wishlist_get_all_levels', [WishListController::class, 'getAllLevels']);
Route::get('wishlist_get_all_members', [WishListController::class, 'getAllMembers']);
