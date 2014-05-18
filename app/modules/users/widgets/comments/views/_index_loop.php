<?php
/**
 * @var yii\base\View $this
 * @var app\modules\users\models\Comment $models
 * @var integer $level
 * @var integer $maxLevel
 */

if ($models) {
	foreach ($models as $model) {
		echo $this->render('_index_item', [
			'model' => $model,
			'level' => $level,
			'maxLevel' => $maxLevel
		]);
	}
}