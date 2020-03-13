<?php

use yii\db\Migration;

/**
 * Class m200310_100500_goods
 */
class m200310_100500_goods extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('goods',[
            'id'        =>  $this->bigPrimaryKey(),
            'name'      =>  $this->text(),
            'amount'    =>  $this->integer(),
            'count'     =>  $this->string(),
            'price'     =>  $this->integer(),
            'check_id'  =>  $this->integer(),
        ]);

        $this->addForeignKey('GoodsCheck','goods','check_id','check','id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropForeignKey('GoodsCheck','goods');
        $this->dropTable('goods');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200310_100500_goods cannot be reverted.\n";

        return false;
    }
    */
}
