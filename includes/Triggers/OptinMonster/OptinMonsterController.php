<?php

namespace BitApps\BTCBI_PRO\Triggers\OptinMonster;

use BitApps\BTCBI_PRO\Triggers\Webhook\WebhookController;

final class OptinMonsterController extends WebhookController
{
    public static function info()
    {
        return [
            'name' => 'OptinMonster',
            'title' => 'Get callback data through an URL',
            'type' => 'webhook',
            'is_active' => true,
        ];
    }
}
