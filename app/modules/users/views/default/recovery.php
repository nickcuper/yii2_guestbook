<?php
/**
 * Recovery password page
 * @var yii\base\View $this
 * @var yii\widgets\ActiveForm $form
 * @var app\modules\users\models\User $model
 */
 
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('users', 'Recovery Password');
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?php echo Html::encode($this->title); ?></h1>
<?php $form = ActiveForm::begin();
    echo $form->field($model, 'email').
         Html::submitButton(Yii::t('users', 'Send'), ['class' => 'btn btn-success pull-right']);
ActiveForm::end(); ?>