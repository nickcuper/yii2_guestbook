<?php
/**
 * Login page
 * @var yii\base\View $this
 * @var yii\widgets\ActiveForm $form
 * @var app\modules\users\models\LoginForm $model
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('users', 'Login');
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?php echo Html::encode($this->title); ?></h1>
<?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-sm-6">
		    <?= $form->field($model, 'username').
		         $form->field($model, 'password')->passwordInput().
		         $form->field($model, 'rememberMe')->checkbox().
		         Html::submitButton(Yii::t('users', 'Login'), ['class' => 'btn btn-primary']) . 
		         '&nbsp;' .
		         Yii::t('users', 'or') .
		         '&nbsp;' .
		         Html::a(Yii::t('users', 'Recovery password'), ['recovery']) ?>
		</div>
	</div>
<?php ActiveForm::end(); ?>