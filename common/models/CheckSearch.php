<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CheckSearch represents the model behind the search form of `common\models\Check`.
 */
class CheckSearch extends Check
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fiscal_drive_number', 'fiscal_document_number', 'fiscal_sign', 'shift_number', 'request_number', 'operation_type', 'user_id', 'nds10', 'nds18', 'amount'], 'integer'],
            [['company', 'company_inn', 'kkt_reg_id', 'date_time', 'seller'], 'safe'],
            [['confirmed'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Check::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'fiscal_drive_number' => $this->fiscal_drive_number,
            'fiscal_document_number' => $this->fiscal_document_number,
            'fiscal_sign' => $this->fiscal_sign,
            'date_time' => $this->date_time,
            'shift_number' => $this->shift_number,
            'request_number' => $this->request_number,
            'operation_type' => $this->operation_type,
            'user_id' => $this->user_id,
            'nds10' => $this->nds10,
            'nds18' => $this->nds18,
            'amount' => $this->amount,
            'confirmed' => $this->confirmed,
        ]);

        $query->andFilterWhere(['ilike', 'company', $this->company])
            ->andFilterWhere(['ilike', 'company_inn', $this->company_inn])
            ->andFilterWhere(['ilike', 'kkt_reg_id', $this->kkt_reg_id])
            ->andFilterWhere(['ilike', 'seller', $this->seller]);

        return $dataProvider;
    }


    public function formName()
    {
        return 's';
    }
}
