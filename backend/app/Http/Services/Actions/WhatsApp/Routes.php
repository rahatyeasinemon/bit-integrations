<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Http\Services\Actions\Trello\TrelloController;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

Route::post('trello_fetch_all_board', [ TrelloController::class, 'fetchAllBoards']);
Route::post('trello_fetch_all_list_Individual_board', [TrelloController::class, 'fetchAllLists']);
