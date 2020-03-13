<?php

use common\models\Check;
use \app\modules\api\models\ApiV1Model;
use app\modules\api\extensions\JResponse;

class ListGET extends ApiV1Model{

    public $page;
    public $limit;
    public $dateTo;
    public $user_id;
    public $dateFrom;
    public $confirmed;


    public function rules() {
        return [
            ['user_id','required'],
            ['user_id','integer'],
            ['user_id','min'=>1, 'max'=>SQL_INT_MAX],

            ['confirmed','boolean'],

            [['dateFrom'], 'date', 'format' => 'php:Y-m-d'],
            [['dateFrom'], 'default', 'value' => null],

            [['dateTo'], 'date', 'format' => 'php:Y-m-d'],
            [['dateTo'], 'default', 'value' => null],


            ['page', 'integer'],
            ['page', 'min' => 1],

            ['limit', 'integer'],
            ['limit', 'default', 'value' => 10],
        ];
    }

    function run() {
        $query = Check::find();
        if ($this->confirmed) $query->where(['confirmed' =>$this->confirmed]);
        $offset = ($this->page-1) * $this->limit;

        if (!empty($this->dateFrom)) $query->andWhere(['>=','date_time',$this->dateFrom]);
        if (!empty($this->dateTo)) $query->andWhere(['<=','date_time',$this->dateTo]);

        $query->offset($offset)->limit($this->limit);
        $count = $query->count();

        $maxPage = ceil($count/ $this->limit);

        $checks = $query->select([''])->orderBy(['date_time'])->asArray()->all();

        return JResponse::success([
            'list'      => $checks,
            'maxPage'   => $maxPage,
            'count'     => $count
        ]);
    }
}