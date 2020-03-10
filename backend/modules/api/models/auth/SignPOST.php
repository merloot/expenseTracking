<?php

use frontend\models\SignupForm;
use app\modules\api\models\ApiV1Model;
use app\modules\api\extensions\JResponse;

class SignPOST extends ApiV1Model {

    public $username;
//    public $email;
    public $password;
    public $phone;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            ['phone', 'required'],
            ['phone', 'integer', 'min'=>10, 'max' => 10],
            ['phone', 'trim'],
            ['phone', 'unique', 'targetClass' => 'common\models\User', 'message' =>'This phone has already been taken'],

        ];
    }
    public function run(){
        $model = new SignupForm();
//        $model->email = $this->email;
        $model->username = $this->username;
        $model->password = $this->password;
        $model->phone = $this->phone;
        if ($model->validate()){
            return JResponse::success();
        }
        return JResponse::error($model->getErrors());
    }
}