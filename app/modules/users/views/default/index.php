<?php
/**
 * Page of my partners
 * @var yii\base\View $this
 * @var app\modules\users\models\User $dataProvider
 */

use yii\helpers\Html;

$this->title = Yii::t('users', 'Partners');
$this->params['breadcrumbs'][] = $this->title;
$this->params['page-id'] = 'users';
?>
<h1><?php echo Html::encode($this->title); ?></h1>
<?= $this->render('_index_loop', [
	'dataProvider' => $dataProvider
]); ?>