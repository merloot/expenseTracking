<?php

class Module extends \yii\base\Module {


    public $controllerNamespace = 'app\modules\api\controllers';


    public function beforeAction($action) {
        if (YII_ENV_DEV){
            if(\Yii::$app->request->getMethod() == 'OPTIONS'){
                return false;
            }
        }

        return parent::beforeAction($action);
    }


    public function behaviors() {
        return [];
    }


    public function init() {
        parent::init();
        \Yii::$app->user->enableSession = false;
    }

}