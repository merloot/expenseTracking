<?php

namespace common\helpers;

use linslin\yii2\curl\Curl;
use yii\helpers\Json;


class CurlHelper {

    public static function sendGet($url, $params) {

        $response = (new Curl())->setOption(CURLOPT_FOLLOWLOCATION, true)->setGetParams($params)->get($url);
        try {
            $ret = Json::decode($response);

        } catch (\Exception $exception) {
            throw new \Exception('Fail decode response:' . $response);
        }
        return $ret;
    }

    public static function sendPost($url, $params) {

        $response = (new Curl())->setOption(CURLOPT_FOLLOWLOCATION, true)->setPostParams($params)->post($url);
        try {
            $ret = Json::decode($response);

        } catch (\Exception $exception) {
            throw new \Exception('Fail decode response:' . $response);

        }
        return $ret;
    }
}