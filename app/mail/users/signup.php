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
<h3><?= Yii::t('users','Hello!')?></h3>
<p><?= Yii::t('users', 'In order to complete the registration process you need to confirm your email address, for this you need to link')?>: <?= Html::a(Html::encode($url), $url) ?>.</p>