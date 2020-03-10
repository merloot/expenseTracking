<?php

namespace console\controllers;

use yii\helpers\Json;
use yii\console\Controller;


class TestController extends Controller {

    public function actionIndex(){
        while (true){
            try{
                $this->cUrl();
            }catch (\Exception $exception){
                var_dump($exception->getMessage());
            }
            sleep(60);
        }
    }

    public function cUrl(){
        try{
            $url = 'https://proverkacheka.nalog.ru:9999/v1/inns/*/kkts/*/fss/9282000100291177/tickets/106856?fiscalSign=1887839089&sendToEmail=no';
            $headers = [
                'Accepts: application/json',
                'device-id: 1',
                'device-os: 1',
                'Authorization: Basic Kzc5NTI4MDM3OTgyOjIxMjMyNA=='
            ];

            $curl = curl_init();
            curl_setopt_array($curl,[
                CURLOPT_URL             => $url,
                CURLOPT_HTTPHEADER      =>$headers,
                CURLOPT_RETURNTRANSFER  => 1

            ]);

            $response = curl_exec($curl);

            $content = Json::decode($response);

            curl_close($curl); // Close request
            var_dump($content['document']['receipt']);


        }catch (\Exception $exception){
            var_dump($exception->getMessage());
        }
    }
}