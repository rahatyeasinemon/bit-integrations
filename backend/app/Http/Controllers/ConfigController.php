<?php

namespace BitApps\BTCBI\Http\Controllers;

use BTCBI\Deps\BitApps\WPKit\Http\Response;

final class ConfigController
{
    public function updatedAppConfig($data)
    {
        if (!property_exists($data, 'data')) {
            return Response::error(__('Data can\'t be empty', 'bit-integrations'));
        }

        update_option('btcbi_app_conf', $data->data);
        return Response::success(__('save successfully done', 'bit-integrations'));
    }

    public function getAppConfig()
    {
        $data = get_option('btcbi_app_conf');
        return Response::success($data);
    }
}
