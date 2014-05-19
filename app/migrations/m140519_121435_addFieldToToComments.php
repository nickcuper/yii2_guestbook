<?php

use yii\db\Schema;

class m140519_121435_addFieldToToComments extends \yii\db\Migration
{
    public function up()
    {
        $this->addColumn('comments', 'to', 'INT(10) DEFAULT "0"');
    }

    public function down()
    {
        echo "m140519_121435_addFieldToToComments cannot be reverted.\n";

        return false;
    }
}
