<?php

namespace app\modules\api\models\auth;

use app\modules\api\extensions\JResponse;
use common\models\Logs;
use Yii;
use app\modules\api\models\ApiV1Model;
use yii\base\Model;

class LogoutPOST extends Model
{
    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public function run() {
        Yii::$app->user->logout();
        return JResponse::success();
    }
}