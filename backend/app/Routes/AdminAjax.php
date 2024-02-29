<?php

namespace BitApps\BTCBI\Routes;

use BTCBI\Deps\BitApps\WPKit\Http\Response;
use BTCBI\Deps\BitApps\WPKit\Http\Router\Route;

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
