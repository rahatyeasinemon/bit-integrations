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
        $min = wpforo_setting('posting', 'topic_body_min_length');

        if ($min) {
            if (wpfkey($args, 'body') && (int) $min > wpforo_length($args['body'])) {
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

        $topicid = WPF()->topic->add($args);

        if ($topicid) {
            return ['success' => true, 'message' => 'New topic created, topic id: ' . $topicid];
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
            $type = 'Topic';
            $typeName = 'Create Topic';
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
