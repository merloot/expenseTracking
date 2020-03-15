<?php
/**
 * Created by PhpStorm.
 * User: merloot
 * Date: 15.03.20
 * Time: 16:19
 */

namespace api\controllers;


use common\models\Check;
use yii\filters\ContentNegotiator;
use yii\rest\Controller;

class CheckController extends Controller {

    public $modelClass = 'common\models\Check';


    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']['only'] = ['create', 'update', 'delete'];
        $behaviors['authenticator']['authMethods'] = [
            HttpBasicAuth::className(),
            HttpBearerAuth::className(),
        ];

        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['create', 'update', 'delete'],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }


    public function actionCreate(){
        $model = new Check();
        $model->user_id = \Yii::$app->user->id;

        $model->load(\Yii::$app->getRequest()->getBodyParams(),'');
        if ($model->save()){
            $response = \Yii::$app->getRequest();
            $response->setStatusCode(201);
            $id = implode(',', array_values($model->getPrimaryKey(true)));
            $response->getHeaders()->set('Location', Url::toRoute(['view', 'id' => $id], true));
        }elseif (!$model->hasErrors()){
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
        return $model;
    }

    public function prepareDataProvider(){
        $searchModel = new CheckSearch();
        return $searchModel->search(Yii::$app->request->queryParams);
    }

    public function checkAccess($action, $model = null, $params = []) {
        if (in_array($action, ['update', 'delete'])) {
            if (!Yii::$app->user->can(Rbac::MANAGE_POST, ['post' => $model])) {
                throw  new ForbiddenHttpException('Forbidden.');
            }
        }
    }

}