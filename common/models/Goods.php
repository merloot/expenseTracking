<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "goods".
 *
 * @property string|null $name
 * @property int|null $amount
 * @property int|null $count
 * @property int|null $price
 * @property int|null $check_id
 *
 * @property Check $check
 */
class Goods extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'goods';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['name'], 'string'],
            [['amount', 'count', 'price', 'check_id'], 'default', 'value' => null],
            [['amount', 'price', 'check_id'], 'integer'],
            ['count','string'],
            [['check_id'], 'exist', 'skipOnError' => true, 'targetClass' => Check::className(), 'targetAttribute' => ['check_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'amount' => 'Amount',
            'count' => 'Count',
            'price' => 'Price',
            'check_id' => 'Check ID',
        ];
    }

    /**
     * Gets query for [[Check]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCheck(){
        return $this->hasOne(Check::className(), ['id' => 'check_id']);
    }
}
