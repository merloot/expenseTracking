<?php
require __DIR__ . '/../../vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__ . '/../../');
$dotenv->load();
// Для дебага добавьте YII_ENV='dev' and YII_DEBUG='true' в свой .env file
// YII_ENV
if (getenv('YII_ENV')==='dev') defined('YII_ENV') or define('YII_ENV', 'dev');
else defined('YII_ENV') or define('YII_ENV', 'prod');
// YII_DEBUG
if (getenv('YII_DEBUG')==='true') defined('YII_DEBUG') or define('YII_DEBUG', true);
else defined('YII_DEBUG') or define('YII_DEBUG', false);
// Для теста можно запустить yii test/env

require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../../common/config/bootstrap.php';
require __DIR__ . '/../config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/main.php',
    require __DIR__ . '/../../common/config/main-local.php',
    require __DIR__ . '/../config/main.php',
    require __DIR__ . '/../config/main-local.php'
);

(new yii\web\Application($config))->run();
