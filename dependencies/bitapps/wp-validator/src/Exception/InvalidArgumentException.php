<?php
/**
 * @license MIT
 *
 * Modified on 30-March-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace BTCBI\Deps\BitApps\WPValidator\Exception;

use Exception;

class InvalidArgumentException extends \Exception

{

    public function __construct($ruleName, $parameterCount, $code = 0, Exception $previous = null)
    {
        parent::__construct(sprintf("Validation rule %s requires at least %d parameters", $ruleName, $parameterCount), $code, $previous);
    }
}
