<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\SureCart\SureCartController;

Hooks::addAction('surecart/purchase_created', [SureCartController::class, 'surecart_purchase_product'], 10, 1);
Hooks::addAction('surecart/purchase_revoked', [SureCartController::class, 'surecart_purchase_revoked'], 10, 1);
Hooks::addAction('surecart/purchase_invoked', [SureCartController::class, 'surecart_purchase_unrevoked'], 10, 1);
