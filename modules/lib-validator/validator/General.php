<?php
/**
 * General validator
 * @package lib-validator
 * @version 0.0.1
 */

namespace LibValidator\Validator;

class General
{
    static function array($value, $options, $object, $field, $rules): ?array{
        if(is_null($value))
            return null;
        
        if(!is_array($value))
            return ['1.0'];
        if($options === true)
            return null;

        $indexed = is_indexed_array($value);

        if($options === 'indexed' && !$indexed)
            return ['1.1'];
        if($options === 'assoc' && $indexed)
            return ['1.2'];
        return null;
    }

    static function callback($value, $options, $object, $field, $rules): ?array{
        if(is_null($value))
            return null;

        $handler = explode('::', $options);
        $class = $handler[0];
        $method= $handler[1];

        return $class::$method($value, $options, $object, $field, $rules);
    }

    static function date($value, $options, $object, $field, $rules): ?array{
        if(is_null($value))
            return null;

        $date = date_create_from_format($options, $value);
        if(false === $date)
            return ['2.0'];
        if(date_format($date, $options) != $value)
            return ['2.1'];
        return null;
    }

    static function email($value, $options, $object, $field, $rules): ?array{
        if(is_null($value))
            return null;

        $email = filter_var($value, FILTER_VALIDATE_EMAIL);
        if(false === $email)
            return ['3.0'];
        return null;
    }

    static function in($value, $options, $object, $field, $rules): ?array{
        if(is_null($value))
            return null;

        if(!in_array($value, $options))
            return ['4.0'];
        return null;
    }

    static function ip($value, $options, $object, $field, $rules): ?array{
        if(is_null($value))
            return null;

        if($options === true){
            if(false !== filter_var($value, FILTER_VALIDATE_IP))
                return null;
            return ['5.0'];
        }

        if($options == '4'){
            if(false !== filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
                return null;
            return ['5.1'];
        }
            
        if($options == '6'){
            if(false !== filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
                return null;
            return ['5.2'];
        }

        return null;
    }

    static function length($value, $options, $object, $field, $rules): ?array{
        if(is_null($value))
            return null;

        $len = strlen($value);

        if(isset($options->min) && $len < $options->min)
            return ['6.0'];

        if(isset($options->max) && $len > $options->max)
            return ['6.1'];

        return null;
    }

    static function notin($value, $options, $object, $field, $rules): ?array{
        if(is_null($value))
            return null;

        if(in_array($value, $options))
            return ['7.0'];
        return null;
    }

    static function numeric($value, $options, $object, $field, $rules): ?array{
        if(is_null($value))
            return null;

        if(!is_numeric($value))
            return ['8.0'];

        if(is_object($options)){
            if(isset($options->min) && $value < $options->min)
                return ['8.1'];

            if(isset($options->max) && $value > $options->max)
                return ['8.2'];

            if(isset($options->decimal)){
                $point = preg_replace('!^0\.!', '', (string)abs(round($value) - $value));
                if(strlen($point) != $options->decimal)
                    return ['8.3'];
            }
        }

        return null;
    }

    static function object($value, $options, $object, $field, $rules): ?array{
        if(is_null($value))
            return null;

        if(!is_object($value))
            return ['9.0'];
        return null;
    }

    static function regex($value, $options, $object, $field, $rules): ?array{
        if(is_null($value))
            return null;

        if(!preg_match($options, $value))
            return ['10.0'];
        return null;
    }

    static function required($value, $options, $object, $field, $rules): ?array{
        if(is_null($value))
            return ['11.0'];
        return null;
    }

    static function text($value, $options, $object, $field, $rules): ?array{
        if(is_null($value))
            return null;

        if(!is_string($value))
            return ['12.0'];

        if($options === 'slug' && !preg_match('!^[a-z0-9-_]+$!', $value))
            return ['12.1'];

        if($options === 'alnumdash' && !preg_match('!^[a-zA-Z0-9-]+$!', $value))
            return ['12.2'];

        if($options === 'alpha' && !preg_match('!^[a-zA-Z]+$!', $value))
            return ['12.3'];

        if($options === 'alnum' && !preg_match('!^[a-zA-Z0-9]+$!', $value))
            return ['12.4'];

        return null;
    }
    
    static function url($value, $options, $object, $field, $rules): ?array{
        if(is_null($value))
            return null;
        
        if(!filter_var($value, FILTER_VALIDATE_URL))
            return ['13.0'];
        if(!is_object($options))
            return null;

        if(isset($options->path) && !filter_var($value, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED))
            return ['13.1'];

        if(isset($options->query)){
            if(!filter_var($value, FILTER_VALIDATE_URL, FILTER_FLAG_QUERY_REQUIRED))
                return ['13.2'];

            if(is_string($options->query))
                $options->query = (array)$options->query;

            if(is_array($options->query)){
                $query = parse_url($value, PHP_URL_QUERY);
                if(!$query)
                    return ['13.2'];

                parse_str($query, $qry);

                foreach($options->query as $val){
                    if(!isset($qry[$val]))
                        return ['13.3'];
                }
            }
        }

        return null;
    }
}