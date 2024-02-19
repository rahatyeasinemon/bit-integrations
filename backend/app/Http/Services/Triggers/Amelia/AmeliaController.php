<?php

namespace BitCode\BTCBI\Http\Services\Triggers\Amelia;

use BitCode\BTCBI\Http\Services\Triggers\Webhook\WebhookController;

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
