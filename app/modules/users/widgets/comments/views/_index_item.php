<?php
/**
 * @var yii\base\View $this
 * @var integer $level
 * @var integer $maxLevel
 */

use yii\helpers\Html;

$class = 'row comment lvl-' . $level;
$relation = ($model['to']) ? 'reciever' : 'user';
?>

<div class="<?= $class ?>" id="comment-<?= $model['comment_id'] ?>" data-parent="<?= $model['parent_id'] ?>">
	<div class="col-sm-3">
	    <?= Html::img($model->$relation->avatar) ?>
	</div>
	<div class="col-sm-7">
		<p class="author">
			<?php echo $model->$relation->getFio(true);?>
		</p>
		<?php  if (Yii::$app->user->id != $model['from'] && !$model['parent_id']) { ?>
                    <p class="manage">
                        <span>&nbsp;&#8212;&nbsp;</span>
                        <a href="#" class="reply" data-id="<?= $model['comment_id'] ?>" data-level="<?= $level ?>">
                            <?= Yii::t('users', 'Reply'); ?>
                        </a>
                    </p>
                <?php  } ?>
	    <div class="date">
		    <?php if ($model['parent_id']) { ?>
		        <?= Html::a('<span class="glyphicon glyphicon-arrow-up"></span>', '#comment-' . $model['parent_id'], ['class' => 'parent-link']) ?>
		    <?php } ?>
		    
	    </div>
	    <div class="content"><?= $model['body'] ?></div>
	</div>
</div>

<?php 

if ($model->children) {
	if ($level < $maxLevel) {
		$level++;
	}
	echo $this->render('_index_loop', [
		'models' => $model->children,
		'level' => $level,
		'maxLevel' => $maxLevel
	]);
}