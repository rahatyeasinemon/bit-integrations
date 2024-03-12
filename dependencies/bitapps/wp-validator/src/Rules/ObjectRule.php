<?php
/**
 * @license MIT
 *
 * Modified on 12-March-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
namespace BTCBI\Deps\BitApps\WPValidator\Rules;

use BTCBI\Deps\BitApps\WPValidator\Rule;

class ObjectRule extends Rule
{
    private $message = "The :attribute must be object";

    public function validate($value)
    {
        return is_object($value);
    }

    public function message()
    {
        return $this->message;
    }
}
