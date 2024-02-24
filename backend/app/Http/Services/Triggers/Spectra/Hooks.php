<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Spectra\SpectraController;

Hooks::addAction('uagb_form_success', [SpectraController::class, 'spectraHandler'], 10, PHP_INT_MAX);
