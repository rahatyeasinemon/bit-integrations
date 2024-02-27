<?php
/**
 * @license MIT
 *
 * Modified on 27-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
namespace BTCBI\Deps\BitApps\WPValidator\Rules;

use BTCBI\Deps\BitApps\WPValidator\Rule;

class NumericRule extends Rule
{
    private $message = "The :attribute must be a number";

    public function validate($value)
    {
        return is_numeric($value);
    }

    public function message()
    {
        return $this->message;
    }
}
