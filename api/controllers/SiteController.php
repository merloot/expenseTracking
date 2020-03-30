<?php

namespace api\controllers;

use frontend\models\SignupForm;
use Yii;
use yii\web\Response;
use yii\rest\Controller;
use common\models\LoginForm;
use yii\filters\ContentNegotiator;

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
            'login'=>['post'],
            'sign'=>['post'],
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

    public function actionSign(){
        $model = new SignupForm();
        $model->load(Yii::$app->request->bodyParams,'');
        if ($model->signup()){
            return $model;
        }
        return $model->getErrors();
    }

}
