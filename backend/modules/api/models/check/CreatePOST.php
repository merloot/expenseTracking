<?php

use common\models\User;
use common\models\Goods;
use common\models\Check;
use \app\modules\api\models\ApiV1Model;
use app\modules\api\extensions\JResponse;

class CreatePOST extends ApiV1Model{

    public $company_inn;
    public $shift_number;
    public $request_number;
    public $operation_type;
    public $user_id;
    public $nds10;
    public $nds18;
    public $amount;
    public $fiscal_drive_number;
    public $fiscal_document_number;
    public $fiscal_sign;
    public $confirmed;
    public $company;
    public $kkt_reg_id;
    public $seller;
    public $dateTime;


    public function rules() {
        return [
            [['company_inn', 'shift_number', 'request_number', 'operation_type', 'user_id', 'nds10', 'nds18', 'amount'], 'default', 'value' => null],
            [['company_inn', 'shift_number', 'request_number', 'operation_type', 'user_id', 'nds10', 'nds18', 'amount'], 'integer'],
            [['fiscal_drive_number', 'fiscal_document_number', 'fiscal_sign'], 'required'],
            [['date_time'], 'safe'],
            [['confirmed'], 'boolean'],
            [['company', 'kkt_reg_id', 'seller'], 'string', 'max' => 255],
            [['fiscal_drive_number', 'fiscal_document_number', 'fiscal_sign'], 'string', 'max' => 64],
            [['fiscal_document_number'], 'unique'],
            [['fiscal_drive_number'], 'unique'],
            [['fiscal_sign'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function run() {

        $user = User::findOne($this->user_id);
        $basicAuth= base64_encode("$user->phone:$user->code");
        $url = 'https://proverkacheka.nalog.ru:9999/v1/inns/*/kkts/*/fss/'.$this->fiscal_drive_number.'/tickets/'.$this->fiscal_document_number.'?fiscalSign='.$this->fiscal_sign.'&sendToEmail=no';
        $headers = [
            'Accepts: application/json',
            'device-id: 1',
            'device-os: 1',
            'Authorization: Basic '.$basicAuth
        ];

        $curl = curl_init();
        curl_setopt_array($curl,[
            CURLOPT_URL             => $url,
            CURLOPT_HTTPHEADER      => $headers,
            CURLOPT_RETURNTRANSFER  => 1

        ]);

        $response = curl_exec($curl);

        $model = new Check();
        $model->fiscal_sign = $this->fiscal_sign;
        $model->fiscal_document_number= $this->fiscal_document_number;
        $model->fiscal_drive_number = $this->fiscal_drive_number;
        $model->user_id = $this->user_id;
        if (curl_getinfo($curl)['http_code']===406){
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

    public function sendCheck(){
    }
}