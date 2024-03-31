<?php

namespace BitApps\BTCBI\Http\Controllers;

use BTCBI\Deps\BitApps\WPKit\Http\Request\Request;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

final class ConfigController
{
    public function updatedAppConfig(Request $request)
    {
        if (!$request->has('data')) {
            return Response::error(__('Data can\'t be empty', 'bit-integrations'));
        }

        update_option('btcbi_app_conf', $request->data);
        return Response::success(__('save successfully done', 'bit-integrations'));
    }

    public function getAppConfig()
    {
        $data = get_option('btcbi_app_conf');
        return Response::success($data);
    }
}
