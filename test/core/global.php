<?php

function is_indexed_array(array $array): bool{
    if(!$array)
        return true;
    return array_keys($array) === range(0, count($array) - 1);
}