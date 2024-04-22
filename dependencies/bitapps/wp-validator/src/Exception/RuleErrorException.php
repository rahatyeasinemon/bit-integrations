<?php
/**
 * @license MIT
 *
 * Modified on 18-April-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace BTCBI\Deps\BitApps\WPValidator\Exception;

use Exception;

class RuleErrorException extends \Exception

{
    public function __construct($ruleName, $code = 0, Exception $previous = null)
    {
        parent::__construct(sprintf("Unsupported validation rule: %s.", $ruleName), $code, $previous);
    }
}
