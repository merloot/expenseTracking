<?php

namespace api\controllers;

use Yii;
use yii\rest\Controller;
use common\models\LoginForm;
use yii\filters\ContentNegotiator;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

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
