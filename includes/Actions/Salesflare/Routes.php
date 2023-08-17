<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Actions\Salesflare\SalesflareController;
use BitCode\FI\Core\Util\Route;


Route::post('salesflare_authentication', [SalesflareController::class, 'authentication']);
