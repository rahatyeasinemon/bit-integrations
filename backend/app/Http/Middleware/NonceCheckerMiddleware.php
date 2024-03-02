<?php

namespace BitApps\BTCBI\HTTP\Middleware;

use BitApps\BTCBI\Config;
use BTCBI\Deps\BitApps\WPKit\Http\Request\Request;

final class NonceCheckerMiddleware
{
    public function handle(Request $request, ...$params)
    {
        if (!$request->has('_ajax_nonce') || !wp_verify_nonce(sanitize_key($request->_ajax_nonce), Config::withPrefix('nonce'))) {
            // return Response::error('Invalid nonce token')->httpStatus(411);
        }

        return true;
    }
}
