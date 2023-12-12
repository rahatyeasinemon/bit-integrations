<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Actions\SystemIO\SystemIOController;
use BitCode\FI\Core\Util\Route;

Route::post('systemIO_authorize', [SystemIOController::class, 'systemIOAuthorize']);
Route::post('systemIO_headers', [SystemIOController::class, 'systemIOHeaders']);
Route::post('systemIO_forms', [SystemIOController::class, 'systemIOForms']);
Route::post('systemIO_tags', [SystemIOController::class, 'systemIOTags']);
