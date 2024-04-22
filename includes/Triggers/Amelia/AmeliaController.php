<?php

namespace BitApps\BTCBI_PRO\Triggers\Amelia;

use BitApps\BTCBI_PRO\Triggers\Webhook\WebhookController;

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
