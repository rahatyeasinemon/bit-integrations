<?php

/**
 * JetEngine Record Api
 */

namespace BitCode\FI\Actions\JetEngine;

use BitCode\FI\Core\Util\Common;
use BitCode\FI\Core\Util\Helper;
use BitCode\FI\Log\LogHandler;
use WeDevs\JetEnginePro\Refund\Validator;

/**
 * Provide functionality for Record insert, update
 */
class RecordApiHelper
{
    private $_integrationID;

    public function __construct($integId)
    {
        $this->_integrationID = $integId;
    }

    public function createPostType($finalData, $createCPTSelectedOptions, $actions)
    {
        if (empty($finalData['name'])) {
            return ['success' => false, 'message' => 'Request parameters are empty!', 'code' => 400];
        }

        $finalData['slug'] = str_replace(' ', '-', strtolower($finalData['name']));

        if (Helper::proActionFeatExists('JetEngine', 'createPostTypeActions')) {
            $filterResponse = apply_filters('btcbi_jet_engine_create_post_type_actions', 'createPostType', $createCPTSelectedOptions, $actions);

            if ($filterResponse !== 'createPostType' && !empty($filterResponse)) {
                $finalData = array_merge($finalData, $filterResponse);
            }
        }

        jet_engine()->cpt->data->set_request($finalData);

        $postTypeId = jet_engine()->cpt->data->create_item(false);

        if (empty($postTypeId) || is_wp_error($postTypeId)) {
            return ['success' => false, 'message' => 'Failed to add create post type!', 'code' => 400];
        }

        return ['success' => true, 'message' => 'Post type created successfully.'];
    }

    public function createVendor($finalData, $actions)
    {
        if (empty($finalData['email']) || empty($finalData['user_login']) || empty($finalData['store_name'])) {
            return ['success' => false, 'message' => 'Required field email, username or store name is empty!', 'code' => 400];
        }

        $data = $this->formatVendorUpsertData($finalData, $actions, 'createVendor');

        $store = jetEngine()->vendor->create($data);

        if (is_wp_error($store)) {
            return ['success' => false, 'message' => $store->get_error_message(), 'code' => 400];
        }

        return ['success' => true, 'message' => 'Vendor created successfully.'];
    }

    public function updateVendor($finalData, $selectedVendor, $actions)
    {
        if (empty($selectedVendor)) {
            return ['success' => false, 'message' => 'Required field vendor is empty!', 'code' => 400];
        }

        $data = $this->formatVendorUpsertData($finalData, $actions, 'updateVendor');

        $storeId = jetEngine()->vendor->update($selectedVendor, $data);

        if (is_wp_error($storeId)) {
            return ['success' => false, 'message' => $storeId->get_error_message(), 'code' => 400];
        }

        return ['success' => true, 'message' => 'Vendor updated successfully.'];
    }

    public function formatVendorUpsertData($finalData, $actions, $module)
    {
        $paymentBankKeys = ['payment_bank_ac_name', 'payment_bank_ac_type', 'payment_bank_ac_number', 'payment_bank_bank_name', 'payment_bank_bank_addr',
            'payment_bank_routing_number', 'payment_bank_iban', 'payment_bank_swift'];

        $addressKeys = ['street_1', 'street_2', 'city', 'zip', 'state', 'country'];
        $euFieldsKey = ['jetEngine_company_name', 'jetEngine_company_id_number', 'jetEngine_vat_number', 'jetEngine_bank_name', 'jetEngine_bank_iban'];
        $data = [];

        foreach ($finalData as $key => $value) {
            if ($key === 'payment_paypal_email') {
                $data['payment']['paypal'] = ['email' => $value];
            } elseif (\in_array($key, $paymentBankKeys)) {
                $data['payment']['bank'][str_replace('payment_bank_', '', $key)] = $value;
            } elseif (\in_array($key, $addressKeys)) {
                $data['address'][$key] = $value;
            } elseif (\in_array($key, $euFieldsKey)) {
                $data[str_replace('jetEngine_', '', $key)] = $value;
            } else {
                $data[$key] = $value;
            }
        }

        if (!empty($actions) && Helper::proActionFeatExists('JetEngine', 'vendorCreateActions')) {
            $filterResponse = apply_filters('btcbi_jetEngine_vendor_crud_actions', $module, $actions);

            if ($filterResponse !== $module && !empty($filterResponse)) {
                $data = array_merge($data, $filterResponse);
            }
        }

        return $data;
    }

