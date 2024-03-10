<?php
/***
 * If try to direct access  plugin folder it will Exit
 **/
if (!defined('ABSPATH')) {
    exit;
}

// use BitApps\BTCBI\Http\Controllers\PostController;

use BitApps\BTCBI\Http\Controllers\ConfigController;
use BitApps\BTCBI\Http\Controllers\UserController;
use BitApps\BTCBI\Http\Controllers\PostController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\BTCBI\Model\Flow;
use BitApps\BTCBI\Http\Services\Log\LogHandler;
use BitApps\BTCBI\Http\Services\Triggers\TriggerController;

Route::group(function () {
    Route::post('log/get', [LogHandler::class, 'get']);
    Route::post('log/delete', [LogHandler::class, 'delete']);

    Route::post('app/config', [ConfigController::class, 'updatedAppConfig']);
    Route::get('get/config', [ConfigController::class, 'getAppConfig']);

    Route::get('trigger/list', [TriggerController::class, 'triggerList']);

    Route::get('flow/list', [Flow::class, 'flowList']);
    Route::post('flow/get', [Flow::class, 'get']);
    Route::post('flow/save', [Flow::class, 'save']);
    Route::post('flow/update', [Flow::class, 'update']);
    Route::post('flow/delete', [Flow::class, 'delete']);
    Route::post('flow/bulk-delete', [Flow::class, 'bulkDelete']);
    Route::post('flow/toggleStatus', [Flow::class, 'toggle_status']);
    Route::post('flow/clone', [Flow::class, 'flowClone']);

    /* Controller */
    Route::post('customfield/list', [PostController::class, 'getCustomFields']);
    Route::get('pods/list', [PostController::class, 'getPodsPostType']);
    Route::post('pods/fields', [PostController::class, 'getPodsField']);
    Route::post('user/list', [UserController::class, 'getWpUsers']);
    Route::get('role/list', [UserController::class, 'getUserRoles']);
    Route::get('page/list', [PostController::class, 'getPages']);
    Route::post('post-types/list', [PostController::class, 'getPostTypes']);
    /*Controller */
})->middleware('nonce');
