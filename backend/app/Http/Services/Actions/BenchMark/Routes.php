<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\BenchMark\BenchMarkController;
use BitApps\BTCBI\Util\Route;

Route::post('benchMark_authorize', [BenchMarkController::class, 'benchMarkAuthorize']);
Route::post('benchMark_headers', [BenchMarkController::class, 'benchMarkHeaders']);
Route::post('benchMark_lists', [BenchMarkController::class, 'benchMarkLists']);
