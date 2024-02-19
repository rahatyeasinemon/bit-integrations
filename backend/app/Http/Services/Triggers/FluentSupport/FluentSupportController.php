<?php

namespace BitCode\BTCBI\Http\Services\Triggers\FluentSupport;

use BitCode\BTCBI\Http\Services\Triggers\Webhook\WebhookController;

final class FluentSupportController extends WebhookController
{
    public static function info()
    {
        return [
            'name' => 'Fluent Support',
            'title' => 'Get callback data through an URL',
            'type' => 'webhook',
            'is_active' => true,
        ];
    }
}
