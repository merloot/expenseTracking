<?php

namespace app\modules\api\models;

use yii\base\Model;

/**
 *
 * От этого класса зависит
 *
 * @package app\modules\api\models
 */
abstract class ApiV1Model extends Model {

    abstract function run();

}
