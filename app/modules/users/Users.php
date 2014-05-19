<?php

namespace app\modules\users;

use Yii;
use yii\base\Module;

class Users extends Module
{

        public $controllerNamespace = 'app\modules\users\controllers';

	/**
	 * @var integer Count items on page
	 */
	public $recordsPerPage = 18;

	/**
	 * @var boolean If this value is false, users will be required at check-in to confirm their e-mail addresses
	 */
	public $activeAfterRegistration = false;

	/**
	 * @var string Link that will redirect the user after deauthorize.
	 */
	public $afterLogoutRedirectUrl = ['my/settings/update'];

	/**
	 * @var array
	 */
	public $avatarAllowedExtensions = ['jpg', 'png', 'gif'];

	/**
	 * @var integer
	 */
	public $avatarWidth = 100;

	/**
	 * @var integer
	 */
	public $avatarHeight = 100;

	/**
	 * @var integer
	 */
	public $avatarMaxWidth = 1000;

	/**
	 * @var integer
	 */
	public $avatarMaxHeight = 1000;

	/**
	 * @var integer
	 */
	public $avatarMaxSize = 3145728; // 3*1024*1024 = 3MB
        
	/**
	 * @var integer
	 */
	public $maxLevel = 1;

	/**
	 * @param string $image
	 * @return string
	 */
	public function avatarPath($image = null)
	{
		$path = '@app/uploads/avatars/';
		if ($image !== null) {
			$path .= '/' . $image;
		}
		return Yii::getAlias($path);
	}

	/**
	 * @param string $image
	 * @return string
	 */
	public function avatarTempPath($image = null)
	{
		$path = '@app/uploads/tmp/avatars/';
		if ($image !== null) {
			$path .= '/' . $image;
		}
		return Yii::getAlias($path);
	}

	/**
	 * @var string URL
	 */
	public function avatarUrl($image = null)
	{
		$url = '/app/uploads/avatars/';
		if ($image !== null) {
			$url .= $image;
		}
		
		return $url;
	}

	/**
	 * @return string URL.
	 */
	public function avatarDefaultUrl() {
		$url = '/images/default-avatar.png';
		return $url;
	}

        /**
	 * Send activation key account at specified at registration e-mail.
	 * Called only if $this->activeAfterRegistration = false.
	 * @param User $event
	 * @return boolean
	 */
	public function onSignup($event)
	{
		$model = $event->sender;
                $subject = Yii::t('users', 'Активационный ключ - ') . Yii::$app->name;
		return $this->send($subject, $model['email'], 'users/signup', [ 'email' => $model['email'], 'key' => $model['auth_key']]);
	}

	/**
	 * This function works when resending the activation code to the new user.
	 * @param User $event
	 * @return boolean
	 */
	public function onResend($event)
	{
		$model = $event->sender;
                $subject = Yii::t('users', 'Активационный ключ - ') . Yii::$app->name;
		return $this->send($subject, $model['email'], 'users/signup', ['email' => $model['email'], 'key' => $model['auth_key']]);
		
	}

	/**
	 * This function works in the time of the request password recovery.
	 * @param User $event
	 * @return boolean
	 */
	public function onRecoveryConfirm($event) {
		$model = $event->sender;
		$subject = Yii::t('users', 'Подтверждение смены пароля - ') . Yii::$app->name;
		return $this->send($subject, $model['email'], 'users/recovery-confirm', ['email' => $model['email'], 'key' => $model['auth_key']]);
	}
        
        /**
         * Send mail with params
	 * @var string $subject
	 * @var string $to
	 * @var string|array|null $from
	 * @var string $view
	 * @var array $params
	 */
	protected function send($subject, $to, $view = null, $params = [], $from = null)
	{   
		$from = ($from !== null) ? $from : [Yii::$app->params['suportEmail'] => Yii::$app->name . ' - suport'];
		Yii::$app->mail
		         ->compose($view, $params)
			     ->setFrom($from)
			     ->setTo($to)
			     ->setSubject($subject)
			     ->send();
	}

}