<?php

namespace BitApps\BTCBI\Http\Services\Triggers\OptinMonster;

use BitApps\BTCBI\Http\Services\Triggers\Webhook\WebhookController;

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
