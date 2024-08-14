<?php

/**
 * Dokan Record Api
 */

namespace BitCode\FI\Actions\Dokan;

use BitCode\FI\Core\Util\Common;
use BitCode\FI\Core\Util\Helper;
use BitCode\FI\Log\LogHandler;

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

    public function createVendor($finalData, $actions)
    {
        if (empty($finalData['email']) || empty($finalData['user_login']) || empty($finalData['store_name'])) {
            return ['success' => false, 'message' => 'Required field email, username or store name is empty!', 'code' => 400];
        }

        $data = $this->formatVendorUpsertData($finalData, $actions, 'createVendor');

        $store = dokan()->vendor->create($data);

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

        $storeId = dokan()->vendor->update($selectedVendor, $data);

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
        $euFieldsKey = ['dokan_company_name', 'dokan_company_id_number', 'dokan_vat_number', 'dokan_bank_name', 'dokan_bank_iban'];
        $data = [];

        foreach ($finalData as $key => $value) {
            if ($key === 'payment_paypal_email') {
                $data['payment']['paypal'] = ['email' => $value];
            } elseif (\in_array($key, $paymentBankKeys)) {
                $data['payment']['bank'][str_replace('payment_bank_', '', $key)] = $value;
            } elseif (\in_array($key, $addressKeys)) {
                $data['address'][$key] = $value;
            } elseif (\in_array($key, $euFieldsKey)) {
                $data[str_replace('dokan_', '', $key)] = $value;
            } else {
                $data[$key] = $value;
            }
        }

        if (!empty($actions) && Helper::proActionFeatExists('Dokan', 'vendorCreateActions')) {
            $filterResponse = apply_filters('btcbi_dokan_vendor_crud_actions', $module, $actions);

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

        $vendor = dokan()->vendor->delete($vendorId, $reassignId);

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
            'status'  => dokan()->withdraw->get_status_code('pending'),
            'ip'      => dokan_get_client_ip(),
            'notes'   => isset($finalData['note']) ? $finalData['note'] : ''
        ];

        $hasPendingRequest = dokan()->withdraw->has_pending_request($selectedVendor);

        if ($hasPendingRequest) {
            return ['success' => false, 'message' => 'Vendor already have pending withdraw request(s)!', 'code' => 400];
        }

        $validateRequest = dokan()->withdraw->is_valid_approval_request($args);

        if (is_wp_error($validateRequest)) {
            return ['success' => false, 'message' => $validateRequest->get_error_message(), 'code' => $validateRequest->get_error_code()];
        }

        $insertWithdraw = dokan()->withdraw->insert_withdraw($args);

        if ($insertWithdraw) {
            return ['success' => true, 'message' => 'Withdraw request inserted successfully. (Vendor ID: ' . $args['user_id'] . 'Amount : ' . $args['amount'] . 'Method: ' . $args['method'] . ')'];
        }

        return ['success' => false, 'message' => 'Something went wrong!', 'code' => 400];
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
            $actionValue = $value->dokanField;
            if ($triggerValue === 'custom') {
                $dataFinal[$actionValue] = Common::replaceFieldWithValue($value->customValue, $data);
            } elseif (!\is_null($data[$triggerValue])) {
                $dataFinal[$actionValue] = $data[$triggerValue];
            }
        }

        return $dataFinal;
    }

    public function execute($fieldValues, $fieldMap, $selectedTask, $actions, $selectedVendor, $selectedPaymentMethod)
    {
        if (isset($fieldMap[0]) && empty($fieldMap[0]->formField)) {
            $finalData = [];
        } else {
            $finalData = $this->generateReqDataFromFieldMap($fieldValues, $fieldMap);
        }

        $type = $typeName = '';

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
