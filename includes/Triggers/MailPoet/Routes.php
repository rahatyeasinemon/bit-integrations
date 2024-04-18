<?php
if (!defined('ABSPATH')) {
    exit;
}
use BitCode\FI\Core\Util\Route;
use BitCode\FI\Triggers\MailPoet\MailPoetController;

Route::get('mailPoet/get', [MailPoetController::class, 'getAll']);
Route::post('mailPoet/get/form', [MailPoetController::class, 'get_a_form']);