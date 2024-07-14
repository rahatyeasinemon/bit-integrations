<?php

/**
 * WPForo Record Api
 */

namespace BitCode\FI\Actions\WPForo;

use BitCode\FI\Core\Util\Common;
use BitCode\FI\Log\LogHandler;
use WPForo\Inc\Access;

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
            return ['success' => false, 'message' => 'Required field email or group is empty!', 'code' => 400];
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

    public function revokeAccessFromGroup($finalData, $selectedGroup)
    {
        if (empty($finalData['email']) || empty($selectedGroup)) {
            return ['success' => false, 'message' => 'Required field email or group is empty!', 'code' => 400];
        }

        $userId = self::getUserIdFromEmail($finalData['email']);

        if (!$userId) {
            return ['success' => false, 'message' => 'The user does not exist on your site, or the email is invalid!', 'code' => 400];
        }

        Access::revoke($userId, $selectedGroup);

        return ['success' => true];
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

    public function execute($fieldValues, $fieldMap, $selectedTask, $selectedReputation)
    {
        $finalData = $this->generateReqDataFromFieldMap($fieldValues, $fieldMap);

        $type = $typeName = '';

        if ($selectedTask === 'userReputation') {
            $response = $this->setUserReputation($finalData, $selectedReputation);
            $type = 'Reputation';
            $typeName = 'Set User Reputation';
        } elseif ($selectedTask === 'revokeAccess') {
            $response = $this->revokeAccessFromGroup($finalData, $selectedGroup);
            $responseMessage = 'User removed from the access group.';
            $typeName = 'Revoke Access';
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
