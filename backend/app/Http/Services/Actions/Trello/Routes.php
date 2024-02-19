<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Http\Services\Actions\Trello\TrelloController;
use BitCode\BTCBI\Util\Route;

Route::post('trello_fetch_all_board', [ TrelloController::class, 'fetchAllBoards']);
Route::post('trello_fetch_all_list_Individual_board', [TrelloController::class, 'fetchAllLists']);
