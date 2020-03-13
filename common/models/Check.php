<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "check".
 *
 * @property int $id
 * @property string|null $company
 * @property int|null $company_inn
 * @property string|null $kkt_reg_id
 * @property string $fiscal_drive_number
 * @property string $fiscal_document_number
 * @property string $fiscal_sign
 * @property string|null $date_time
 * @property string|null $seller
 * @property int|null $shift_number
 * @property int|null $request_number
 * @property int|null $operation_type
 * @property int|null $user_id
 * @property int|null $nds10
 * @property int|null $nds18
 * @property int|null $amount
 * @property bool|null $confirmed
 *
 * @property User $user
 */
class Check extends ActiveRecord {
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'check';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['company_inn', 'shift_number', 'request_number', 'operation_type', 'user_id', 'nds10', 'nds18', 'amount'], 'default', 'value' => null],
            [['shift_number', 'request_number', 'operation_type', 'user_id', 'nds10', 'nds18', 'amount','fiscal_drive_number', 'fiscal_document_number', 'fiscal_sign'], 'integer'],
            [['fiscal_drive_number', 'fiscal_document_number', 'fiscal_sign'], 'required'],
            [['date_time'], 'safe'],
            [['confirmed'], 'boolean'],
            [['company', 'company_inn', 'kkt_reg_id', 'seller'], 'string', 'max' => 255],
            [['fiscal_document_number', 'fiscal_drive_number','fiscal_sign'], 'unique', 'targetAttribute' => ['fiscal_document_number', 'fiscal_drive_number','fiscal_sign']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'company' => 'Company',
            'company_inn' => 'Company Inn',
            'kkt_reg_id' => 'Kkt Reg ID',
            'fiscal_drive_number' => 'Fiscal Drive Number',
            'fiscal_document_number' => 'Fiscal Document Number',
            'fiscal_sign' => 'Fiscal Sign',
            'date_time' => 'Date Time',
            'seller' => 'Seller',
            'shift_number' => 'Shift Number',
            'request_number' => 'Request Number',
            'operation_type' => 'Operation Type',
            'user_id' => 'User ID',
            'nds10' => 'Nds10',
            'nds18' => 'Nds18',
            'amount' => 'Amount',
            'confirmed' => 'Confirmed',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getGoods(){
        return $this->hasMany(Goods::className(),['check_id'=>'id']);
    }

    public static function getCheckByFiscalDocumentNumber($fiscalDocumentNumber){
        return self::find()->where(['fiscal_document_number'=>$fiscalDocumentNumber])->one();
    }
}
