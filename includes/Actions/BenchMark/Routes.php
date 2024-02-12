<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\BenchMark\BenchMarkController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('benchMark_authorize', [BenchMarkController::class, 'benchMarkAuthorize']);
Route::post('benchMark_headers', [BenchMarkController::class, 'benchMarkHeaders']);
Route::post('benchMark_lists', [BenchMarkController::class, 'benchMarkLists']);
