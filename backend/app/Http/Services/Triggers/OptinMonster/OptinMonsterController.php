<?php

namespace BitCode\BTCBI\Triggers\OptinMonster;

use BitCode\BTCBI\Triggers\Webhook\WebhookController;

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
