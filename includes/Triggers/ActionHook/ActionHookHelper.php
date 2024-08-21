<?php

namespace BitCode\FI\Triggers\ActionHook;

use ReflectionClass;

class ActionHookHelper
{
    public static function convertToSimpleArray($value)
    {
        if (\is_object($value)) {
            $value = static::convertObjectToArray($value);
        }

        if (\is_array($value)) {
            foreach ($value as $key => $subValue) {
                $value[$key] = static::convertToSimpleArray($subValue);
            }
        }

        return $value;
    }

    private static function convertObjectToArray($object)
    {
        $reflection = new ReflectionClass($object);
        $properties = $reflection->getProperties();

        $array = [];
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $name = $property->getName();
            $value = $property->getValue($object);

            $name = preg_replace('/^\x00(?:\*\x00|\w+\x00)/', '', $name);

            $array[$name] = static::convertToSimpleArray($value);
        }

        return $array;
    }
}
