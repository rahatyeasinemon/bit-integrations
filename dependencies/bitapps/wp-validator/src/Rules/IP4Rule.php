<?php
/**
 * @license MIT
 *
 * Modified on 18-April-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
namespace BTCBI\Deps\BitApps\WPValidator\Rules;

use BTCBI\Deps\BitApps\WPValidator\Rule;

class IP4Rule extends Rule
{
    private $message = "The :attribute must be a valid IPv4 address";

    public function validate($value)
    {
        return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }

    public function message()
    {
        return $this->message;
    }
}
