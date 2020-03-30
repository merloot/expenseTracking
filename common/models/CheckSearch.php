<?php

namespace common\models;


use yii\base\Model;
use yii\data\ActiveDataProvider;

class CheckSearch extends Check {

    public function rules() {
        return[
            [['id','user_id','date_time'],'safe']
        ];
    }

    public function scenarios() {
        return Model::scenarios();
    }

    public function search($params){
        $query = Check::find()->where(['confirmed' => true])->andWhere(['user_id' =>$this->user_id]);

        $dataProvider = new ActiveDataProvider([
            'query'=> $query,
        ]);

        $this->load($params);
        if (!$this->validate()){
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'date_time' => $this->date_time,
        ]);

        return $dataProvider;
    }
}