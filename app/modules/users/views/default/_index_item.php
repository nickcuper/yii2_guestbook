<?php
/**
 * Шаблон одного пользователя на странице всех записей.
 * @var yii\base\View $this
 * @var common\modules\users\models\User $model
 */

use yii\helpers\Html;
?>
<div class="user-avatar">
    <?= Html::img($model->avatar) ?>
</div>
<h3><?= Html::a($model->fio, ['guestbook', 'username' => $model['login']]) ?></h3>