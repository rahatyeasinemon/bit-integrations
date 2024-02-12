<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Route;
use BitCode\BTCBI\Triggers\EssentialBlocks\EssentialBlocksController;

Route::post('essential_blocks/get', [EssentialBlocksController::class, 'getTestData']);
Route::post('essential_blocks/test/remove', [EssentialBlocksController::class, 'removeTestData']);
