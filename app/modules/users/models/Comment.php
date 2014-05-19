<?php
namespace app\modules\users\models;

use Yii;
use yii\db\ActiveRecord;

use app\modules\users\models\User;
use app\modules\users\models\query\CommentQuery;

/**
 * Class Comment
 * @package app\modules\users\models
 * Model Comments.
 *
 * @property integer $comment_id
 * @property integer $parent_id
 * @property integer $to Reciver.
 * @property integer $from Author.
 * @property integer $status
 * @property string $body
 * @property date $date_create
 * 
 */
class Comment extends ActiveRecord
{
	/**
	 * Status comments
	 */
	const STATUS_UNREAD = 0;
        const STATUS_READ = 1;
	
        /**
	 * Key count answer cache
	 */
	const CACHE_USER_COUNT_MESSAGE = 'countMessage';
        
        /**
	 * Key count answer cache
	 */
	const CACHE_USER_COUNT_NEW_MESSAGE = 'newcountMessage';
        
	/**
	 * @var Uses for store structure 
	 */
	protected $_children;

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			
		];
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'comments';
	}

	/**
	 * @return array or NULL.
	 */
	public function getChildren()
	{
		return $this->_children;
	}

	/**
	 * Set value $_childs.
	 */
	public function setChildren($value)
	{
		$this->_children = $value;
	}

	/**
	 * Select All comments.
	 * @return yii\db\ActiveRecord
	 */
	public function getComments() 
        {
                $comments = self::find()
                            ->where(['from' => $this->from])
                            ->orderBy(['parent_id' => 'ASC', 'date_create' => 'ASC'])
                            ->with('user')
                            ->all();
                if ($comments) {
                        $comments = self::buildTree($comments);
                }
                return $comments;
        }
	
	/**
	 * Create tree of comments
	 * @param array $data
	 * @param int $rootID parent_id
	 * @return tree structure comments.
	 */
	protected function buildTree(&$data, $rootId = 0) 
        {
                $tree = [];
                
                foreach ($data as $id => $node) {
                    
                    if ($node->parent_id == $rootId) {
                        unset($data[$id]);
                        $node->children = self::buildTree($data, $node->comment_id);
                        $tree[] = $node;
                    }
                }
                return $tree;
        }

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['body'], 'required' , 'on' => ['create']],
			['parent_id', 'exist', 'targetAttribute' => 'comment_id']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios()
	{
		return [
			'create' => ['body', 'parent_id', 'from', 'to'],
			'update' => ['body'],
			'delete' => ''
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
		    'commnet_id' => Yii::t('users', 'ID'),
		    'parent_id' => Yii::t('users', 'Parent Comment'),
		    'from' => Yii::t('users', 'Author'),
		    'to' => Yii::t('users', 'Reciver'),
                    'body' => Yii::t('users', 'Text'),
                    'status' => Yii::t('users', 'Status'),
                    'create_time' => Yii::t('users', 'Time Create'),
		];
	}

        /**
	 * @return \yii\db\ActiveRelation.
	 */
	public function getUser()
        {
            return $this->hasOne(User::className(), ['user_id' => 'from']);
        }
        
        /**
	 * @return \yii\db\ActiveRelation.
	 */
	public function getReciever()
        {
            return $this->hasOne(User::className(), ['user_id' => 'to']);
        }
        
        /**
         * Cache counter
         * TODO I think this not right way
	 * @return int $value
	 */
	public function CountReply()
        {
            
                $keyCountMessage = self::CACHE_USER_COUNT_MESSAGE;
                $keyNewMessage = self::CACHE_USER_COUNT_NEW_MESSAGE;
                
		$valueNewMessage = Yii::$app->getCache()->get($keyNewMessage);
		$valueCountMessge = Yii::$app->getCache()->get($keyCountMessage);
                
                $isvalueNewMessage = ($valueNewMessage === false || empty($valueNewMessage));
                $isvalueCountMessage = ($valueCountMessge === false || empty($valueCountMessge));
                $countMessage = Comment::find()->count();
                
		if (($isvalueNewMessage || $isvalueCountMessage) || ($countMessage != $valueCountMessge)) {
			$valueNewMessage = Comment::find()
                                ->where(['to' => Yii::$app->user->id, 'status' => self::STATUS_UNREAD])
                                ->count();
                        
			Yii::$app->cache->set($keyCountMessage, $countMessage);
			Yii::$app->cache->set($keyNewMessage, $valueNewMessage);
		}
		return $valueNewMessage;
        }
        
        /**
         * If reply Set all replies comments status READ
         */
        public function changeStatus() 
        {
                if ( Yii::$app->user->identity->countreplies ) {
                        Comment::updateAll(['status' => self::STATUS_READ], 
                                'status=' . self::STATUS_UNREAD . ' AND `to`=' . Yii::$app->user->id);
                }
        }

	/**
	 * @inheritdoc
	 */
	public function beforeSave($insert)
	{
		if (parent::beforeSave($insert)) {
			if ($this->isNewRecord) {
				if (!$this->parent_id) {
					$this->parent_id = 0;
				}
			}
			return true;
		}
		return false;
	}
}