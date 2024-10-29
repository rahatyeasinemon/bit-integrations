<?php

namespace BitCode\FI\controller;

use BitCode\FI\Core\Database\AuthModel;

final class AuthDataController
{
    public function saveAuthData($requestParams)
    {
        $actionName = sanitize_text_field($requestParams->actionName);

        // Process tokenDetails with nested sanitization
        $tokenDetails = $requestParams->tokenDetails;
        $sanitizedTokenDetails = [
            'access_token'     => isset($tokenDetails->access_token) ? sanitize_textarea_field($tokenDetails->access_token) : '',
            'expires_in'       => isset($tokenDetails->expires_in) ? \intval($tokenDetails->expires_in) : 0,
            'refresh_token'    => isset($tokenDetails->refresh_token) ? sanitize_textarea_field($tokenDetails->refresh_token) : '',
            'scope'            => isset($tokenDetails->scope) ? sanitize_text_field($tokenDetails->scope) : '',
            'token_type'       => isset($tokenDetails->token_type) ? sanitize_text_field($tokenDetails->token_type) : '',
            'generates_on'     => isset($tokenDetails->generates_on) ? \intval($tokenDetails->generates_on) : 0,
            'selectedAuthType' => isset($tokenDetails->selectedAuthType) ? sanitize_text_field($tokenDetails->selectedAuthType) : '',
        ];

        // Process userInfo with nested sanitization
        $userInfo = $requestParams->userInfo;
        $sanitizedUserInfo = [
            'user' => [
                'kind'         => isset($userInfo->user->kind) ? sanitize_text_field($userInfo->user->kind) : '',
                'displayName'  => isset($userInfo->user->displayName) ? sanitize_text_field($userInfo->user->displayName) : '',
                'photoLink'    => isset($userInfo->user->photoLink) ? esc_url_raw($userInfo->user->photoLink) : '',
                'me'           => isset($userInfo->user->me) ? filter_var($userInfo->user->me, FILTER_VALIDATE_BOOLEAN) : false,
                'permissionId' => isset($userInfo->user->permissionId) ? sanitize_text_field($userInfo->user->permissionId) : '',
                'emailAddress' => isset($userInfo->user->emailAddress) ? sanitize_email($userInfo->user->emailAddress) : '',
            ]
        ];

        // Insert sanitized data into the database
        $authModel = new AuthModel();
        $response = $authModel->insert(
            [
                'action_name'  => $actionName,
                'tokenDetails' => wp_json_encode($sanitizedTokenDetails),
                'userInfo'     => wp_json_encode($sanitizedUserInfo),
                'created_at'   => current_time('mysql')
            ]
        );

        return $this->getAuthData($actionName);
    }

    public function getAuthData($request)
    {
        $actionName = $request->actionName;
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
