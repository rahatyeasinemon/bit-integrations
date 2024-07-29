<?php

/**
 * WooCommerce Fields.
 */

namespace BitCode\FI\Actions\WooCommerce;

use BitCode\FI\Core\Util\Helper;

class WooCommerceMetaFields
{
    public static function metaBoxFields($module)
    {
        $fileTypes = [
            'image',
            'image_upload',
            'file_advanced',
            'file_upload',
            'single_image',
            'file',
            'image_advanced',
            'video'
        ];

        $metaBoxFields = [];
        $metaBoxUploadFields = [];

        if (\function_exists('rwmb_meta')) {
            if ($module === 'customer') {
                $field_registry = rwmb_get_registry('field');
                $meta_boxes = $field_registry->get_by_object_type($object_type = 'user');
                $metaFields = isset($meta_boxes['user']) && \is_array($meta_boxes['user']) ? array_values($meta_boxes['user']) : [];
            } else {
                $metaFields = array_values(rwmb_get_object_fields($module));
            }
            foreach ($metaFields as $index => $field) {
                if (!\in_array($field['type'], $fileTypes)) {
                    $metaBoxFields[$index] = (object) [
                        'fieldKey'  => $field['id'],
                        'fieldName' => 'Metabox Field - ' . $field['name'],
                        'required'  => $field['required'],
                    ];
                } else {
                    $metaBoxUploadFields[$index] = (object) [
                        'fieldKey'  => $field['id'],
                        'fieldName' => 'Metabox Field - ' . $field['name'],
                        'required'  => $field['required'],
                    ];
                }
            }
        }

        return ['meta_fields' => $metaBoxFields, 'upload_fields' => $metaBoxUploadFields];
    }

    public static function getProductModuleFields($module)
    {
        $metaBox = static::metaBoxFields($module);

        return [
            'fields'        => array_merge(WooCommerceStaticFields::productBasicFields(), $metaBox['meta_fields']),
            'upload_fields' => array_merge(WooCommerceStaticFields::productUploadFields(), $metaBox['upload_fields']),
            'required'      => ['post_title', '_sku']
        ];
    }

    public static function getOrderModuleFields($module)
    {
        $metaBoxOrder = static::metaBoxFields($module);
        $metaBoxCustomer = static::metaBoxFields('customer');

        $checkoutFields = WooCommerceStaticFields::checkoutBasicFields();
        $customerFields = WooCommerceStaticFields::customerFields();
        $billingFields = WooCommerceStaticFields::billingFields();
        $ShippingFields = WooCommerceStaticFields::shippingFields();
        $fieldLineItems = WooCommerceStaticFields::lineItemsFields();
        $flexibleCheckoutFields = static::getFlexibleCheckoutFields();

        $fieldsOrder = array_merge($checkoutFields, $billingFields, $ShippingFields, $flexibleCheckoutFields, $metaBoxOrder['meta_fields']);
        $fieldsCustomer = array_merge($customerFields, $billingFields, $ShippingFields, $flexibleCheckoutFields, $metaBoxCustomer['meta_fields']);

        uksort($fieldsOrder, 'strnatcasecmp');
        uksort($fieldsCustomer, 'strnatcasecmp');
        uksort($fieldLineItems, 'strnatcasecmp');

        $requiredCustomerFields = ['user_login', 'user_email'];
        $requiredLineItemFields = ['sku', 'quantity'];

        $responseOrder = [
            'fields'       => $fieldsOrder,
            'uploadFields' => [],
            'required'     => []
        ];
        $responseCustomer = [
            'fields'       => $fieldsCustomer,
            'uploadFields' => [],
            'required'     => $requiredCustomerFields
        ];

        $responseLine = [
            'fields'       => $fieldLineItems,
            'uploadFields' => [],
            'required'     => $requiredLineItemFields
        ];

        return [$responseOrder, $responseCustomer, $responseLine];
    }

    private static function getFlexibleCheckoutFields()
    {
        if (Helper::proActionFeatExists('WC', 'getFlexibleCheckoutFields')) {
            $checkoutFields = [];
            $fields = apply_filters('btcbi_woocommerce_flexible_checkout_fields', []);

            foreach ($fields as $field) {
                $checkoutFields[$field->fieldName] = (object) [
                    'fieldKey'  => $field->fieldKey,
                    'fieldName' => $field->fieldName
                ];
            }

            return $checkoutFields;
        }

        return [];
    }
}
