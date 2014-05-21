<?php

use yii\db\Schema;

class m140521_160036_addRandomDataToStatisticTable extends \yii\db\Migration
{
    public function up()
    {
        $countValue = 1000;

        for($i=0; $i<=$countValue;$i++) {
            $randValue = substr(hash('sha256', mt_rand()), 0, 50);
            $sql = <<<SQL
                    INSERT INTO `guestbook`.`statistic_read` (
`random_var`
)
VALUES (
"$randValue"
);
SQL;
            $this->execute($sql);

            if ($i%100==0) {
                echo 'I`m Sleep';
                sleep(5);
            }
        }


    }

    public function down()
    {
        echo "m140521_160036_addRandomDataToStatisticTable cannot be reverted.\n";

        return false;
    }
}
