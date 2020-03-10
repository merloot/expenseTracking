<?php

namespace app\modules\api\controllers;


use Yii;
use yii\web\Response;
use yii\web\Controller;
use yii\base\Exception;
use app\modules\api\models\ApiModel;
use app\modules\api\extensions\JResponse;
use app\modules\api\helpers\StringConversion;

final class DefaultController extends Controller
{
    public $data;
    public $pk = false;

    public function behaviors() {
        return [
            'basicAuth' => [
                'class' => \yii\filters\auth\HttpHeaderAuth::className(),
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
            header('Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT');
        }
        if (YII_ENV_DEV) {
            if (\Yii::$app->request->getMethod() == 'OPTIONS') {
                return false;
            }
        }
        \Yii::$app->request->enableCsrfValidation = false;

        return parent::beforeAction($action);
    }

    public function actionIndex($method,$action) {

        \Yii::$app->response->format = Response::FORMAT_JSON;

        if ($method == 'GET'){
            $this->data = Yii::$app->request->get();
        } elseif ($method == 'POST'){
            $this->data = Yii::$app->request->post();
        }

        $path = $this->classPathFormation($method,$action);

        if (!class_exists($path)) {
            throw new Exception('Not found: '.$path, 404);
        }
        $Model = new $path();

        if ($this->pk) {
            $Model->setPK($this->pk);
        }

        $Model->setAttributes($this->data);

        if (!$Model->validate()) {
            return  JResponse::error($Model->getErrors());
        }

        try {
            $result = $Model->run();
        } catch (Exception $e) {
            return  JResponse::error($e->getMessage());

        }

        if (!$result) {
            return  JResponse::error([]);
        }
        return $result;
    }

    /* формирует путь класса */
    protected function classPathFormation($method,$action) {
        return 'app\modules\api\models' . '\\' .
            StringConversion::toCamelNotation($action).strtoupper($method);
    }

}