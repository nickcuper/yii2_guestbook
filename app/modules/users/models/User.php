<?php
namespace app\modules\users\models;

use Yii;
use yii\base\ModelEvent;
use yii\db\ActiveRecord;
use yii\helpers\Security;
use yii\helpers\ArrayHelper;
use \yii\web\IdentityInterface;

use app\extensions\fileapi\behaviors\UploadBehavior;

use app\modules\users\models\Comment;
use app\modules\users\models\query\UserQuery;

/**
 * Class User
 * @package app\modules\users\models
 * User model.
 *
 * @property integer $user_id
 * @property string $login
 * @property string $email E-mail
 * @property string $password_hash
 * @property string $auth_key
 * @property string $fname
 * @property string $lname
 * @property string $avatar_url
 * @property integer $role_id
 * @property integer $state_id
 * @property integer $country_id
 * @property integer $date_register
 *
 * @property string $password
 * @property string $repassword
 * @property string $oldpassword
 * @property int $countreplies
 */
class User extends ActiveRecord implements IdentityInterface
{
	/**
	 * User Status
	 */
	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;

	/**
	 * User roles
	 */
        const ROLE_ADMIN = 1;
	const ROLE_USER = 2;

	/**
	 * Model Event
	 */
	const EVENT_AFTER_VALIDATE_SUCCESS = 'afterValidateSuccess';

	/**
	 * @var string $username
	 */
	public $username;

        /**
	 * @var string $password
	 */
	public $password;

	/**
	 * @var string $repassword
	 */
	public $repassword;

	/**
	 * @var string $oldpassword
	 */
	public $oldpassword;

        /**
	 * @var int $countreplies
	 */
	public $countreplies;

	/**
	 * @var string $_fio
	 */
	protected $_fio;

	/**
	 * Path to user avatar
	 * @var string $_avatar
	 */
	protected $_avatar;

	/**
	 * @var string
	 */
	protected $_role;

	/**
	 * @var string
	 */
	protected $_status;

