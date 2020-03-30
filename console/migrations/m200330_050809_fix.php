<?php

use yii\db\Migration;

/**
 * Class m200330_050809_fix
 */
class m200330_050809_fix extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('user','phone');
        $this->addColumn('user','phone','string');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(){
        echo "m200330_050809_fix cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200330_050809_fix cannot be reverted.\n";

        return false;
    }
    */
}
