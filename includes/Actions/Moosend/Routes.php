<?php
if (!defined('ABSPATH')) {
  exit;
}

use BitCode\BTCBI\Actions\Moosend\MoosendController;
use BitCode\BTCBI\Core\Util\Route;

Route::post('moosend_handle_authorize', [MoosendController::class, 'handleAuthorize']);
