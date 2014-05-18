<?php

namespace app\modules\users;

use Yii;
use yii\base\Module;

class Users extends Module
{

        public $controllerNamespace = 'app\modules\users\controllers';

	/**
	 * @var integer Количество записей на главной странице модуля.
	 */
	public $recordsPerPage = 18;

	/**
	 * @var boolean Если данное значение false, пользователи при регистрации должны будут подтверждать свои электронные адреса
	 */
	public $activeAfterRegistration = false;

	/**
	 * @var string Ссылка на которую будет перенаправляен пользователь после деавторизации.
	 */
	public $afterLogoutRedirectUrl = ['/site/index'];

	/**
	 * @var array Доступные расширения загружаемых аватар-ов
	 */
	public $avatarAllowedExtensions = ['jpg', 'png', 'gif'];

	/**
	 * @var integer Ширина аватар-а пользователя
	 */
	public $avatarWidth = 100;

	/**
	 * @var integer Высота аватар-а пользователя
	 */
	public $avatarHeight = 100;

	/**
	 * @var integer Максимальная ширина аватар-а пользователя
	 */
	public $avatarMaxWidth = 1000;

	/**
	 * @var integer Максимальная высота аватар-а пользователя
	 */
	public $avatarMaxHeight = 1000;

	/**
	 * @var integer Максимальный размер загружаемого аватар-а
	 */
	public $avatarMaxSize = 3145728; // 2*1024*1024 = 2MB

	/**
	 * @param string $image Имя изображения
	 * @return string Путь к папке где хранятся аватар-ы или путь к конкретному аватар-у
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
	 * @param string $image Имя изображения
	 * @return string Путь к временной папке где хранятся аватар-ы или путь к конкретному аватар-у
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
	 * @var string URL к папке где хранятся аватар-ы с публичным доступом.
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
	 * @return string URL дефолтной аватар картинки.
	 */
	public function avatarDefaultUrl() {
		$url = '/images/default-avatar.png';
		return $url;
	}

        /**
	 * Отправляем ключ активации учётной записи на указаный при регистарции e-mail.
	 * Вызывается только если $this->activeAfterRegistration = false.
	 * @param User $event
	 * @return boolean
	 */
	public function onSignup($event)
	{
		$model = $event->sender;
                $subject = 'Активационный ключ - ' . Yii::$app->name;
		return $this->send($subject, $model['email'], 'users/signup', [ 'email' => $model['email'], 'key' => $model['auth_key']]);
	}

	/**
	 * Данная функция срабатывает в момент повторной отправки кода активации, новому пользователю.
	 * @param User $event
	 * @return boolean
	 */
	public function onResend($event)
	{
		$model = $event->sender;
                $subject = 'Активационный ключ - ' . Yii::$app->name;
		return $this->send($subject, $model['email'], 'users/signup', ['email' => $model['email'], 'key' => $model['auth_key']]);
		
	}

	/**
	 * Данная функция срабатывает в момент запроса восстановления пароля.
	 * @param User $event
	 * @return boolean
	 */
	public function onRecoveryConfirm($event) {
		$model = $event->sender;
		$subject = 'Подтверждение смены пароля - ' . Yii::$app->name;
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