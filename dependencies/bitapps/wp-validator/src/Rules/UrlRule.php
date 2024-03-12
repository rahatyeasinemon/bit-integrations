<?php
/**
 * @license MIT
 *
 * Modified on 12-March-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
namespace BTCBI\Deps\BitApps\WPValidator\Rules;

use BTCBI\Deps\BitApps\WPValidator\Rule;

class UrlRule extends Rule
{
    private $message = "The :attribute format is invalid";

    public function validate($value)
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    public function message()
    {
        return $this->message;
    }
}
