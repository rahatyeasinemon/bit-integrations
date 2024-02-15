<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Actions\Trello\TrelloController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('trello_fetch_all_board', [ TrelloController::class, 'fetchAllBoards']);
Route::post('trello_fetch_all_list_Individual_board',[TrelloController::class, 'fetchAllLists']);
