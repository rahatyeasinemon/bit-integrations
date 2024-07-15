<?php

/**
 * WPForo Record Api
 */

namespace BitCode\FI\Actions\WPForo;

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

        if (\function_exists('wpforo_member')) {
            $userGroupId = wpforo_member($userId, 'groupid');
            $selectedGroup = (int) $selectedGroup;

            if ($selectedGroup && $selectedGroup === $userGroupId) {
                $defaultGroup = absint(WPF()->usergroup->default_groupid);
                $sql = 'UPDATE `' . WPF()->tables->profiles . '` SET `groupid` = %d WHERE `userid` = %d';
                if (false !== WPF()->db->query(WPF()->db->prepare($sql, $defaultGroup, $userId))) {
                    if (\function_exists('wpforo_clean_cache')) {
                        wpforo_clean_cache('avatar', $userId);
                    }

                    delete_user_meta(\intval($userId), '_wpf_member_obj');

                    if (\function_exists('wpforo_setting')) {
                        if (wpforo_setting('seo', 'seo_profile')) {
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

        $actions = (array) $topicOptions['actions'];
        $privateTopic = 0;

        if (!empty($actions) && $actions['privateTopic']) {
            $privateTopic = 1;
        }

        error_log(print_r(['topic options' => $topicOptions, empty((array) $topicOptions['actions'])], true));
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
            $actionValue = $value->wpforoField;
            if ($triggerValue === 'custom') {
                $dataFinal[$actionValue] = Common::replaceFieldWithValue($value->customValue, $data);
            } elseif (!\is_null($data[$triggerValue])) {
                $dataFinal[$actionValue] = $data[$triggerValue];
            }
        }

        return $dataFinal;
    }

    public function execute($fieldValues, $fieldMap, $selectedTask, $selectedReputation, $selectedGroup, $topicOptions)
    {
        $finalData = $this->generateReqDataFromFieldMap($fieldValues, $fieldMap);

        $type = $typeName = '';

        if ($selectedTask === 'userReputation') {
            $response = $this->setUserReputation($finalData, $selectedReputation);
            $type = 'Reputation';
            $typeName = 'Set User Reputation';
        } elseif ($selectedTask === 'addToGroup') {
            $response = $this->addToGroup($finalData, $selectedGroup);
            $type = 'Group';
            $typeName = 'Add User to Group';
        } elseif ($selectedTask === 'removeFromGroup') {
            $response = $this->removeFromGroup($finalData, $selectedGroup);
            $type = 'Group';
            $typeName = 'Remove User from Group';
        } elseif ($selectedTask === 'createTopic') {
            $response = $this->createTopic($finalData, $topicOptions);
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
