<?php

use common\models\Check;
use \app\modules\api\models\ApiV1Model;
use app\modules\api\extensions\JResponse;

class OneGET extends ApiV1Model {

    public $check_id;


    public function rules() {
        return [
            ['id', 'required'],
            ['id', 'integer'],
            ['id', 'min'=>1, 'max'=> SQL_INT_MAX]
        ];
    }


    function run() {
        $check = Check::find()->where(['id'=>$this->check_id])->joinWith('goods')->asArray()->one();
        if (!$check){
            return JResponse::error('Check not found');
        }else{
            return JResponse::success([
                'check' => $check
            ]);
        }
    }
}