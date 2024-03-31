<?php
/**
 * @license MIT
 *
 * Modified on 30-March-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
namespace BTCBI\Deps\BitApps\WPValidator\Rules;

use BTCBI\Deps\BitApps\WPValidator\Rule;

class DateRule extends Rule
{
    private $message = "The :attribute is not a valid date";

    public function validate($value)
    {
        return strtotime($value) !== false;
    }

    public function message()
    {
        return $this->message;
    }
}
