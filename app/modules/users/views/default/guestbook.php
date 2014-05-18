<?php
/**
 * GuestBook Page
 * @var yii\base\View $this
 * @var yii\widgets\ActiveForm $form
 * @var app\modules\users\models\LoginForm $model
 */

use yii\helpers\Html;
use app\modules\users\widgets\comments\Comments;

$this->title = Yii::t('users', '{who} GuestBook',['who' => ($login) ? $login : 'My']);
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?php echo Html::encode($this->title); ?></h1>


    <div class="row">
        <div class="col-sm-8">
		   <?= Comments::widget(['user_id' => $user_id]) ?>
		</div>
	</div>