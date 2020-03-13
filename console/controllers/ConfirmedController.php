<?php

namespace console\controllers;


use common\models\Check;
use common\models\Goods;
use yii\console\Controller;


class ConfirmedController extends Controller {


    public function actionIndex(){
        while (true){
            try{
                $notConfirmed = Check::find()->where(['confirmed'=>false])->all();
                foreach ($notConfirmed as $check){
                    $this->sendCheck($check);
//                    var_dump($this->sendCheck($check));
                }
            }catch (\Exception $exception){
                var_dump($exception->getMessage());
            }
            sleep(5);
        }
    }

    public function sendCheck($check){

        $url = 'https://proverkacheka.nalog.ru:9999/v1/inns/*/kkts/*/fss/'.(int)$check->fiscal_drive_number.'/tickets/'.$check->fiscal_document_number.'?fiscalSign='.$check->fiscal_sign.'&sendToEmail=no';
        $headers = [
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

        if (curl_getinfo($curl)['http_code']===406) {
            return false;
        } else {
            $content = Json::decode($response);

            $check->company_inn     = $content['document']['receipt']['userInn'];
            $check->company         = $content['document']['receipt']['user'];
            $check->shift_number    = $content['document']['receipt']['shiftNumber'];
            $check->request_number  = $content['document']['receipt']['requestNumber'];
            $check->operation_type  = $content['document']['receipt']['operationType'];
            $check->nds10           = $content['document']['receipt']['nds10'];
            $check->nds18           = $content['document']['receipt']['nds18'];
            $check->amount          = $content['document']['receipt']['totalSum'];
            $check->confirmed       =true;
            $check->kkt_reg_id      =$content['document']['receipt']['kktRegId'];
            $check->seller          =$content['document']['receipt']['operator'];
            $check->date_time       =date('Y-m-d H:i:s',strtotime($content['document']['receipt']['dateTime']));
            if (!$check->save()){
                var_dump($check->getErrors());
            }
            foreach ($check['document']['receipt']['items'] as $item){
                $good = new Goods();
                $good->name     = $item['document']['receipt']['name'];
                $good->amount   = $item['document']['receipt']['price'];
                $good->check_id = $check->id;
                $good->count    = $item['document']['receipt']['quantity'];
                $good->price    = $item['document']['receipt']['sum'];
                if (!$good->save()){
                    var_dump($good->getErrors());
                }
            }
            return true;
        }

    }
}