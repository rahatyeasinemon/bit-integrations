<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Util\Route;
use BitCode\BTCBI\Triggers\Spectra\SpectraController;

Route::post('spectra/get', [SpectraController::class, 'getTestData']);
Route::post('spectra/test/remove', [SpectraController::class, 'removeTestData']);
