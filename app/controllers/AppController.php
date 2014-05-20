<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\modules\users\models\Comment;

class AppController extends Controller
{

    public function init()
    {
        parent::init();

        // update counter
        $this->on('beforeAction', function($event) {
                if (!Yii::$app->user->isGuest)  {
                    Yii::$app->user->identity->countreplies = Comment::CountReply();
                }
        });

        if (isset($_POST['_lang']) && Yii::$app->request->getIsAjax())
        {
                Yii::$app->language = $_POST['_lang'];
                Yii::$app->session['_lang'] = Yii::$app->language;
        }
        else if (isset(Yii::$app->session['_lang']))
        {
                Yii::$app->language = Yii::$app->session['_lang'];
        }

    }

}
