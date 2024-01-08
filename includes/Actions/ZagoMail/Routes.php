<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Actions\ZagoMail\ZagoMailController;
use BitCode\FI\Core\Util\Route;

Route::post('zagoMail_authorize', [ZagoMailController::class, 'zagoMailAuthorize']);
Route::post('zagoMail_headers', [ZagoMailController::class, 'zagoMailHeaders']);
Route::post('zagoMail_forms', [ZagoMailController::class, 'zagoMailForms']);
Route::post('zagoMail_tags', [ZagoMailController::class, 'zagoMailTags']);
