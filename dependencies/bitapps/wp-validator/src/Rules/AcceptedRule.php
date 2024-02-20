<?php
/**
 * @license MIT
 *
 * Modified on 20-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace BTCBI\Deps\BitApps\WPValidator\Rules;

use BTCBI\Deps\BitApps\WPValidator\Rule;

class AcceptedRule extends Rule
{
    private $message = "The :attribute must be accepted";

    public function validate($value)
    {
        $accepted = ['yes', 'on', '1', 1, true, 'true'];
        return in_array($value, $accepted, true);
    }

    public function message()
    {
        return $this->message;
    }
}
