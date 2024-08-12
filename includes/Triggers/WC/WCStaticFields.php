<?php

namespace BitCode\FI\Triggers\WC;

use BitCode\FI\Core\Util\Helper;

class WCStaticFields
{
    public static function getWCOrderFields($id)
    {
        $fields = array_merge(static::checkoutBasicFields(), static::getOrderACFFields(), static::getCheckoutCustomFields(), static::getFlexibleCheckoutFields());

        if (version_compare(WC_VERSION, '8.5.1', '>=')) {
            $fields = array_merge($fields, static::checkoutUpgradeFields());
        }

        if ($id == 10) {
            $fields = array_merge($fields, static::specificOrderProductFields());
        } elseif ($id == 17) {
            $fields = array_merge([
                'specified_product_by_category' => (object) [
                    'fieldKey'  => 'specified_product_by_category',
                    'fieldName' => 'Specified Product By Category'
                ],
            ], $fields);
        }

        return $fields;
    }

    private static function getOrderACFFields()
    {
        $fields = [];
        $acfFieldGroups = Helper::acfGetFieldGroups(['shop_order']);

        foreach ($acfFieldGroups as $group) {
            $acfFields = acf_get_fields($group['ID']);

            foreach ($acfFields as $field) {
                $fields[$field['label']] = (object) [
                    'fieldKey'  => $field['_name'],
                    'fieldName' => $field['label']
                ];
            }
        }

        return $fields;
    }

    private static function getCheckoutCustomFields()
    {
        $fields = [];
        $checkoutFields = WC()->checkout()->get_checkout_fields();

        foreach ($checkoutFields as $group) {
            foreach ($group as $field) {
                if (!empty($field['custom']) && $field['custom']) {
                    $fields[$field['name']] = (object) [
                        'fieldKey'  => $field['name'],
                        'fieldName' => $field['label']
                    ];
                }
            }
        }

        return $fields;
    }

    private static function getFlexibleCheckoutFields()
    {
        if (Helper::proActionFeatExists('WC', 'getFlexibleCheckoutFields')) {
            return apply_filters('btcbi_woocommerce_flexible_checkout_fields', []);
        }

        return [];
    }

