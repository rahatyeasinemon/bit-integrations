<?php

/**
 * WooCommerce Integration
 */

namespace BitCode\FI\Actions\WooCommerce;

use WP_Error;
use WC_Data_Store;
use BitCode\FI\Log\LogHandler;

class WooCommerceController
{
    private $_integrationID;

    public function __construct($integrationID)
    {
        $this->_integrationID = $integrationID;
    }

    public static function authorizeWC()
    {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        if (is_plugin_active('woocommerce/woocommerce.php')) {
            wp_send_json_success(true, 200);
        }

        wp_send_json_error(__('WooCommerce must be activated!', 'bit-integrations'));
    }

    public static function refreshFields($queryParams)
    {
        if (empty($queryParams->module)) {
            wp_send_json_error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }

        $metabox = WooCommerceMetaFields::metaBoxFields($queryParams->module);

        $uploadFields = [];

        if ($queryParams->module === 'product') {
            $productFields = WooCommerceMetaFields::getProductModuleFields($queryParams->module);
            $fields = $productFields['fields'];
            $uploadFields = $productFields['upload_fields'];
            $required = $productFields['required'];
        }

        if ($queryParams->module === 'customer') {
            $fields = [
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

            $required = ['user_login', 'user_email'];
        }

        if ($queryParams->module === 'order') {
            wp_send_json_success(WooCommerceMetaFields::getOrderModuleFields($queryParams->module), 200);
        }

        if ($queryParams->module === 'changestatus') {
            $fields = [
                'Order ID' => (object) [
                    'fieldKey'  => 'order_id',
                    'fieldName' => 'Order ID',
                    'required'  => true
                ],
                'Order Status' => (object) [
                    'fieldKey'  => 'order_status',
                    'fieldName' => 'Order Status',
                    'required'  => true
                ],
                'Customer Email' => (object) [
                    'fieldKey'  => 'email',
                    'fieldName' => 'Customer Email',
                    'required'  => true
                ],
                'From Date' => (object) [
                    'fieldKey'  => 'from_date',
                    'fieldName' => 'From Date',
                    'required'  => true
                ],
                'To Date' => (object) [
                    'fieldKey'  => 'to_date',
                    'fieldName' => 'To Date',
                    'required'  => true
                ],
                'N Days' => (object) [
                    'fieldKey'  => 'n_days',
                    'fieldName' => 'N Days',
                    'required'  => true
                ],
                'N Weeks' => (object) [
                    'fieldKey'  => 'n_weeks',
                    'fieldName' => 'N Weeks',
                    'required'  => true
                ],
                'N Months' => (object) [
                    'fieldKey'  => 'n_months',
                    'fieldName' => 'N Months',
                    'required'  => true
                ],

            ];
            $required = [];
            $response = [
                'fields'       => $fields,
                'uploadFields' => $uploadFields,
                'required'     => $required
            ];

            wp_send_json_success($response, 200);
        }

        uksort($fields, 'strnatcasecmp');
        uksort($uploadFields, 'strnatcasecmp');
        $fields = array_merge($fields, $metabox['meta_fields']);
        $response = [
            'fields'       => $fields,
            'uploadFields' => $uploadFields,
            'required'     => $required
        ];

        wp_send_json_success($response, 200);
    }

    public function searchProjects($queryParams)
    {
        include_once \dirname(WC_PLUGIN_FILE) . '/includes/class-wc-product-functions.php';
        $data_store = WC_Data_Store::load('product');
        $search_results = $data_store->search_products($queryParams->searchTxt);
        $products = [];
        foreach ($search_results as $res) {
            if ($res) {
                $product = wc_get_product($res);
                $products[] = [
                    'id'   => $res,
                    'name' => $product->get_name(),
                ];
            }
        }

        wp_send_json_success($products, 200);
    }

    public static function allSubscriptionsProducts()
    {
        global $wpdb;
        $allSubscriptions = $wpdb->get_results($wpdb->prepare("
        	SELECT posts.ID, posts.post_title FROM {$wpdb->posts} as posts
        	LEFT JOIN {$wpdb->term_relationships} as rel ON (posts.ID = rel.object_id)
        	WHERE rel.term_taxonomy_id IN (SELECT term_id FROM {$wpdb->terms} WHERE slug IN ('subscription','variable-subscription'))
        	AND posts.post_type = 'product'
        	AND posts.post_status = 'publish'
        	UNION ALL
        	SELECT ID, post_title FROM {$wpdb->posts}
        	WHERE post_type = 'shop_subscription'
        	AND post_status = 'publish'
        	ORDER BY post_title
        "));

        $subscriptions[] = [
            'product_id'   => 'any',
            'product_name' => 'Any product',
        ];

        foreach ($allSubscriptions as $key => $val) {
            $subscriptions[] = [
                'product_id'   => $val->ID,
                'product_name' => $val->post_title,
            ];
        }
        wp_send_json_success($subscriptions, 200);
    }

    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $module = $integrationDetails->module;
        $fieldMap = $integrationDetails->{$module}->field_map;
        $uploadFieldMap = $integrationDetails->{$module}->upload_field_map;
        $required = $integrationDetails->default->fields->{$module}->required;

        if (
            empty($module)
        ) {
            $error = new WP_Error('REQ_FIELD_EMPTY', __('module and field map are required for woocommerce', 'bit-integrations'));
            LogHandler::save($this->_integrationID, 'record', 'validation', $error);

            return $error;
        }

        $recordApiHelper = new RecordApiHelper($this->_integrationID);

        $wcApiResponse = $recordApiHelper->execute(
            $module,
            $fieldValues,
            $fieldMap,
            $uploadFieldMap,
            $required,
            $integrationDetails
        );

        if (is_wp_error($wcApiResponse)) {
            return $wcApiResponse;
        }

        return $wcApiResponse;
    }
}
