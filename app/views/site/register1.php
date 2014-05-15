<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var app\models\Users $model
 * @var ActiveForm $form
 */
?>
<div class="register1">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'login') ?>
        <?= $form->field($model, 'password') ?>
        <?= $form->field($model, 'email') ?>
        <?= $form->field($model, 'is_active') ?>
        <?= $form->field($model, 'role_id') ?>
        <?= $form->field($model, 'state_id') ?>
        <?= $form->field($model, 'date_register') ?>
        <?= $form->field($model, 'address') ?>
        <?= $form->field($model, 'fname') ?>
        <?= $form->field($model, 'lname') ?>
        <?= $form->field($model, 'city') ?>
        <?= $form->field($model, 'phone_cell') ?>
        <?= $form->field($model, 'phone_work') ?>
        <?= $form->field($model, 'zip') ?>
    
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- register1 -->
