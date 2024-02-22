<?php
/**
 * @license MIT
 *
 * Modified on 22-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
namespace BTCBI\Deps\BitApps\WPValidator\Rules;

use BTCBI\Deps\BitApps\WPValidator\Rule;

class ArrayRule extends Rule
{
    private $message = "The :attribute must be array";

    public function validate($value)
    {
        return is_array($value);
    }

    public function message()
    {
        return $this->message;
    }
}
