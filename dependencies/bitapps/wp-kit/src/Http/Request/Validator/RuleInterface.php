<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified on 22-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace BTCBI\Deps\BitApps\WPKit\Http\Request\Validator;

interface RuleInterface
{
    /**
     * Checks if value validate the rule
     *
     * @param string $value
     *
     * @return bool
     */
    public function validate($value);

    public function message();
}