    public function deleteVendor($finalData, $selectedVendor)
    {
        if (empty($finalData['vendor_email']) && empty($selectedVendor)) {
            return ['success' => false, 'message' => 'Vendor email or id is required!', 'code' => 400];
        }

        if (!empty($selectedVendor)) {
            $vendorId = $selectedVendor;
        } else {
            $vendorId = self::getUserIdFromEmail($finalData['vendor_email']);
        }

        if (!empty($finalData['reassign_email'])) {
            $reassignId = self::getUserIdFromEmail($finalData['reassign_email']);
        } else {
            $reassignId = null;
        }

        if (empty($vendorId)) {
            return ['success' => false, 'message' => 'Vendor not found!', 'code' => 400];
        }

        $vendor = jetEngine()->vendor->delete($vendorId, $reassignId);

        if (!empty($vendor) && isset($vendor['id'])) {
            if ($vendor['id'] === 0) {
                return ['success' => false, 'message' => 'Vendor not found!', 'code' => 400];
            }

            $id = $vendor['id'];
            $vendorEmail = isset($vendor['email']) ? ' Email: ' . $vendor['email'] : '';

            return ['success' => true, 'message' => 'Vendor deleted successfully. (ID: ' . $id . $vendorEmail . ')'];
        }

        return ['success' => false, 'message' => 'Something went wrong!', 'code' => 400];
    }

    public function withdrawRequest($finalData, $selectedVendor, $selectedPaymentMethod)
    {
        if (empty($selectedVendor) || empty($selectedPaymentMethod) || empty($finalData['amount'])) {
            return ['success' => false, 'message' => 'Request parameters are empty!', 'code' => 400];
        }

        $args = [
            'method'  => $selectedPaymentMethod,
            'user_id' => $selectedVendor,
            'amount'  => $finalData['amount'],
            'status'  => jetEngine()->withdraw->get_status_code('pending'),
            'ip'      => jetEngine_get_client_ip(),
            'notes'   => isset($finalData['note']) ? $finalData['note'] : ''
        ];

        $hasPendingRequest = jetEngine()->withdraw->has_pending_request($selectedVendor);

        if ($hasPendingRequest) {
            return ['success' => false, 'message' => 'Vendor already have pending withdraw request(s)!', 'code' => 400];
        }

        $validateRequest = jetEngine()->withdraw->is_valid_approval_request($args);

        if (is_wp_error($validateRequest)) {
            return ['success' => false, 'message' => $validateRequest->get_error_message(), 'code' => $validateRequest->get_error_code()];
        }

        $insertWithdraw = jetEngine()->withdraw->insert_withdraw($args);

        if ($insertWithdraw) {
            return ['success' => true, 'message' => 'Withdraw request inserted successfully. (Vendor ID: ' . $args['user_id'] . 'Amount : ' . $args['amount'] . 'Method: ' . $args['method'] . ')'];
        }

        return ['success' => false, 'message' => 'Something went wrong!', 'code' => 400];
    }

