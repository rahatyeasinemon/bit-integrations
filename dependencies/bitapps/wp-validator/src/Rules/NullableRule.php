<?php
/**
 * @license MIT
 *
 * Modified on 22-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
namespace BTCBI\Deps\BitApps\WPValidator\Rules;

use BTCBI\Deps\BitApps\WPValidator\Rule;

class NullableRule extends Rule
{

    private $message = '';

    public function validate($value)
    {
        return true;
    }

    public function message()
    {
        return $this->message;
    }
}
