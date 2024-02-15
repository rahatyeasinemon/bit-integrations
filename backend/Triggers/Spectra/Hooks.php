<?php

if (!defined('ABSPATH')) {
    exit;
}

use BitCode\BTCBI\Core\Util\Hooks;
use BitCode\BTCBI\Triggers\Spectra\SpectraController;

Hooks::add('uagb_form_success', [SpectraController::class, 'spectraHandler'], 10, PHP_INT_MAX);
