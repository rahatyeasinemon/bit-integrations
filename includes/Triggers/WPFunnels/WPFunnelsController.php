<?php

namespace BitApps\BTCBI_PRO\Triggers\WPFunnels;

use BitApps\BTCBI_PRO\Triggers\Webhook\WebhookController;

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
