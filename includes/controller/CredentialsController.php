<?php

namespace BitCode\FI\controller;

use BitCode\FI\Core\Util\HttpHelper;

final class CredentialsController
{
    public function getCredentials($params)
    {
        $apiEndpoint = 'https://auth-apps.bitapps.pro/apps/' . $params->actionName;

        return $response = HttpHelper::get($apiEndpoint, null, null);
    }
}
