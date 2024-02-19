<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Http\Services\Triggers\EssentialBlocks\EssentialBlocksController;

Route::post('essential_blocks/get', [EssentialBlocksController::class, 'getTestData']);
Route::post('essential_blocks/test/remove', [EssentialBlocksController::class, 'removeTestData']);
