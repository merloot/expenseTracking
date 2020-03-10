<?php

use common\models\LoginForm;
use app\modules\api\models\ApiV1Model;
use app\modules\api\extensions\JResponse;

class LoginPOST extends ApiV1Model {

    public $username;
    public $password;
    public $rememberMe = true;

    public function rules(){
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
//            [['username'], 'email'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'string'],
        ];
    }

    public function run(){
        $model = new LoginForm();
        $model->username = $this->username;
        $model->password = $this->password;
        $model->rememberMe = $this->rememberMe;
        if ($model->login()){
            return JResponse::success();
        }
        return JResponse::error();
    }
}