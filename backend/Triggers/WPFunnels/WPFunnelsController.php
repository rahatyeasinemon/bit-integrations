<?php

namespace BitCode\BTCBI\Triggers\WPFunnels;

use BitCode\BTCBI\Triggers\Webhook\WebhookController;

final class WPFunnelsController extends WebhookController
{
    public static function info()
    {
        return [
            'name' => 'WPFunnels',
            'title' => 'Get callback data through an URL',
            'type' => 'webhook',
            'is_active' => true,
        ];
    }
}
