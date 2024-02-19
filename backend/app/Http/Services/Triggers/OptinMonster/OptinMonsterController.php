<?php

namespace BitCode\BTCBI\Http\Services\Triggers\OptinMonster;

use BitCode\BTCBI\Http\Services\Triggers\Webhook\WebhookController;

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
