<?php

namespace BitCode\FI\controller;

use BitCode\FI\Core\Database\AuthModel;

final class AuthDataController
{
    public function saveAuthData($requestParams)
    {
        $actionName = sanitize_text_field($requestParams->actionName);
        $tokenDetails = wp_json_encode($requestParams->tokenDetails);
        $userInfo = wp_json_encode($requestParams->userInfo);

        $sanitizedTokenDetails = sanitize_text_field($tokenDetails);
        $sanitizedUserInfo = sanitize_text_field($userInfo);

        if (empty($actionName) || empty($sanitizedTokenDetails) || empty($sanitizedUserInfo)) {
            return;
        }

        $authModel = new AuthModel();
        $authModel->insert(
            [
                'action_name'  => $actionName,
                'tokenDetails' => $sanitizedTokenDetails,
                'userInfo'     => $sanitizedUserInfo,
                'created_at'   => current_time('mysql')
            ]
        );

        return $this->getAuthData($actionName);
    }

    public function getAuthData($request)
    {
        $actionName = sanitize_text_field($request->actionName);
        if (empty($actionName)) {
            wp_send_json_error('Action name is not available');
            exit;
        }

        $authModel = new AuthModel();
        $result = $authModel->get(
            [
                'id',
                'action_name',
                'tokenDetails',
                'userInfo',
            ],
            ['action_name' => $actionName]
        );

        if (is_wp_error($result)) {
            wp_send_json_success(['data' => []]);
            exit;
        }

        foreach ($result as $item) {
            $item->tokenDetails = json_decode($item->tokenDetails, true);
            $item->userInfo = json_decode($item->userInfo, true);
        }

        wp_send_json_success(['data' => $result]);
        exit;
    }

    public function getAuthDataById($request)
    {
        $id = absint($request->id);
        if (empty($id)) {
            wp_send_json_error('Action name is not available');
            exit;
        }

        $authModel = new AuthModel();
        $result = $authModel->get(
            [
                'id',
                'action_name',
                'tokenDetails',
                'userInfo',
            ],
            ['id' => $id]
        );

        if (is_wp_error($result)) {
            wp_send_json_success(['data' => []]);
            exit;
        }

        foreach ($result as $item) {
            $item->tokenDetails = json_decode($item->tokenDetails, true);
            $item->userInfo = json_decode($item->userInfo, true);
        }

        wp_send_json_success(['data' => $result]);
        exit;
    }

    public function deleteAuthData($id)
    {
        $condition = null;
        $id = absint($id);
        if (!empty($id)) {
            $condition = [
                'id' => $id
            ];
        }
        $authModel = new AuthModel();

        return $authModel->delete($condition);
    }
}
