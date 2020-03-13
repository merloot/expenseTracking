<?php

namespace console\controllers;

use yii\helpers\Json;
use common\models\Check;
use common\models\Goods;
use yii\console\Controller;
use app\modules\api\extensions\JResponse;


class TestController extends Controller {

    public $testArray=[];
    public function actionIndex(){
        while (true){
            try{
                $this->many();
//                $this->cUrl();
            }catch (\Exception $exception){
                var_dump($exception->getMessage());
            }
            sleep(5);
        }
    }

    public function cUrl(){
//            $url = 'https://proverkacheka.nalog.ru:9999/v1/inns/*/kkts/*/fss/9282000100291177/tickets/106856?fiscalSign=1887839089&sendToEmail=no';

        $fn = 9289000100230581;
        $fp = 1920673435;
        $i  = 93074;
        $url = 'https://proverkacheka.nalog.ru:9999/v1/inns/*/kkts/*/fss/'.$fn.'/tickets/'.$i.'?fiscalSign='.$fp.'&sendToEmail=no';
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

        $model = new Check();
        $model->fiscal_sign = $fp;
        $model->fiscal_document_number= $i;
        $model->fiscal_drive_number = $fn;
        $model->user_id = 3;
        if (curl_getinfo($curl)['http_code']===406){
            var_dump(1);
            $model->confirmed = false;
            $model->save();
        }else{
            $check = Json::decode($response);

            $model->company_inn     = $check['document']['receipt']['userInn'];
            $model->company         = $check['document']['receipt']['user'];
            $model->shift_number    = $check['document']['receipt']['shiftNumber'];
            $model->request_number  = $check['document']['receipt']['requestNumber'];
            $model->operation_type  = $check['document']['receipt']['operationType'];
            $model->nds10           = $check['document']['receipt']['nds10'];
            $model->nds18           = $check['document']['receipt']['nds18'];
            $model->amount          = $check['document']['receipt']['totalSum'];
            $model->confirmed       =true;
            $model->kkt_reg_id      =$check['document']['receipt']['kktRegId'];
            $model->seller          =$check['document']['receipt']['operator'];
            $model->date_time       =date('Y-m-d H:i:s',strtotime($check['document']['receipt']['dateTime']));
            if (!$model->save()){
                var_dump($model->getErrors());
            }

            foreach ($check['document']['receipt']['items'] as $item){
                $good = new Goods();
                $good->name     = $item['document']['receipt']['name'];
                $good->amount   = $item['document']['receipt']['price'];
                $good->check_id = $model->id;
                $good->count    = $item['document']['receipt']['quantity'];
                $good->price    = $item['document']['receipt']['sum'];
                if (!$good->save()){
                    var_dump($good->getErrors());
                }
            }
        }
    }

    public function many(){
        $check = Check::find()->where(['id'=>3])->joinWith('goods')->asArray()->one();
        if (!$check){
            return var_dump('Check not find');
        }else{
            return var_dump($check);
        }
    }
}