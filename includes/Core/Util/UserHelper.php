<?php

namespace BitCode\FI\Core\Util;

use WP_User;

/**
 * bit-integration User helper class
 *
 * @since 1.0.0
 */
final class UserHelper
{
    public static function getUserData($id)
    {
        if (empty($id)) {
            return [];
        }

        $user = new WP_User($id);
        if (empty($user->email)) {
            return [];
        }

        return [
            'wp_user_id'      => $user->ID,
            'user_login'      => $user->user_login,
            'display_name'    => $user->display_name,
            'user_firstname'  => $user->user_firstname,
            'user_lastname'   => $user->user_lastname,
            'user_email'      => $user->user_email,
            'user_registered' => $user->user_registered,
            'user_role'       => $user->roles,
        ];
    }
}
