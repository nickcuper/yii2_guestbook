<?php

use yii\db\Schema;

class m140521_152822_createStatisticReadTable extends \yii\db\Migration
{
    public function up()
    {
                    $createTable = <<<SQL
                    CREATE TABLE IF NOT EXISTS `statistic_read` (
  `statistic_read_id` int(11) NOT NULL AUTO_INCREMENT,
  `random_var` varchar(255) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`statistic_read_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
SQL;
            $this->execute($createTable);

            
    }

    public function down()
    {
        echo "m140521_152822_createStatisticReadTable cannot be reverted.\n";

        return false;
    }
}
