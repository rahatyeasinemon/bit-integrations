<?php

namespace BitCode\BTCBI\Triggers\Amelia;

use BitCode\BTCBI\Triggers\Webhook\WebhookController;

final class AmeliaController extends WebhookController
{
    public static function info()
    {
        return [
            'name' => 'Amelia',
            'title' => 'Get callback data through an URL',
            'type' => 'webhook',
            'is_active' => true,
        ];
    }
}
