<?php

namespace BitCode\FI\Actions\WishList;

use BitCode\FI\Core\Util\HttpHelper;

/**
 * wlmapiclass
 * Helper class for WishList Member API 2.0
 *
 * @author Mike Lopez https://wishlistproducts.com
 *
 * @version 1.7
 */
class wlmapiclass
{
    public $url;

    public $key;

    public $return_format = 'json';

    public $authenticated = 0;

    public $auth_error = '';

    public $fake = 0;

    public $method_emulation = 0;

    public $cookie_file;

    /**
     * Initailize wlmapi
     *
     * @param string     $url     WordPress URL
     * @param string     $key     API Key
     * @param null|mixed $tempdir
     */
    public function __construct($url, $key, $tempdir = null)
    {
        if (substr($url, -1) != '/') {
            $url .= '/';
        }
        if (\is_null($tempdir)) {
            if (\function_exists('sys_get_temp_dir')) {
                $tempdir = sys_get_temp_dir();
            }
            if (!$tempdir) {
                $tempdir = '/tmp';
            }
        }
        $this->tempdir = $tempdir;
        $this->url = $url . '?/wlmapi/2.0/';
        $this->key = $key;

        if (empty($this->cookie_file)) {
            $this->cookie_file = tempnam($this->tempdir, 'wlmapi');
        }
    }

    public function __destruct()
    {
        if (file_exists($this->cookie_file)) {
            unlink($this->cookie_file);
        }
    }

    /**
     * Send a POST request to WishList Member API (add new data)
     * Returns API result on success or false on error.
     * If an error occurred, a short description of the error will be
     * stored in the object's auth_error property
     *
     * @param string $resource
     * @param array  $data
     *
     * @return xml|php|json|false
     */
    public function post($resource, $data)
    {
        if (!$this->_auth()) {
            return false;
        }

        return $this->_request('POST', $this->_resourcefix($resource), $data);
    }

    /**
     * Send a GET request to WishList Member API (retrieve data)
     * Returns API result on success or false on error.
     * If an error occurred, a short description of the error will be
     * stored in the object's auth_error property
     *
     * @param string           $resource
     * @param array (optional) $data
     *
     * @return xml|php|json|false
     */
    public function get($resource, $data = '')
    {
        if (!$this->_auth()) {
            error_log('Get Request Authorization failed');

            return false;
        }

        return $this->_request('GET', $this->_resourcefix($resource), $data);
    }

    /**
     * Send a PUT request to WishList Member API (update existing data)
     * Returns API result on success or false on error.
     * If an error occurred, a short description of the error will be
     * stored in the object's auth_error property
     *
     * @param string $resource
     * @param array  $data
     *
     * @return xml|php|json|false
     */
    public function put($resource, $data)
    {
        if (!$this->_auth()) {
            return false;
        }

        return $this->_request('PUT', $this->_resourcefix($resource), $data);
    }

    /**
     * Send a DELETE to WishList Member API (delete the resource)
     * Returns API result on success or false on error.
     * If an error occurred, a short description of the error will be
     * stored in the object's auth_error property
     *
     * @param string           $resource
     * @param array (optional) $data
     *
     * @return xml|php|json|false
     */
    public function delete($resource, $data = '')
    {
        if (!$this->_auth()) {
            return false;
        }

        return $this->_request('DELETE', $this->_resourcefix($resource), $data);
    }

    public function addCookieJar($handle, $r, $url)
    {
        curl_setopt($handle, CURLOPT_COOKIEFILE, $this->cookie_file);
        curl_setopt($handle, CURLOPT_COOKIEJAR, $this->cookie_file);
    }
    private function _request($method, $resource, $data = '')
    {
        if (\defined('WLMAPICLASS_PASS_NOCACHE_DATA') && WLMAPICLASS_PASS_NOCACHE_DATA) {
            usleep(1);
            if (!\is_array($data)) {
                $data = [];
            }
            $data['__nocache__'] = md5(microtime());
        }

        $data = empty($data) ? '' : http_build_query($data);
        $url = $this->url . $this->return_format . $resource;
        add_action('http_api_curl', [$this, 'addCookieJar'], 10, 3);
        $response = HttpHelper::request($url, strtoupper($method), $data, null, ['cookies' => ['lock' => $this->lock]]);
        remove_action('http_api_curl', [$this, 'addCookieJar'], 10);

        return $response;
    }

    private function _resourcefix($resource)
    {
        if (substr($resource, 0, 1) != '/') {
            $resource = '/' . $resource;
        }

        return $resource;
    }

    private function _auth()
    {
        if (!empty($this->authenticated)) {
            return true;
        }
        $m = $this->return_format;
        $this->return_format = 'php';

        $output = $this->_request('GET', '/auth');

        $output = isset($output) ? unserialize($output) : '';

        if ($output['success'] != 1 || empty($output['lock'])) {
            $this->auth_error = 'No auth lock to open';

            return false;
        }

        $hash = md5($output['lock'] . $this->key);

        $this->authenticated = 1;

        $data = [
            'key'               => $hash,
            'support_emulation' => 1,
        ];

        $output = $this->_request('POST', '/auth', $data);

        $output = \is_string($output) ? unserialize($output) : $output;
        error_log(print_r(['---->>' => $output, \is_string($output) ? 'ye': 'na'], true));
        if ($output['success'] == 1) {
            $this->authenticated = 1;
            if (!empty($output['support_emulation'])) {
                $this->method_emulation = 1;
            }
        } else {
            $this->auth_error = $output['ERROR'];

            return false;
        }

        $this->return_format = $m;

        return true;
    }
}
