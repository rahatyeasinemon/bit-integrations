<?php

namespace BitCode\FI\Core\Util;

// All Pro Action Features
final class AllProActionFeat
{
    public static $features = [
        'MailChimp' => [
            ['feat_name' => 'addRemoveTag', 'class' => '\BitApps\BTCBI_PRO\Actions\MailChimp\MailChimpRecordHelper', 'pro_init_v' => '2.0.9']
        ],
        'FreshSales' => [
            ['feat_name' => 'upsertRecord', 'class' => '\BitApps\BTCBI_PRO\Actions\FreshSales\FreshSalesRecordApiHelper', 'pro_init_v' => '2.1.1']
        ],
        'WC' => [
            ['feat_name' => 'getFlexibleCheckoutFields', 'class' => '\BitApps\BTCBI_PRO\Triggers\WC\WCHelperPro', 'pro_init_v' => '2.1.2'],
            ['feat_name' => 'getFlexibleCheckoutFieldsValue', 'class' => '\BitApps\BTCBI_PRO\Triggers\WC\WCHelperPro', 'pro_init_v' => '2.1.2']
        ],
        'WhatsApp' => [
            ['feat_name' => 'textMessages', 'class' => '\BitApps\BTCBI_PRO\Triggers\WhatsApp\WhatsAppHelperPro', 'pro_init_v' => '2.1.6'],
        ]
    ];
}
