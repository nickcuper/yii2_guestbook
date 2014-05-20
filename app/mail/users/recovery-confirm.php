<?php
/**
 * Представление отправки подвтерждения восстановления пароля.
 * @var yii\web\View $this Представление
 * @var string $key Ключ активации
 * @var string $email Email адрес
 */

use yii\helpers\Html;

$url = Yii::$app->urlManager->createAbsoluteUrl(['users/default/recoveryPassword', 'key' => $key, 'email' => $email]);
?>
<h3><?= Yii::t('users','Hello!')?></h3>
<p><?= Yii::t('users', 'In order to confirm the password change, you need to link')?>: <?= Html::a(Html::encode($url), $url) ?>.</p>