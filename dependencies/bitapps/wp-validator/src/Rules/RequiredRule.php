<?php
/**
 * @license MIT
 *
 * Modified on 30-March-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
namespace BTCBI\Deps\BitApps\WPValidator\Rules;

use BTCBI\Deps\BitApps\WPValidator\Helpers;
use BTCBI\Deps\BitApps\WPValidator\Rule;

class RequiredRule extends Rule
{
    use Helpers;

    private $message = 'The :attribute field is required';

    public function validate($value)
    {
        return !$this->isEmpty($value);
    }

    public function message()
    {
        return $this->message;
    }
}
