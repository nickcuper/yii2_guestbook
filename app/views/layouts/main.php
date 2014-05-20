<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\widgets\NavBarLangList\NavBarLangList;
use app\widgets\alert\Alert;
/**
 * @var \yii\web\View $this
 * @var string $content
 */
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>
    <div class="wrap">
        <?php

            NavBar::begin([
                'brandLabel' => 'Yii2 Guestbook',
                'brandUrl' => '/',
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);

            if (Yii::$app->user->isGuest) {
                $menuItems = [
                    ['label' => Yii::t('users', 'Register'), 'url' => ['/signup']],
                    ['label' => Yii::t('users', 'Login'), 'url' => ['/login']]
                ];

            } else {
                $menuItems = [
                    [
                        'label' => Yii::t('users', 'My Account'),
                        'url' => ['/users/default/update'],
                    ],
                    [
                        'label' => Yii::t('users', 'My GuestBook <span class="badge {class}">{count}</span>', ['count' => Yii::$app->user->identity->countreplies, 'class' => 'label-info']),
                        'url' => ['/guestbook'],

                    ],
                    [
                        'label' => Yii::t('users', 'My Partners'),
                        'url' => ['/partners'],
                    ],
                    [
                        'label' => Yii::t('users', 'Logout [{name}]' , ['name' => Yii::$app->user->identity->login]),
                        'url' => ['/logout'],

                    ],

                ];

            }

            echo NavBarLangList::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $menuItems,
                'encodeLabels'=>false,
                'langList' => true,
            ]);
            NavBar::end();

        ?>

        <div class="container">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= Alert::widget();?>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; My Company <?= date('Y') ?></p>
            <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
