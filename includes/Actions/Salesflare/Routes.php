<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Actions\Salesflare\SalesflareController;
use BitCode\FI\Core\Util\Route;


Route::post('onehashcrm_authentication', [SalesflareController::class, 'authentication']);