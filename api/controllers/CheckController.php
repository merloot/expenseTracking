<?php


namespace api\controllers;


use common\models\CheckList;
use yii\web\Response;
use common\models\User;
use common\models\Goods;
use common\models\Check;
use yii\data\Pagination;
use yii\rest\Controller;
use yii\filters\AccessControl;
use common\models\CheckSearch;
use yii\filters\ContentNegotiator;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use common\extensions\JResponse;
use backend\modules\api\models\check\ListGET;

class CheckController extends Controller {

    public $modelClass = 'common\models\Check';


    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']['only'] = ['create', 'all', 'one'];
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
            'only' => ['create', 'all', 'one'],
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

    public function actionIndex(){
        return false;
    }

    public function actionCreate(){

        $user = User::findOne(\Yii::$app->user->id);
        $basicAuth = base64_encode("$user->phone:$user->code");
        $url = 'https://proverkacheka.nalog.ru:9999/v1/inns/*/kkts/*/fss/'
            .\Yii::$app->request->post('fiscal_drive_number').
            '/tickets/'.\Yii::$app->request->post('fiscal_document_number').
            '?fiscalSign='.\Yii::$app->request->post('fiscal_sign').
            '&sendToEmail=no';

        $headers = [
            'Accepts: application/json',
            'device-id: 1',
            'device-os: 1',
            'Authorization: Basic '.$basicAuth
        ];

        $curl = curl_init();
        curl_setopt_array($curl,[
            CURLOPT_URL             => $url,
            CURLOPT_HTTPHEADER      => $headers,
            CURLOPT_RETURNTRANSFER  => 1

        ]);

        $response = curl_exec($curl);

        $model = new Check();
        if($model->load(\Yii::$app->request->post())&& $model->validate()){
        $model->user_id = $user->id;
        if (curl_getinfo($curl)['http_code']=== 406){
            $model->confirmed = false;
            $model->save();
        }else{
            $check = Json::decode($response);

            $model->company_inn     = $check['document']['receipt']['userInn'];
            $model->company         = $check['document']['receipt']['user'];
            $model->shift_number    = $check['document']['receipt']['shiftNumber'];
            $model->request_number  = $check['document']['receipt']['requestNumber'];
            $model->operation_type  = $check['document']['receipt']['operationType'];
            $model->nds10           = $check['document']['receipt']['nds10'];
            $model->nds18           = $check['document']['receipt']['nds18'];
            $model->amount          = $check['document']['receipt']['totalSum'];
            $model->confirmed       =true;
            $model->kkt_reg_id      =$check['document']['receipt']['kktRegId'];
            $model->seller          =$check['document']['receipt']['operator'];
            $model->date_time       =date('Y-m-d H:i:s',strtotime($check['document']['receipt']['dateTime']));
            if (!$model->save()){
                return JResponse::error($model->getErrors());
            }
            foreach ($check['document']['receipt']['items'] as $item){
                $good = new Goods();
                $good->name     = $item['document']['receipt']['name'];
                $good->amount   = $item['document']['receipt']['price'];
                $good->check_id = $model->id;
                $good->count    = $item['document']['receipt']['quantity'];
                $good->price    = $item['document']['receipt']['sum'];
                if (!$good->save()){
                    return JResponse::error($good->getErrors());
                }
            }
        }
        return JResponse::success($model);
        }
        return JResponse::error($model->getErrors());
    }

//    public function actionCreate(){
//        $model = new Check();
//        $model->user_id = \Yii::$app->user->id;
//
//        $model->load(\Yii::$app->getRequest()->getBodyParams(),'');
//        if ($model->save()){
//            $response = \Yii::$app->getRequest();
//            $response->setStatusCode(201);
//            $id = implode(',', array_values($model->getPrimaryKey(true)));
//            $response->getHeaders()->set('Location', Url::toRoute(['view', 'id' => $id], true));
//        }elseif (!$model->hasErrors()){
//            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
//        }
//        return $model;
//    }


    public function actionAll(){

        $model = new CheckList();
        $model->load(\Yii::$app->request->get(),'');
//        if($model->validate()){
            return JResponse::success($model->run());
//        } else {
//            JResponse::error($model->getErrors());
//        }
//        $searchCheck = new CheckSearch();
//        $dataProvider = $searchCheck->search(\Yii::$app->request->queryParams);
//        $pages = new Pagination([
//            'totalCount'    => $dataProvider->query->count(),
//            'pageSize'      => 10,
//        ]);
//
//        $checks = $dataProvider
//            ->query
//            ->offset($pages->offset)
//            ->limit($pages->limit)
//            ->orderBy(['id'=>SORT_DESC])
//            ->all();
//
//        return JResponse::success([
//            'checks'        => $checks,
//            'searchCheck'   => $searchCheck,
//            'pages'         => $pages,
//
//        ]);
    }


    public function actionOne($check_id){
        $check = Check::find()
            ->where(['id'=>$check_id])
            ->andWhere(['user_id'=>\Yii::$app->user->id])
            ->joinWith('goods')
            ->asArray()
            ->one();
        if(!$check){
            return JResponse::error('Check not found');

        }
        return JResponse::success($check);
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