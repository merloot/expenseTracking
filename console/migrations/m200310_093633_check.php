<?php

use yii\db\Migration;

/**
 * Class m200310_093633_check
 */
class m200310_093633_check extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('check',[
            'id'                    =>$this->primaryKey(),
            'company'               => $this->string(255),
            'company_inn'           => $this->string(255),
            'kkt_reg_id'            => $this->string(255),
            'fiscal_drive_number'   => $this->bigInteger()->notNull(),
            'fiscal_document_number'=> $this->bigInteger()->notNull(),
            'fiscal_sign'           => $this->bigInteger()->notNull(),
            'date_time'             => $this->timestamp(),
            'seller'                => $this->string(255),
            'shift_number'          => $this->integer(64),
            'request_number'        => $this->integer(64),
            'operation_type'        => $this->smallInteger(),
            'user_id'               => $this->integer(),
            'nds10'                 => $this->integer(),
            'nds18'                 => $this->integer(),
            'amount'                => $this->integer(),
            'confirmed'             => $this->boolean(),

        ]);

//        $this->addPrimaryKey('check_pk','check',['fiscal_drive_number','fiscal_document_number','fiscal_sign']);
        $this->addForeignKey('CheckUser','check','user_id','user','id','CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropForeignKey('CheckUser','check');
        $this->dropTable('check');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200310_093633_check cannot be reverted.\n";

        return false;
    }
    */
}
