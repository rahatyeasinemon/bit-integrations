<?php

namespace BitApps\BTCBI\Util;

final class Capabilities
{
    public static function Check($cap, ...$args)
    {
        return current_user_can($cap, ...$args);
    }

    public static function Filter($cap, $default = 'manage_options')
    {
        return static::Check(Hooks::applyFilter($cap, $default));
    }
}