    private static function checkoutBasicFields()
    {
        return [
            'Id' => (object) [
                'fieldKey'  => 'id',
                'fieldName' => 'Order Id'
            ],
            'Order key' => (object) [
                'fieldKey'  => 'order_key',
                'fieldName' => 'Order Key'
            ],
            'cart_tax' => (object) [
                'fieldKey'  => 'cart_tax',
                'fieldName' => 'Cart Tax'
            ],
            'Currency' => (object) [
                'fieldKey'  => 'currency',
                'fieldName' => 'Currency'
            ],
            'discount tax' => (object) [
                'fieldKey'  => 'discount_tax',
                'fieldName' => 'Discount Tax'
            ],
            'discount_to_display' => (object) [
                'fieldKey'  => 'discount_to_display',
                'fieldName' => 'Discount To Display'
            ],
            'discount total' => (object) [
                'fieldKey'  => 'discount_total',
                'fieldName' => 'Discount Total'
            ],
            'shipping_tax' => (object) [
                'fieldKey'  => 'shipping_tax',
                'fieldName' => 'Shipping Tax'
            ],
            'shipping total' => (object) [
                'fieldKey'  => 'shipping_total',
                'fieldName' => 'Shipping Total'
            ],
            'total_tax' => (object) [
                'fieldKey'  => 'total_tax',
                'fieldName' => 'Total Tax'
            ],
            'total' => (object) [
                'fieldKey'  => 'total',
                'fieldName' => 'Total'
            ],
            'total_refunded' => (object) [
                'fieldKey'  => 'total_refunded',
                'fieldName' => 'Total Refunded'
            ],
            'tax_refunded' => (object) [
                'fieldKey'  => 'tax_refunded',
                'fieldName' => 'Tax Refunded'
            ],
            'total_shipping_refunded' => (object) [
                'fieldKey'  => 'total_shipping_refunded',
                'fieldName' => 'Total Shipping Refunded'
            ],
            'total_qty_refunded' => (object) [
                'fieldKey'  => 'total_qty_refunded',
                'fieldName' => 'Total Qty Refunded'
            ],
            'remaining_refund_amount' => (object) [
                'fieldKey'  => 'remaining_refund_amount',
                'fieldName' => 'remaining_refund_amount'
            ],
            'Status' => (object) [
                'fieldKey'  => 'status',
                'fieldName' => 'Status'
            ],
            'shipping_method' => (object) [
                'fieldKey'  => 'shipping_method',
                'fieldName' => 'shipping method'
            ],
            'Created via' => (object) [
                'fieldKey'  => 'created_via',
                'fieldName' => 'Created Via'
            ],
            'Date created' => (object) [
                'fieldKey'  => 'date_created',
                'fieldName' => 'Date created'
            ],
            'date modified' => (object) [
                'fieldKey'  => 'date_modified',
                'fieldName' => 'Date Modified'
            ],
            'date completed' => (object) [
                'fieldKey'  => 'date_completed',
                'fieldName' => 'Date completed'
            ],
            'date paid' => (object) [
                'fieldKey'  => 'date_paid',
                'fieldName' => 'Date paid'
            ],

            'prices_include_tax' => (object) [
                'fieldKey'  => 'prices_include_tax',
                'fieldName' => 'Prices Include Tax'
            ],
            'customer_id' => (object) [
                'fieldKey'  => 'customer_id',
                'fieldName' => 'Customer Id'
            ],
            'Billing First Name' => (object) [
                'fieldKey'  => 'billing_first_name',
                'fieldName' => 'Billing First Name'
            ],
            'Billing Last Name' => (object) [
                'fieldKey'  => 'billing_last_name',
                'fieldName' => 'Billing Last Name'
            ],
            'Billing Company' => (object) [
                'fieldKey'  => 'billing_company',
                'fieldName' => 'Billing Company'
            ],
            'Billing Address 1' => (object) [
                'fieldKey'  => 'billing_address_1',
                'fieldName' => 'Billing Address 1'
            ],
            'Billing Address 2' => (object) [
                'fieldKey'  => 'billing_address_2',
                'fieldName' => 'Billing Address 2'
            ],
            'Billing City' => (object) [
                'fieldKey'  => 'billing_city',
                'fieldName' => 'Billing City'
            ],
            'Billing Post Code' => (object) [
                'fieldKey'  => 'billing_postcode',
                'fieldName' => 'Billing Post Code'
            ],
            'Billing Country' => (object) [
                'fieldKey'  => 'billing_country',
                'fieldName' => 'Billing Country'
            ],
            'Billing State' => (object) [
                'fieldKey'  => 'billing_state',
                'fieldName' => 'Billing State'
            ],
            'Billing Email' => (object) [
                'fieldKey'  => 'billing_email',
                'fieldName' => 'Billing Email'
            ],
            'Billing Phone' => (object) [
                'fieldKey'  => 'billing_phone',
                'fieldName' => 'Billing Phone'
            ],
            'Shipping First Name' => (object) [
                'fieldKey'  => 'shipping_first_name',
                'fieldName' => 'Shipping First Name'
            ],
            'Shipping Last Name' => (object) [
                'fieldKey'  => 'shipping_last_name',
                'fieldName' => 'Shipping Last Name'
            ],
            'Shipping Company' => (object) [
                'fieldKey'  => 'shipping_company',
                'fieldName' => 'Shipping Company'
            ],
            'Shipping Address 1' => (object) [
                'fieldKey'  => 'shipping_address_1',
                'fieldName' => 'Shipping Address 1'
            ],
            'Shipping Address 2' => (object) [
                'fieldKey'  => 'shipping_address_2',
                'fieldName' => 'Shipping Address 2'
            ],
            'Shipping City' => (object) [
                'fieldKey'  => 'shipping_city',
                'fieldName' => 'Shipping City'
            ],
            'Shipping Post Code' => (object) [
                'fieldKey'  => 'shipping_postcode',
                'fieldName' => 'Shipping Post Code'
            ],
            'Shipping Country' => (object) [
                'fieldKey'  => 'shipping_country',
                'fieldName' => 'Shipping Country'
            ],
            'Payment Method' => (object) [
                'fieldKey'  => 'payment_method',
                'fieldName' => 'Payment Method'
            ],
            'Payment Method Title' => (object) [
                'fieldKey'  => 'payment_method_title',
                'fieldName' => 'Payment Method Title'
            ],
            'Line Items' => (object) [
                'fieldKey'  => 'line_items',
                'fieldName' => 'Line Items'
            ],
            'Order Receive URl' => (object) [
                'fieldKey'  => 'order_received_url',
                'fieldName' => 'order_received_url'
            ],
            'Customer Note' => (object) [
                'fieldKey'  => 'customer_note',
                'fieldName' => 'Customer Note'
            ],
        ];
    }

