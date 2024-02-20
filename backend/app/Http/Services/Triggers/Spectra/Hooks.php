<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitApps\BTCBI\Util\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Spectra\SpectraController;

Hooks::add('uagb_form_success', [SpectraController::class, 'spectraHandler'], 10, PHP_INT_MAX);
