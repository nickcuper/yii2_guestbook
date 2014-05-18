<?php
/**
 * Account page
 * @var yii\base\View $this
 * @var yii\widgets\ActiveForm $form
 * @var app\modules\users\models\User $model
 */
 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\extensions\fileapi\FileAPIAdvanced;

$this->title = 'Profile Update';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?php echo Html::encode($this->title); ?></h1>
<?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-sm-6">
		    <?= $form->field($model, 'login') .
		         $form->field($model, 'fname') .
		         $form->field($model, 'lname') .
		         $form->field($model, 'phone') .
		         $form->field($model, 'avatar_url')->widget(FileAPIAdvanced::className(), [
		         	'url' => $this->context->module->avatarUrl(),
		         	'deleteUrl' => Url::toRoute('/users/default/delete-avatar'),
		         	'deleteTempUrl' => Url::toRoute('/users/default/deleteTempAvatar'),
	                'crop' => true,
	                'cropResizeWidth' => $this->context->module->avatarWidth,
	                'cropResizeHeight' => $this->context->module->avatarHeight,
	                'settings' => [
	                    'url' => Url::toRoute('uploadTempAvatar'),
	                    'imageSize' =>  [
	                        'minWidth' => $this->context->module->avatarWidth,
	                        'minHeight' => $this->context->module->avatarHeight
	                    ]
	                ]
			     ]) .
		         Html::submitButton(Yii::t('users', 'Save Changes'), ['class' => 'btn btn-primary btn-large pull-right']) ?>
		</div>
	</div>
<?php ActiveForm::end(); ?>