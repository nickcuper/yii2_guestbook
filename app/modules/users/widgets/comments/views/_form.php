<?php
/**
 * @var yii\base\View $this
 * @var frontend\modules\comments\models\Comment $model
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
    'id' => 'comment-form',
    'action' => ['/comments'],
    'fieldConfig' => [
        'template' => "{input}\n{hint}\n{error}"
    ],
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'validateOnChange' => false
]);
    echo $form->field($model, 'body')->textarea() .
         Html::activeHiddenInput($model, 'from') .
         Html::activeHiddenInput($model, 'parent_id') .
         Html::hiddenInput('level', null, ['id' => 'comment-level']) .
         Html::hiddenInput('is_answer', $is_answer) .
         Html::submitInput($sendButtonText, [
            'class' => 'btn btn-primary pull-right'
         ]) .
         Html::button($cancelButtonText, [
            'class' => 'btn btn-link cancel pull-right'
         ]);
ActiveForm::end();