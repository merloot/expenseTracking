<?php


namespace api\controllers;


use app\modules\api\extensions\JResponse;
use common\models\Goods;
use yii\filters\ContentNegotiator;
use yii\rest\Controller;

class GoodController extends Controller {

    public $modelClass = 'common\models\Goods';


    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']['only'] = ['create'];
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
            'only' => ['create'],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actions() {
        $actions = parent::actions();
        unset($actions['create']);
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }


    public function actionCreate($check_id){
        $model = new Goods();
        $model->check_id = $check_id;

        $model->load(\Yii::$app->getRequest()->getBodyParams(),'');
        if ($model->save()){
            $response = \Yii::$app->getRequest();
            $response->setStatusCode(201);
            $id = implode(',', array_values($model->getPrimaryKey(true)));
            $response->getHeaders()->set('Location', Url::toRoute(['view', 'id' => $id], true));
        }elseif (!$model->hasErrors()){
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
        return JResponse::success($model);
    }


    public function prepareDataProvider(){
        $searchModel = new CheckSearch();
        return $searchModel->search(Yii::$app->request->queryParams);
    }
}