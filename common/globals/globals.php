<?php
const SQL_INT_MAX = 2147483647;

function compareTwoDates($one, $condition, $two)
{
    list($one, $two) = [ strtotime($one), strtotime($two) ];
    if ($condition === '>')
    {
        return $one > $two;
    }
    elseif ($condition === '<')
    {
        return $one < $two;
    }
    elseif ($condition === '=')
    {
        return $one === $two;
    }
    else
    {
        throw new \Exception('Can not compare!');
    }
}

function now($format = false)
{
    try {
        if ($format !== false)
        {
            return date($format);
        }
    }
    catch (\Exception $e)
    {
    }
    return date('Y-m-d H:i:s');
}
function getenv_or_exception($key){
    $value = getenv($key);
    if (!$value){
        throw new Exception('There is no '. $key . ' in .env');
    } else {
        return $value;
    }
}

