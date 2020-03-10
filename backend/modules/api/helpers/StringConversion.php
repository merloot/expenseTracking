<?php
/**
 * Created by PhpStorm.
 * User: black
 * Date: 12.04.2017
 * Time: 22:59
 */

namespace app\modules\api\helpers;


class StringConversion
{
    /**
     * Перевод строки в вид "Верблюжий Вид".
     * @param $string (string)
     * @return string
     */
    public static function toCamelNotation($string) {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }


    public static function toLowercase($string) {
        return mb_strtolower(str_replace('-', '', $string));
    }

}