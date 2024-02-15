<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Route;
use BitCode\BTCBI\Actions\FreshSales\FreshSalesController;

Route::post('FreshSales_authorization', [FreshSalesController::class, 'authorization']);
Route::post('FreshSales_refresh_fields', [FreshSalesController::class, 'getFields']);
Route::post('FreshSales_fetch_meta_data', [FreshSalesController::class, 'getMetaData']);
