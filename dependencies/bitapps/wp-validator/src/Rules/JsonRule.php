<?php
/**
 * @license MIT
 *
 * Modified on 20-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace BTCBI\Deps\BitApps\WPValidator\Rules;

use BTCBI\Deps\BitApps\WPValidator\Rule;

class JsonRule extends Rule
{
    private $message = "The :attribute must be a valid JSON string";

    public function validate($value)
    {
        json_decode($value);
        return json_last_error() === JSON_ERROR_NONE;
    }

    public function message()
    {
        return $this->message;
    }
}
