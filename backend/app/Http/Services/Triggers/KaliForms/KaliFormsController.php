<?php

namespace BitApps\BTCBI\Http\Services\Triggers\KaliForms;

use BitApps\BTCBI\Http\Services\Triggers\Webhook\WebhookController;

final class KaliFormsController extends WebhookController
{
    public static function info()
    {
        return [
            'name' => 'Kali Forms',
            'title' => 'Get callback data through an URL',
            'type' => 'webhook',
            'is_active' => true,
        ];
    }
}
