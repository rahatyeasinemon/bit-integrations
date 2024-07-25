<?php

/**
 * WooCommerce Static Fields.
 */

namespace BitCode\FI\Actions\WooCommerce;

class WooCommerceStaticFields
{
    public static function checkoutBasicFields()
    {
        return [
            'customer_note' => (object) [
                'fieldKey'  => 'customer_note',
                'fieldName' => 'Customer Note'
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
            // Fixed Cart Items Coupon
            'coupon_code' => (object) [
                'fieldKey'  => 'coupon_code',
                'fieldName' => 'Coupon Code'
            ],
        ];
    }

    public static function customerFields()
    {
        return [
            'First Name' => (object) [
                'fieldKey'  => 'first_name',
                'fieldName' => 'First Name'
            ],
            'Last Name' => (object) [
                'fieldKey'  => 'last_name',
                'fieldName' => 'Last Name'
            ],
            'Email' => (object) [
                'fieldKey'  => 'user_email',
                'fieldName' => 'Email',
                'required'  => true
            ],
            'Username' => (object) [
                'fieldKey'  => 'user_login',
                'fieldName' => 'Username',
                'required'  => true
            ],
            'Password' => (object) [
                'fieldKey'  => 'user_pass',
                'fieldName' => 'Password'
            ],
            'Display Name' => (object) [
                'fieldKey'  => 'display_name',
                'fieldName' => 'Display Name'
            ],
            'Nickname' => (object) [
                'fieldKey'  => 'nickname',
                'fieldName' => 'Nickname'
            ],
            'Description' => (object) [
                'fieldKey'  => 'description',
                'fieldName' => 'Description'
            ],
            'Locale' => (object) [
                'fieldKey'  => 'locale',
                'fieldName' => 'Locale'
            ],
            'Website' => (object) [
                'fieldKey'  => 'user_url',
                'fieldName' => 'Website'
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
            'Shipping State' => (object) [
                'fieldKey'  => 'shipping_state',
                'fieldName' => 'Shipping State'
            ],
        ];
    }

    public static function lineItemsFields()
    {
        return [
            'Product Name' => (object) [
                'fieldKey'  => 'name',
                'fieldName' => 'Product Name',
                'required'  => true
            ],
            'Sku' => (object) [
                'fieldKey'  => 'sku',
                'fieldName' => 'Sku',
                'required'  => true
            ],
            'Quantity' => (object) [
                'fieldKey'  => 'quantity',
                'fieldName' => 'Quantity',
                'required'  => true
            ],
            'Price' => (object) [
                'fieldKey'  => 'price',
                'fieldName' => 'Price',
                'required'  => true
            ],
            'Tax Class' => (object) [
                'fieldKey'  => 'tax_class',
                'fieldName' => 'Tax Class'
            ],
            'Line Subtotal' => (object) [
                'fieldKey'  => 'subtotal',
                'fieldName' => 'Line Subtotal'
            ],
            'Line Subtotal Tax' => (object) [
                'fieldKey'  => 'line_subtotal_tax',
                'fieldName' => 'Line Subtotal Tax'
            ],
            'Line Total' => (object) [
                'fieldKey'  => 'total',
                'fieldName' => 'Line Total'
            ],
        ];
    }
}
