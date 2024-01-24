<?php


if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Core\Util\Route;
use BitCode\FI\Triggers\FormHook\FormHookController;

Route::post('form_hook/test', [FormHookController::class, 'getTestData']);
Route::post('form_hook/test/remove', [FormHookController::class, 'removeTestData']);
