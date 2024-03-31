<?php
/**
 * @license MIT
 *
 * Modified on 30-March-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace BTCBI\Deps\BitApps\WPValidator\Exception;

use Exception;

class MethodNotFoundException extends \Exception

{
    public function __construct($sanitizationMethod, $code = 0, Exception $previous = null)
    {
        parent::__construct(sprintf("Unsupported sanitization method: %s.", $sanitizationMethod), $code, $previous);
    }
}
