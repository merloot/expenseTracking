<?php

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

        //Не тестировал, не работает, подумать

        $NALOG = 'https://proverkacheka.nalog.ru:9999/v1/inns/*/kkts/*/fss/9282000100367383/tickets/19746?fiscalSign='.$this->fiscal_sign.'&sendToEmail=no';

        $data = [
            'fiscal_drive_number'       =>$this->fiscal_drive_number,
            'fiscal_document_number'    =>$this->fiscal_document_number,
            'fiscal_sign'               =>$this->fiscal_sign,
            'user_id'                   =>$this->user_id,

            'company_inn'               =>$this->company_inn,
            'shift_number'              =>$this->shift_number,
            'request_number'            =>$this->request_number,
            'operation_type'            =>$this->operation_type,
            'nds10'                     =>$this->nds10,
            'nds18'                     =>$this->nds18,
            'amount'                    =>$this->amount,
            'confirmed'                 =>$this->confirmed,
            'company'                   =>$this->company,
            'kkt_reg_id'                =>$this->kkt_reg_id,
            'seller'                    =>$this->seller,
        ];

        $model = new Check();
        $model->attributes = $data;

        if ($model->validate()){
            return JResponse::success();
        }else{
            return JResponse::error($model->getErrors());
        }
    }

    public function sendCheck(){


    }
}