<?php
/**
 * @license MIT
 *
 * Modified on 22-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace BTCBI\Deps\BitApps\WPValidator\Rules;

use BTCBI\Deps\BitApps\WPValidator\Rule;

class StringRule extends Rule
{
    protected $message = "The :attribute field should be string";

    public function validate($value)
    {
        return is_string($value);
    }

    public function message()
    {
        return $this->message;
    }
}
