<?php

/*
 * Show Comments List
 */



    echo Comments::widget([
        'model' => $model,
        'maxLevel' => Yii::$app->getModule('comments')->maxLevel
    ]);

?>
