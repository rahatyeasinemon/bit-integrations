<?php

namespace BitApps\BTCBI\Http\Controllers;

use BTCBI\Deps\BitApps\WPKit\Http\Response;

final class UserController
{
    public function __construct()
    {
        //
    }

    public function getWpUsers()
    {
        $users = get_users(['fields' => ['display_name', 'ID']]);

        return Response::success($users);
    }

    public function getUserRoles()
    {
        global $wp_roles;
        $roles = [];
        $key = 0;
        foreach ($wp_roles->get_names() as $index => $role) {
            $key++;
            $roles[$key]['key'] = $index;
            $roles[$key]['name'] = $role;
        }
        return Response::success($roles);
    }
}
