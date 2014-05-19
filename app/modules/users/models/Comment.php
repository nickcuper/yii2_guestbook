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
 * @property staring $body
 * @property date $date_create
 * 
 * TODO add status field
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
	const CACHE_USER_COUNT_MESSAGE = 'newMessage';
        
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
	 * @inheritdoc
	 */
	public static function find()
    {
        return new CommentQuery(get_called_class());
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
			[['body'], 'required'],
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
	 * @return \yii\db\ActiveRelation Parent Commet.
	 */
	public function getCommentParent()
        {
            return $this->hasOne(self::className(), ['comment_id' => 'parent_id']);
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