<?php

namespace BitApps\BTCBI\Routes;

use BitApps\BTCBI\Util\Route;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

class AdminAjax
{
    public function register()
    {
        Route::post('app/config', [$this, 'updatedAppConfig']);
        Route::get('get/config', [$this, 'getAppConfig']);

    }

    public function updatedAppConfig($data)
    {
        if (!property_exists($data, 'data')) {
            Response::error(__('Data can\'t be empty', 'bit-integrations'));
        }

        update_option('btcbi_app_conf', $data->data);
        Response::success(__('save successfully done', 'bit-integrations'));
    }

    public function getAppConfig()
    {
        $data = get_option('btcbi_app_conf');
        Response::success($data);
    }

}
