<?php

namespace api\controllers;

use Yii;
use yii\rest\Controller;
use common\models\LoginForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */

    protected function verbs(){
        return[
            'login' =>['post'],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {
        return 'api';
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin() {
        $model = new LoginForm();
        $model->load(Yii::$app->request->bodyParams,'');
        if($token = $model->auth()){
            return $token;
        } else {
            return $model;
        }
    }

}
