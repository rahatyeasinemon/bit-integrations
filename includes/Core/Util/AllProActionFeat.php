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
        ]
    ];
}
