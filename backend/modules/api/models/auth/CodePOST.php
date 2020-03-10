<?php

use common\models\LoginForm;
use app\modules\api\models\ApiV1Model;
use app\modules\api\extensions\JResponse;

class CodePOST extends ApiV1Model {

    public $email;
    public $password;
    public $rememberMe = true;

    public function rules(){
        return [
            // username and password are both required
            [['email', 'password'], 'required'],
            [['email'], 'email'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'string'],
        ];
    }

    public function run(){
        $model = new LoginForm();
        $model->email = $this->email;
        $model->password = $this->password;
        $model->rememberMe = $this->rememberMe;
        if ($model->validate()){
            return JResponse::success();
        }
        return JResponse::error();
    }
}