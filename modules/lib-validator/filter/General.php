<?php
/**
 * Validator filters
 * @package lib-validator
 * @version 0.0.1
 */

namespace LibValidator\Filter;

class General
{
    static function array($value, $options, $object, $field, $filters){
        return (array)$value;
    }

    static function boolean($value, $options, $object, $field, $filters){
        return (bool)$value;
    }

    static function float($value, $options, $object, $field, $filters){
        return (float)$value;
    }

    static function integer($value, $options, $object, $field, $filters){
        return (int)$value;
    }

    static function lowercase($value, $options, $object, $field, $filters){
        return strtolower($value);
    }

    static function object($value, $options, $object, $field, $filters){
        return (object)$value;
    }

    static function string($value, $options, $object, $field, $filters){
        return (string)$value;
    }

    static function ucwords($value, $options, $object, $field, $filters){
        return ucwords($value);
    }

    static function uppercase($value, $options, $object, $field, $filters){
        return strtoupper($value);
    }
}