    private static function checkoutUpgradeFields()
    {
        return [
            'Device Type' => (object) [
                'fieldKey'  => '_wc_order_attribution_device_type',
                'fieldName' => 'Device Type'
            ],
            'Referring source' => (object) [
                'fieldKey'  => '_wc_order_attribution_referrer',
                'fieldName' => 'Referring source'
            ],
            'Session Count' => (object) [
                'fieldKey'  => '_wc_order_attribution_session_count',
                'fieldName' => 'Session Count'
            ],
            'Session Entry' => (object) [
                'fieldKey'  => '_wc_order_attribution_session_entry',
                'fieldName' => 'Session Entry'
            ],
            'Session page views' => (object) [
                'fieldKey'  => '_wc_order_attribution_session_pages',
                'fieldName' => 'Session page views'
            ],
            'Session Start Time' => (object) [
                'fieldKey'  => '_wc_order_attribution_session_start_time',
                'fieldName' => 'Session Start Time'
            ],
            'Source Type' => (object) [
                'fieldKey'  => '_wc_order_attribution_source_type',
                'fieldName' => 'Source Type'
            ],
            'User Agent' => (object) [
                'fieldKey'  => '_wc_order_attribution_user_agent',
                'fieldName' => 'User Agent'
            ],
            'Origin' => (object) [
                'fieldKey'  => '_wc_order_attribution_utm_source',
                'fieldName' => 'Origin'
            ],
        ];
    }

    private static function specificOrderProductFields()
    {
        return [
            'product_id' => (object) [
                'fieldKey'  => 'product_id',
                'fieldName' => 'Product Id'
            ],
            'variation_id' => (object) [
                'fieldKey'  => 'variation_id',
                'fieldName' => 'Variation Id'
            ],
            'product_name' => (object) [
                'fieldKey'  => 'product_name',
                'fieldName' => 'Product Name'
            ],
            'quantity' => (object) [
                'fieldKey'  => 'quantity',
                'fieldName' => 'Quantity'
            ],
            'subtotal' => (object) [
                'fieldKey'  => 'subtotal',
                'fieldName' => 'Subtotal'
            ],
            'total' => (object) [
                'fieldKey'  => 'total',
                'fieldName' => 'Total'
            ],
            'subtotal_tax' => (object) [
                'fieldKey'  => 'subtotal_tax',
                'fieldName' => 'Subtotal Tax'
            ],
            'tax_class' => (object) [
                'fieldKey'  => 'tax_class',
                'fieldName' => 'Tax Class'
            ],
            'tax_status' => (object) [
                'fieldKey'  => 'tax_status',
                'fieldName' => 'Tax Status'
            ],
        ];
    }
}