        /**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
                        // Login [[login]]
			['login', 'filter', 'filter' => 'trim', 'on' => ['signup']],
			['login', 'required', 'on' => ['signup']],
			['login', 'match', 'pattern' => '/^[a-zA-Z0-9_-]+$/', 'on' => ['signup']],
			['login', 'string', 'min' => 3, 'max' => 30, 'on' => ['signup']],
			['login', 'unique', 'on' => ['signup']],

			// E-mail [[email]]
			['email', 'filter', 'filter' => 'trim', 'on' => ['signup', 'resend', 'recovery']],
			['email', 'required', 'on' => ['signup', 'resend', 'recovery']],
			['email', 'email', 'on' => ['signup', 'resend', 'recovery']],
			['email', 'string', 'max' => 100, 'on' => ['signup', 'resend', 'recovery']],
			['email', 'unique', 'on' => ['signup']],
			#['email', 'exist', 'on' => ['resend', 'recovery'], 'message' => Yii::t('users', 'User exist in the system')],

			// Password [[password]]
			['password', 'required', 'on' => ['signup', 'login', 'password']],
			['password', 'string', 'min' => 6, 'max' => 30, 'on' => ['signup', 'login', 'password']],
			['password', 'compare', 'compareAttribute' => 'oldpassword', 'operator' => '!==', 'on' => 'password'],

			// Confirm Password [[repassword]]
			['repassword', 'required', 'on' => ['signup', 'password']],
			['repassword', 'string', 'min' => 6, 'max' => 30, 'on' => ['signup', 'password']],
			['repassword', 'compare', 'compareAttribute' => 'password', 'on' => ['signup', 'password']],

			// Old password [[oldpassword]]
			['oldpassword', 'required', 'on' => 'password'],
			['oldpassword', 'string', 'min' => 6, 'max' => 30, 'on' => 'password'],
			['oldpassword', 'validateOldPassword', 'on' => 'password'],

			// First Name & Last Name [[fname]] & [[lname]]
			[['fname', 'lname'], 'required', 'on' => ['signup', 'update']],
			[['fname', 'lname'], 'string', 'max' => 50, 'on' => ['signup', 'update']],
			['fname', 'match', 'pattern' => '/^[a-z]+$/iu', 'on' => ['signup', 'update']],
			['lname', 'match', 'pattern' => '/^[a-z]+(-[a-z]+)?$/iu', 'on' => ['signup', 'update']],

                        // WMR & Phone Country and State
                        [['wmr', 'phone','country_id', 'state_id'], 'required', 'on' => 'signup'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios()
	{
		return [
			'signup' => ['fname', 'lname', 'login', 'email', 'password', 'repassword', 'wmr', 'phone','country_id', 'state_id'],
			'activation' => [],
			'login' => ['login', 'password'],
			'update' => ['fname', 'lname', 'login', 'phone', 'avatar_url'],
			'delete' => [],
			'resend' => ['email'],
			'recovery' => ['email'],
			'password' => ['password', 'repassword', 'oldpassword'],
			'delete-avatar' => [],

		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'user_id' => Yii::t('users', 'User ID'),
                        'login' => Yii::t('users', 'Login'),
                        'password' => Yii::t('users', 'Password'),
                        'repassword' => Yii::t('users', 'Repeat Password'),
                        'email' => Yii::t('users', 'Email'),
                        'fname' => Yii::t('users', 'First Name'),
                        'lname' => Yii::t('users', 'Last Name'),
                        'is_active' => Yii::t('users', 'Is Active'),
                        'role_id' => Yii::t('users', 'Role'),
                        'state_id' => Yii::t('users', 'State'),
                        'country_id' => Yii::t('users', 'Country'),
                        'phone' => Yii::t('users', 'Phone'),
                        'wmr' => Yii::t('users', 'WMR'),
                        'avatar_url' => Yii::t('users', 'Avatar'),
                        'date_register' => Yii::t('users', 'Date Register'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [

			'uploadBehavior' => [
				'class' => UploadBehavior::className(),
				'attributes' => ['avatar_url'],
				'deleteScenarios' => [
				    'avatar_url' => 'delete-avatar',
				],
				'scenarios' => ['signup', 'update'],
				'path' => Yii::$app->getModule('users')->avatarPath(),
				'tempPath' => Yii::$app->getModule('users')->avatarTempPath()
			]
		];
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'user';
	}

	/**
	 * @inheritdoc
	 */
	public static function find()
        {
            return new UserQuery(get_called_class());
        }

	/**
	 * Select user by [[user_id]]
	 * @param integer $id
	 */
	public static function findIdentity($id)
	{
		return static::findOne($id);
	}

	/**
	 * Select user by [[login]]
	 * @param string $username
	 */
	public static function findByLogin($login)
	{
		return static::find()->where('login = :login', [':login' => $login])->one();
	}

	/**
	 * Find Active user by login [[login]]
	 * @param string $username
	 */
	public static function findActiveByLogin($login)
	{
		return static::find()->where('login = :login', [':login' => $login])->active()->one();
	}

	/**
	 * Find InActive user by login [[login]]
	 * @param string $username
	 */
	public static function findInactiveByLogin($login)
	{
		return static::find()->where('login = :login', [':login' => $login])->inactive()->one();
	}

        /**
	 * @return integer ID user.
	 */
	public function getId()
	{
		return $this->user_id;
	}

	/**
	 * @return string Auth user key.
	 */
	public function getAuthKey()
	{
		return $this->auth_key;
	}

	/**
	 * @return string Full Name.
	 */
	public function getFio($username = false)
	{
                if ($this->user_id == \Yii::$app->user->id)
                    return Yii::t('users','Me');

		if ($this->_fio === null) {
			$this->_fio = $this->fname . ' ' . $this->lname;
			if ($username !== false) {
				$this->_fio .= ' [' . $this->login . ']';
			}
		}
		return $this->_fio;
	}

