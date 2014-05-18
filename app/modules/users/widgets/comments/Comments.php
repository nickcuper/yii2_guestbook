<?php
namespace app\modules\users\widgets\comments;

use Yii;
use yii\base\Widget;
use app\modules\users\models\Comment;
use app\modules\users\widgets\comments\assets\CommentsAsset;
use app\modules\users\widgets\comments\assets\CommentsGuestAsset;

/**
 * Widget [[Comments]]
 * This widget create process add and show comments on guestbook user
 */
class Comments extends Widget
{
	/**
	 * @var yii\db\ActiveRecord
	 */
	public $model;

	/**
	 * @var string
	 */
	public $title;

	/**
	 * @var string
	 */
	public $sendButtonText;

	/**
	 * @var string
	 */
	public $cancelButtonText;

	/**
	 * @var string
	 */
	public $createButtonTxt;

	/**
	 * @var integer
	 */
	public $maxLevel = 1;
        
        /**
	 * @var integer This param uses via relation.
	 */
        public $user_id;
        
        /**
	 * @inheritdoc
	 */
	public function init()
	{
		if ($this->title === null) {
			$this->title = Yii::t('users', 'Comments');
		}
		if ($this->sendButtonText === null) {
			$this->sendButtonText = Yii::t('users', 'Add');
		}
		if ($this->createButtonTxt === null) {
			$this->createButtonTxt = Yii::t('users', 'Add comment');
		}
		if ($this->cancelButtonText === null) {
			$this->cancelButtonText = Yii::t('users', 'Cancel');
		}
	}

	/**
	 * @inheritdoc
	 */
	public function run()
	{
		$model = self::baseComment();
		$models = $model->getComments();
		$this->registerClientScript();
		
    	echo $this->render('index', [
    		'id' => $this->getId(),
    		'model' => $model,
    		'models' => $models,
    		'title' => $this->title,
    		'level' => 0,
    		'maxLevel' => $this->maxLevel,
    		'sendButtonText' => $this->sendButtonText,
    		'cancelButtonText' => $this->cancelButtonText,
    		'createButtonTxt' => $this->createButtonTxt
    	]);
  	}

  	/**
	 * Register AssetBundle widget.
	 */
	public function registerClientScript()
	{
		$view = $this->getView();
		
			CommentsAsset::register($view);
			$view->registerJs("jQuery('#comment-form').comments();");
		
	}

  	/**
         * Create base structure comment
	 */
  	protected function baseComment()
	{
		$model = new Comment(['scenario' => 'create']);
                $model->from = ($this->user_id) ? $this->user_id : Yii::$app->user->id;
                
                return $model;
	}
}