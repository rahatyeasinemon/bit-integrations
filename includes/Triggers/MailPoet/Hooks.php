<?php
if (!defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Core\Util\Hooks;
use BitCode\FI\Triggers\MailPoet\MailPoetController;

Hooks::add('mailpoet_subscription_before_subscribe', [MailPoetController::class, 'handle_mailpoet_submit'], 10, 3);