    public function refundRequest($finalData)
    {
        if (!is_plugin_active('jetEngine-pro/jetEngine-pro.php')) {
            return [
                'success' => false,
                'message' => 'The JetEngine Pro plugin is not installed or activated.',
                'code'    => 400
            ];
        }

        if (empty($finalData['order_id']) || empty($finalData['refund_amount'])) {
            return ['success' => false, 'message' => 'Request parameters are empty!', 'code' => 400];
        }

        $args = [
            'order_id'        => $finalData['order_id'],
            'refund_amount'   => $finalData['refund_amount'],
            'refund_reason'   => isset($finalData['refund_reason']) ? $finalData['refund_reason'] : '',
            'item_tax_totals' => []
        ];

        $validateRefundAmount = Validator::validate_refund_amount($finalData['refund_amount'], $args);

        if (is_wp_error($validateRefundAmount)) {
            return ['success' => false, 'message' => $validateRefundAmount->get_error_message(), 'code' => $validateRefundAmount->get_error_code()];
        }

        $refund = jetEngine_pro()->refund->create($args);

        if (is_wp_error($refund)) {
            return ['success' => false, 'message' => $refund->get_error_message(), 'code' => $refund->get_error_code()];
        }

        $refundAmount = $refund->get_refund_amount();
        $orderId = $refund->get_order_id();

        return ['success' => true, 'message' => 'Refund request added successfully. (Order ID: ' . $orderId . ' Refund Amount : ' . $refundAmount . ')'];
    }

    public static function getUserIdFromEmail($email)
    {
        if (empty($email) || !is_email($email) || !email_exists($email)) {
            return false;
        }

        $get_user = get_user_by('email', $email);

        return $get_user->ID;
    }

    public function generateReqDataFromFieldMap($data, $fieldMap)
    {
        $dataFinal = [];
        foreach ($fieldMap as $value) {
            $triggerValue = $value->formField;
            $actionValue = $value->jetEngineField;
            if ($triggerValue === 'custom') {
                $dataFinal[$actionValue] = Common::replaceFieldWithValue($value->customValue, $data);
            } elseif (!\is_null($data[$triggerValue])) {
                $dataFinal[$actionValue] = $data[$triggerValue];
            }
        }

        return $dataFinal;
    }

    public function execute($fieldValues, $fieldMap, $selectedTask, $actions, $createCPTSelectedOptions)
    {
        if (isset($fieldMap[0]) && empty($fieldMap[0]->formField)) {
            $finalData = [];
        } else {
            $finalData = $this->generateReqDataFromFieldMap($fieldValues, $fieldMap);
        }

        $type = $typeName = '';

        if ($selectedTask === 'createPostType') {
            $response = $this->createPostType($finalData, $createCPTSelectedOptions, $actions);
        }

        if ($selectedTask === 'createVendor') {
            $response = $this->createVendor($finalData, $actions);
            $type = 'Vendor';
            $typeName = 'Create Vendor';
        } elseif ($selectedTask === 'updateVendor') {
            $response = $this->updateVendor($finalData, $selectedVendor, $actions);
            $type = 'Vendor';
            $typeName = 'Update Vendor';
        } elseif ($selectedTask === 'deleteVendor') {
            $response = $this->deleteVendor($finalData, $selectedVendor);
            $type = 'Vendor';
            $typeName = 'Delete Vendor';
        } elseif ($selectedTask === 'withdrawRequest') {
            $response = $this->withdrawRequest($finalData, $selectedVendor, $selectedPaymentMethod);
            $type = 'Withdraw';
            $typeName = 'Withdraw Request';
        } elseif ($selectedTask === 'refundRequest') {
            $response = $this->refundRequest($finalData);
            $type = 'Refund';
            $typeName = 'Refund Request';
        }

        if ($response['success']) {
            $res = ['message' => $response['message']];
            LogHandler::save($this->_integrationID, wp_json_encode(['type' => $type, 'type_name' => $typeName]), 'success', wp_json_encode($res));
        } else {
            LogHandler::save($this->_integrationID, wp_json_encode(['type' => $type, 'type_name' => $typeName]), 'error', wp_json_encode($response));
        }

        return $response;
    }
}
