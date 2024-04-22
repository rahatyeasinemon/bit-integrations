<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI_PRO\Actions\Trello\TrelloController;
use BitApps\BTCBI_PRO\Core\Util\Route;

Route::post('trello_fetch_all_board', [ TrelloController::class, 'fetchAllBoards']);
Route::post('trello_fetch_all_list_Individual_board', [TrelloController::class, 'fetchAllLists']);
