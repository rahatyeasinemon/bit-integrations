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

    public static function billingFields()
    {
        return [
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
        ];
    }

    public static function shippingFields()
    {
        return [
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

    public static function productBasicFields(){
        return [
            'Product Name' => (object) [
                'fieldKey'  => 'post_title',
                'fieldName' => 'Product Name',
                'required'  => true
            ],
            'Product Description' => (object) [
                'fieldKey'  => 'post_content',
                'fieldName' => 'Product Description'
            ],
            'Product Short Description' => (object) [
                'fieldKey'  => 'post_excerpt',
                'fieldName' => 'Product Short Description'
            ],
            'Post Date' => (object) [
                'fieldKey'  => 'post_date',
                'fieldName' => 'Post Date'
            ],
            'Post Date GMT' => (object) [
                'fieldKey'  => 'post_date_gmt',
                'fieldName' => 'Post Date GMT'
            ],
            'Product Status' => (object) [
                'fieldKey'  => 'post_status',
                'fieldName' => 'Product Status'
            ],
            'Product Tag' => (object) [
                'fieldKey'  => 'tags_input',
                'fieldName' => 'Product Tag'
            ],
            'Product Category' => (object) [
                'fieldKey'  => 'post_category',
                'fieldName' => 'Product Category'
            ],
            'Catalog Visibility' => (object) [
                'fieldKey'  => '_visibility',
                'fieldName' => 'Catalog Visibility'
            ],
            'Featured Product' => (object) [
                'fieldKey'  => '_featured',
                'fieldName' => 'Featured Product'
            ],
            'Post Password' => (object) [
                'fieldKey'  => 'post_password',
                'fieldName' => 'Post Password'
            ],
            'Regular Price' => (object) [
                'fieldKey'  => '_regular_price',
                'fieldName' => 'Regular Price'
            ],
            'Sale Price' => (object) [
                'fieldKey'  => '_sale_price',
                'fieldName' => 'Sale Price'
            ],
            'Sale Price From Date' => (object) [
                'fieldKey'  => '_sale_price_dates_from',
                'fieldName' => 'Sale Price From Date'
            ],
            'Sale Price To Date' => (object) [
                'fieldKey'  => '_sale_price_dates_to',
                'fieldName' => 'Sale Price To Date'
            ],
            'SKU' => (object) [
                'fieldKey'  => '_sku',
                'fieldName' => 'SKU',
                'required'  => true,
            ],
            'Manage Stock' => (object) [
                'fieldKey'  => '_manage_stock',
                'fieldName' => 'Manage Stock'
            ],
            'Stock Quantity' => (object) [
                'fieldKey'  => '_stock',
                'fieldName' => 'Stock Quantity'
            ],
            'Allow Backorders' => (object) [
                'fieldKey'  => '_backorders',
                'fieldName' => 'Allow Backorders'
            ],
            'Low Stock Threshold' => (object) [
                'fieldKey'  => '_low_stock_amount',
                'fieldName' => 'Low Stock Threshold'
            ],
            'Stock Status' => (object) [
                'fieldKey'  => '_stock_status',
                'fieldName' => 'Stock Status'
            ],
            'Sold Individually' => (object) [
                'fieldKey'  => '_sold_individually',
                'fieldName' => 'Sold Individually'
            ],
            'Weight' => (object) [
                'fieldKey'  => '_weight',
                'fieldName' => 'Weight'
            ],
            'Length' => (object) [
                'fieldKey'  => '_length',
                'fieldName' => 'Length'
            ],
            'Width' => (object) [
                'fieldKey'  => '_width',
                'fieldName' => 'Width'
            ],
            'Height' => (object) [
                'fieldKey'  => '_height',
                'fieldName' => 'Height'
            ],
            'Purchase Note' => (object) [
                'fieldKey'  => '_purchase_note',
                'fieldName' => 'Purchase Note'
            ],
            'Menu Order' => (object) [
                'fieldKey'  => 'menu_order',
                'fieldName' => 'Menu Order'
            ],
            'Enable Reviews' => (object) [
                'fieldKey'  => 'comment_status',
                'fieldName' => 'Enable Reviews'
            ],
            'Virtual' => (object) [
                'fieldKey'  => '_virtual',
                'fieldName' => 'Virtual'
            ],
            'Downloadable' => (object) [
                'fieldKey'  => '_downloadable',
                'fieldName' => 'Downloadable'
            ],
            'Download Limit' => (object) [
                'fieldKey'  => '_download_limit',
                'fieldName' => 'Download Limit'
            ],
            'Download Expiry' => (object) [
                'fieldKey'  => '_download_expiry',
                'fieldName' => 'Download Expiry'
            ],
            'Product Type' => (object) [
                'fieldKey'  => 'product_type',
                'fieldName' => 'Product Type'
            ],
            'Product URL' => (object) [
                'fieldKey'  => '_product_url',
                'fieldName' => 'Product URL'
            ],
            'Button Text' => (object) [
                'fieldKey'  => '_button_text',
                'fieldName' => 'Button Text'
            ],
        ];
    }

    public static function productUploadFields(){
        return [
            'Product Image' => (object) [
                'fieldKey'  => 'product_image',
                'fieldName' => 'Product Image'
            ],
            'Product Gallery' => (object) [
                'fieldKey'  => 'product_gallery',
                'fieldName' => 'Product Gallery'
            ],
            'Downloadable Files' => (object) [
                'fieldKey'  => 'downloadable_files',
                'fieldName' => 'Downloadable Files'
            ],
        ];
    }
}
