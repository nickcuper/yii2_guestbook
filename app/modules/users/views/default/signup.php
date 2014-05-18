<?php
/**
 * Register Page
 * @var yii\base\View $this
 * @var yii\widgets\ActiveForm $form
 * @var app\modules\users\models\User $model
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

use app\models\Country;
use app\widgets\alert\Alert;


$this->title = Yii::t('users', 'Register');
$this->params['breadcrumbs'][] = $this->title;
$this->params['page-id'] = 'signup';

$this->registerJs("jQuery(document).on('change', '#user-country_id', function(evt) {
    contry = parseInt(jQuery(this).find('option:selected').val());
    jQuery.post('state',{country_id:contry}, function(data) {
        if (data) {
            var state = jQuery.parseJSON(data);
            var htmlOptions = '';
            
            for (i=0; i<state.length; i++) {
                htmlOptions += '<option value='+state[i].state_id+'>'+state[i].name+'</option>';
            }
            jQuery( '#user-state_id' ).html(htmlOptions);
        }
    });

});"      
, \yii\web\VIEW::POS_READY);

?>
<h1><?php echo Html::encode($this->title); ?></h1>

<?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-sm-5">
	        <?= $form->field($model, 'login') .
	            $form->field($model, 'fname') .
	            $form->field($model, 'lname') .
	            $form->field($model, 'phone') .
	            $form->field($model, 'wmr') .
                    $form->field($model, 'email') .
                    $form->field($model, 'country_id')->dropDownList(
                        ArrayHelper::map(Country::find()->all(), 'country_id', 'name'),
                        ['prompt'=> Yii::t('app', '- Select Country -') ]    // empty options
                    ) .
                    $form->field($model, 'state_id')->dropDownList(
                        [],          
                        ['prompt'=> Yii::t('app', '- Please select country now -') ]    // empty options
                    ) .
	            $form->field($model, 'password')->passwordInput() . 
                    $form->field($model, 'repassword')->passwordInput() ?>
        
    
    <?= Html::submitButton(Yii::t('users', 'Registered'), ['class' => 'btn btn-success btn-large pull-right']);?></div>
<?php ActiveForm::end(); ?>