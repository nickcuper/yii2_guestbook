<?php
/**
 * @var yii\base\View $this 
 * @var app\modules\users\models\Comment $model 
 * @var app\modules\users\models\Comment $models 
 * @var string $title 
 * @var integer $level 
 * @var integer $maxLevel
 */
?>

<?php $isShowCommentForm = (Yii::$app->user->id == $model->from); ?>

<div id="comments-widget">
    <h3><?= $title ?></h3>
    
    <div class="comments">
	    <?= $this->render('_index_loop', [
	    	'models' => $models,
	    	'level' => $level,
	    	'maxLevel' => $maxLevel
	    ]) ?>
    </div>
    <?php if (Yii::$app->user->id == $model->from) ?>
        <div class="<?= (!$isShowCommentForm) ? 'hidden': ''?>">
            <?= $this->render('_form', [
                'model' => $model,
                'is_answer' => (int)(!$isShowCommentForm), 
                'sendButtonText' => $sendButtonText,
                'cancelButtonText' => $cancelButtonText
            ]) ?>
       </div>
    
</div>