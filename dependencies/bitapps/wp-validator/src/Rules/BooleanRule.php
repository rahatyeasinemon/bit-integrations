<?php
/**
 * @license MIT
 *
 * Modified on 27-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace BTCBI\Deps\BitApps\WPValidator\Rules;

use BTCBI\Deps\BitApps\WPValidator\Rule;

class BooleanRule extends Rule
{
    private $message = 'The :attribute must be a boolean';

    public function validate($value)
    {
        return \in_array($value, [true, false, 'true', 'false', 1, 0, '0', '1'], true);
    }

    public function message()
    {
        return $this->message;
    }
}
