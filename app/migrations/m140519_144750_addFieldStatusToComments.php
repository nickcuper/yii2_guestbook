<?php

use yii\db\Schema;

class m140519_144750_addFieldStatusToComments extends \yii\db\Migration
{
    public function up()
    {
        $this->addColumn('comments', 'status', 'TINYINT(1) DEFAULT "0"');
    }

    public function down()
    {
        echo "m140519_144750_addFieldStatusToComments cannot be reverted.\n";

        return false;
    }
}