	/**
	 * @return string|null Path avatar user.
	 */
	public function getAvatar()
	{
		if ($this->_avatar === null) {
			$this->_avatar = $this->avatar_url ? Yii::$app->getModule('users')->avatarUrl($this->avatar_url) : Yii::$app->getModule('users')->avatarDefaultUrl();
		}
		return $this->_avatar;
	}

	/**
	 * @return string
	 */
	public function getRole()
	{
		if ($this->_role === null) {
			$roles = self::getRoleArray();
			$this->_role = $roles[$this->role_id];
		}
		return $this->_role;
	}

	/**
	 * @return array
	 */
	public static function getRoleArray()
	{
		return [
		    self::ROLE_USER => Yii::t('users', 'User'),
		    self::ROLE_ADMIN => Yii::t('users', 'Admin'),
		];
	}

	/**
	 * @return string Читабельный статус пользователя.
	 */
	public function getStatus()
	{
		if ($this->_status === null) {
			$statuses = self::getStatusArray();
			$this->_status = $statuses[$this->is_active];
		}
		return $this->_status;
	}

	/**
	 * @return array Массив доступных ролей пользователя.
	 */
	public static function getStatusArray()
	{
		return [
		    self::STATUS_ACTIVE => Yii::t('users', 'Active'),
		    self::STATUS_INACTIVE => Yii::t('users', 'InActive')
		];
	}

	/**
	 * Validate Auth key
	 * @param string $authKey
	 * @return boolean
	 */
	public function validateAuthKey($authKey)
	{
		return $this->auth_key === $authKey;
	}

	/**
	 * Validate password
	 * @param string $password
	 * @return boolean
	 */
	public function validatePassword($password)
	{
		return Security::validatePassword($password, $this->password_hash);
	}

	/**
	 * Validate old password
	 * @return boolean
	 */
	public function validateOldPassword()
	{
		if (!$this->validatePassword($this->oldpassword)) {
			$this->addError('oldpassword', Yii::t('users', 'Неверный текущий пароль.'));
		}
	}

        /**
	 * @return \yii\db\ActiveRelation Comments User
	 */
	public function getComments()
        {
            return $this->hasMany(Comment::className(), ['from' => 'user_id']);
        }

	/**
	 * @inheritdoc
	 */
	public function afterValidate()
	{

		if (!$this->hasErrors() && ($this->scenario === 'resend' || $this->scenario === 'recovery')) {
			$event = new ModelEvent;
			$event->sender = self::find()->where(['email' => $this->email])->one();
			$this->trigger(self::EVENT_AFTER_VALIDATE_SUCCESS, $event);
		}
		parent::afterValidate();
	}

	/**
	 * @inheritdoc
	 */
	public function beforeSave($insert)
	{
		if (parent::beforeSave($insert)) {

			if ($this->isNewRecord) {

				if (!empty($this->password)) {
					$this->password_hash = Security::generatePasswordHash($this->password);
				}

				if (!$this->is_active) {
					if (Yii::$app->getModule('users')->activeAfterRegistration) {
						$this->is_active = self::STATUS_ACTIVE;
					} else {
						$this->is_active = self::STATUS_INACTIVE;
					}
				}

				$this->auth_key = Security::generateRandomKey();
			} else {

				if ($this->scenario === 'activation') {
					$this->is_active = self::STATUS_ACTIVE;
					$this->auth_key = Security::generateRandomKey();
				}

				if ($this->scenario === 'recovery') {
					$this->password = Security::generateRandomKey(8);
					$this->auth_key = Security::generateRandomKey();
					$this->password_hash = Security::generatePasswordHash($this->password);
				}

				if ($this->scenario === 'password') {
					$this->password_hash = Security::generatePasswordHash($this->password);
				}

				if ($this->scenario === 'delete-avatar') {
					$avatar = Yii::$app->getModule('users')->avatarPath($this->avatar_url);
					if (is_file($avatar) && unlink($avatar)) {
						$this->avatar_url = '';
					}
				}

			}
			return true;
		}
		return false;
	}

        /**
        * @inheritdoc
        */
        public static function findIdentityByAccessToken($token, $type = null)
        {
            throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
        }
}