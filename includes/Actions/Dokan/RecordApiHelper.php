<?php

/**
 * Dokan Record Api
 */

namespace BitCode\FI\Actions\Dokan;

use BitCode\FI\Core\Util\Common;
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

        $paymentBankKeys = ['payment_bank_ac_name', 'payment_bank_ac_type', 'payment_bank_ac_number', 'payment_bank_bank_name', 'payment_bank_bank_addr',
            'payment_bank_routing_number', 'payment_bank_iban', 'payment_bank_swift'];

        $addressKeys = ['street_1', 'street_2', 'city', 'zip', 'state', 'country'];
        $euFieldsKey = ['dokan_company_name', 'dokan_company_id_number', 'dokan_vat_number', 'dokan_bank_name', 'dokan_bank_iban'];
        $data = [];

        error_log(print_r(['final data' => $finalData], true));

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

        if (!empty($actions)) {
            if (isset($actions['notifyVendor'])) {
                $data['notify_vendor'] = true;
            } else {
                $data['notify_vendor'] = false;
            }

            if (isset($actions['enableSelling'])) {
                $data['enabled'] = true;
            }

            if (isset($actions['publishProduct'])) {
                $data['trusted'] = true;
            }

            if (isset($actions['featureVendor'])) {
                $data['featured'] = true;
            }
        } else {
            $data['notify_vendor'] = false;
        }

        $store = dokan()->vendor->create($data);

        if (is_wp_error($store)) {
            return ['success' => false, 'message' => $store->get_error_message(), 'code' => 400];
        }

        error_log(print_r(['data' => $data], true));

        return ['success' => true, 'message' => 'Vendor created successfully.'];
    }

    public function setUserReputation($finalData, $selectedReputation)
    {
        if (empty($finalData['email']) || empty($selectedReputation)) {
            return ['success' => false, 'message' => 'Required field email or reputation is empty!', 'code' => 400];
        }

        $userId = self::getUserIdFromEmail($finalData['email']);

        if (!$userId) {
            return ['success' => false, 'message' => 'The user does not exist on your site, or the email is invalid!', 'code' => 400];
        }

        $points = WPF()->member->rating($selectedReputation, 'points');
        $data = ['custom_points' => $points];

        WPF()->member->update_profile_fields($userId, $data, false);
        WPF()->member->reset($userId);

        return ['success' => true, 'message' => 'User reputation changed.'];
    }

    public function addToGroup($finalData, $selectedGroup)
    {
        if (empty($finalData['email']) || empty($selectedGroup)) {
            return ['success' => false, 'message' => 'Required field email or group is empty!', 'code' => 400];
        }

        $userId = self::getUserIdFromEmail($finalData['email']);

        if (!$userId) {
            return ['success' => false, 'message' => 'The user does not exist on your site, or the email is invalid!', 'code' => 400];
        }

        WPF()->usergroup->set_users_groupid([$selectedGroup => [$userId]]);

        return ['success' => true, 'message' => 'User group changed.'];
    }

    public function removeFromGroup($finalData, $selectedGroup)
    {
        if (empty($finalData['email']) || empty($selectedGroup)) {
            return ['success' => false, 'message' => 'Required field email or group is empty!', 'code' => 400];
        }

        $userId = self::getUserIdFromEmail($finalData['email']);

        if (!$userId) {
            return ['success' => false, 'message' => 'The user does not exist on your site, or the email is invalid!', 'code' => 400];
        }

        WPF()->member->clear_db_cache();

        if (\function_exists('dokan_member')) {
            $userGroupId = dokan_member($userId, 'groupid');
            $selectedGroup = (int) $selectedGroup;

            if ($selectedGroup && $selectedGroup === $userGroupId) {
                $defaultGroup = absint(WPF()->usergroup->default_groupid);
                $sql = 'UPDATE `' . WPF()->tables->profiles . '` SET `groupid` = %d WHERE `userid` = %d';
                if (false !== WPF()->db->query(WPF()->db->prepare($sql, $defaultGroup, $userId))) {
                    if (\function_exists('dokan_clean_cache')) {
                        dokan_clean_cache('avatar', $userId);
                    }

                    delete_user_meta(\intval($userId), '_wpf_member_obj');

                    if (\function_exists('dokan_setting')) {
                        if (dokan_setting('seo', 'seo_profile')) {
                            WPF()->seo->clear_cache();
                        }
                    }
                }

                return ['success' => true, 'message' => 'User removed from group.'];
            }
        }

        return ['success' => false, 'message' => 'Something went wrong!'];
    }

    public function createTopic($finalData, $topicOptions)
    {
        if (empty($finalData['email']) || empty($finalData['topic_title']) || empty($finalData['topic_content'])) {
            return ['success' => false, 'message' => 'Required fields are empty!', 'code' => 400];
        }

        if (empty($topicOptions['selectedForum'])) {
            return ['success' => false, 'message' => 'Forum id is required!', 'code' => 400];
        }

        $userId = self::getUserIdFromEmail($finalData['email']);

        if (!$userId) {
            return ['success' => false, 'message' => 'The user does not exist on your site, or the email is invalid!', 'code' => 400];
        }

        $actions = (array) $topicOptions['actions'];
        $privateTopic = 0;

        if (!empty($actions) && $actions['privateTopic']) {
            $privateTopic = 1;
        }

        $args['forumid'] = $topicOptions['selectedForum'];
        $args['title'] = sanitize_title($finalData['topic_title']);
        $args['body'] = preg_replace('#</pre>[\r\n\t\s\0]*<pre>#isu', "\r\n", (string) $finalData['topic_content']);
        $args['userid'] = $userId;

        if (!empty($topicOptions['selectedTags'])) {
            $args['tags'] = $topicOptions['selectedTags'];
        }

        $args['private'] = $privateTopic;
        WPF()->member->set_guest_cookies($args);
        $min = dokan_setting('posting', 'topic_body_min_length');

        if ($min) {
            if (wpfkey($args, 'body') && (int) $min > dokan_length($args['body'])) {
                return ['success' => false, 'message' => 'The content is too short', 'code' => 400];
            }
        }

        if (!isset($args['forumid'])) {
            return ['success' => false, 'message' => 'Add Topic error: No forum selected', 'code' => 400];
        }

        if (!WPF()->forum->get_forum($args['forumid'])) {
            return ['success' => false, 'message' => 'Add Topic error: No forum selected', 'code' => 400];
        }

        if (!WPF()->perm->forum_can('ct', $args['forumid'])) {
            return ['success' => false, 'message' => 'You don\'t have permission to create topic into this forum', 'code' => 400];
        }

        if (!WPF()->perm->can_post_now()) {
            return ['success' => false, 'message' => 'You are posting too quickly. Slow down.', 'code' => 400];
        }

        $topicId = WPF()->topic->add($args);

        if ($topicId) {
            return ['success' => true, 'message' => 'New topic created, topic id: ' . $topicId];
        }

        return ['success' => false, 'message' => 'Something went wrong!'];
    }

    public function deleteTopic($finalData, $selectedTopic)
    {
        if (empty($selectedTopic) && empty($finalData['topic_id'])) {
            return ['success' => false, 'message' => 'Topic id is required!', 'code' => 400];
        }

        if (!empty($selectedTopic)) {
            $topic = $selectedTopic;
        } else {
            $topic = $finalData['topic_id'];
        }

        $topicId = WPF()->topic->delete($topic, true, false);

        if ($topicId) {
            return ['success' => true, 'message' => 'Topic deleted successfully'];
        }

        return ['success' => false, 'message' => 'Something went wrong!'];
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

    public function execute($fieldValues, $fieldMap, $selectedTask, $actions)
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
