<?php


if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\BTCBI\Http\Services\Triggers\Spectra\SpectraController;

Route::post('spectra/get', [SpectraController::class, 'getTestData']);
Route::post('spectra/test/remove', [SpectraController::class, 'removeTestData']);
