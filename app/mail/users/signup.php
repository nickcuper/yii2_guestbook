<?php
/**
 * Send Activate key
 * @var yii\web\View $this
 * @var string $key
 * @var string $email
 */

use yii\helpers\Html;

$url = Yii::$app->urlManager->createAbsoluteUrl(['users/default/activation', 'key' => $key, 'email' => $email]);
?>
<h3>Здравствуйте!</h3>
<p>Для того чтобы закончить процесс регистрации вам нужно подтвердить ваш электронный адрес, для этого вы должны перейти по ссылке: <?= Html::a(Html::encode($url), $url) ?>.</p>