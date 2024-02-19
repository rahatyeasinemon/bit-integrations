<?php

namespace BitCode\BTCBI\Http\Services\Triggers\BitAssist;

use BitCode\BTCBI\Http\Services\Triggers\Webhook\WebhookController;

final class BitAssistController extends WebhookController
{
    public static function info()
    {
        return [
            'name' => 'Bit Assist',
            'title' => 'Get callback data through an URL',
            'type' => 'webhook',
            'is_active' => true,
        ];
    }
}
