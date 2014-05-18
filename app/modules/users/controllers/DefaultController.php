<?php
namespace app\modules\users\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\HttpException;
use yii\web\Controller;
use yii\helpers\Url;

// models
use app\modules\users\models\User;
use app\modules\users\models\LoginForm;

// fileApi
use app\extensions\fileapi\actions\UploadAction;
use app\extensions\fileapi\actions\DeleteAction;

class DefaultController extends Controller
{
	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'actions' => ['login', 'signup', 'resend', 'activation', 'recovery'],
						'roles' => ['?']
					],
					[
						'allow' => true,
						'actions' => ['index', 'logout', 'request-email-change', 'password', 'update', 'guestbook'],
						'roles' => ['@']
					],
					[
						'allow' => true,
						'actions' => ['view', 'email'],
						'roles' => ['?', '@']
					],
					[
						'allow' => true,
						'actions' => ['uploadTempAvatar'],
						'verbs' => ['POST'],
						'roles' => ['?', '@']
					],
					[
						'allow' => true,
						'actions' => ['delete-avatar', 'deleteTempAvatar'],
						'verbs' => ['DELETE'],
						'roles' => ['@']
					],
					[
						'allow' => true,
						'actions' => ['delete'],
						'verbs' => ['DELETE'],
						'roles' => ['@']
					]
				]
			]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function actions()
	{
		return [
		    // Upload temp avatar
		    'uploadTempAvatar' => [
		        'class' => UploadAction::className(),
		        'path' => $this->module->avatarTempPath(),
		        'types' => $this->module->avatarAllowedExtensions,
		        'minHeight' => $this->module->avatarHeight,
		        'maxHeight' => $this->module->avatarMaxHeight,
		        'minWidth' => $this->module->avatarWidth,
		        'maxWidth' => $this->module->avatarMaxWidth,
		        'maxSize' => $this->module->avatarMaxSize
		    ],
		    // Delete temp avatar
		    'deleteTempAvatar' => [
		        'class' => DeleteAction::className(),
		        'path' => $this->module->avatarTempPath(),
		    ]
		];
	}

	/**
	 * Show all Records
	 */
	function actionIndex()
	{
		$dataProvider = new ActiveDataProvider([
			'query' => User::find()->mypartners(),
			'pagination' => [
			    'pageSize' => $this->module->recordsPerPage
			]
		]);
		
		return $this->render('index', [
			'dataProvider' => $dataProvider
		]);
	}

	/**
	 * Show user profile by username
	 * @param string $username.
	 */
	public function actionView($username)
	{
		if ($mUser = User::findActiveByLogin($username)) {
			
			return $this->render('view', [
				'model' => $mUser
			]);
		} else {
			throw new HttpException(404);
		}
	}

	/**
	 * Create new account.
	 */
	public function actionSignup()
	{
		$model = new User(['scenario' => 'signup']);
		// Добавляем обработчик события который отправляет сообщение с клюом активации на e-mail адрес что был указан при регистрации.
		if ($this->module->activeAfterRegistration === false) {
			$model->on(User::EVENT_AFTER_INSERT, [$this->module, 'onSignup']);
		}

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			// Если после регистрации нужно подтвердить почтовый адрес, вызываем функцию отправки кода активации на почту.
			if ($this->module->activeAfterRegistration === false) {
				// Сообщаем пользователю что регистрация прошла успешно, и что на его e-mail был отправлен ключ активации аккаунта.
				Yii::$app->session->setFlash('success', Yii::t('users', 'Учётная запись была успешно создана. Через несколько секунд вам на почту будет отправлен код для активации аккаунта. В случае если письмо не пришло в течении 15 минут, вы можете заново запросить отправку ключа по данной <a href="{url}">ссылке</a>. Спасибо!', ['url' => Url::toRoute('resend')]));
			} else {
				// Авторизуем сразу пользователя.
				Yii::$app->getUser()->login($model);
				// Сообщаем пользователю что регистрация прошла успешно.
				Yii::$app->session->setFlash('success', Yii::t('users', 'Account was created'));
			}
			// User go to home
			return $this->goHome();
		}
		
		return $this->render('signup', [
			'model' => $model
		]);
	}

	/**
	 * Авторизуем пользователя.
	 */
	public function actionLogin()
	{

		if (!Yii::$app->user->isGuest) {
			$this->goHome();
		}
                
		$mLoginForm = new LoginForm;
                
		if ($mLoginForm->load(Yii::$app->request->post()) && $mLoginForm->login()) {
			return $this->goBack();
		}
		
		return $this->render('login', [
			'model' => $mLoginForm
		]);
	}

	/**
	 * Logout user
	 */
	public function actionLogout()
	{
		Yii::$app->user->logout();
		// redirect user 
		return $this->redirect($this->module->afterLogoutRedirectUrl);
	}

	/**
	 * Activate User Profile
	 * @param string $email
	 * @param string $key
	 */
	public function actionActivation($email, $key)
	{
		if ($mUser = User::find()->where(['and', 'email = :email', 'auth_key = :auth_key'], [':email' => $email, ':auth_key' => $key])->inactive()->one()) {
			$mUser->setScenario('activation');
			// If find record show message
			if ($mUser->save(false)) {
				Yii::$app->session->setFlash('success', Yii::t('users', 'Congratulations! Your Account was activate.'));
			}
		} else {
			
			Yii::$app->session->setFlash('danger', Yii::t('users', 'Oops! What happend Please contanct to admin'));
		}
		
		return $this->goHome();
	}

	/**
	 * Recovery Password
	 * @param string $email
	 * @param string $key
	 */
	public function actionRecovery($email = false, $key = false)
	{
		
		if ($email && $key) {
			
			if ($mUser = User::find()->where(['and', 'email = :email', 'auth_key = :auth_key'], [':email' => $email, ':auth_key' => $key])->active()->one()) {
				$mUser->setScenario('recovery');
				// add Event Handler for send email
				$mUser->on(User::EVENT_AFTER_UPDATE, [$this->module, 'onRecoveryPassword']);

				if ($mUser->save(false)) {
					// В случае успешного восстановления пароля, перенаправляем пользователя на главную страницу, и оповещаем пользователя об успешном завершении процесса восстановления.
					Yii::$app->session->setFlash('success', Yii::t('users', 'Пароль был успешно восстановлен и отправлен на указанный электронный адрес. Проверьте пожалуйста почту!'));
				}
			} else {
				// В случае когда пользователь с передаными аргументами не существует в базе данных, оповещаем пользователя об ошибке.
				Yii::$app->session->setFlash('danger', Yii::t('users', 'Неправильный запрос подтверждения смены пароля. Пожалуйста попробуйте ещё раз!'));
			}
			// Перенаправляем пользователя на главную страницу сайта.
			return $this->goHome();

		// В случае когда $email и $key не заданы, прорабатывается сценарий непосредственного запроса восстановления пароля.
		} else {
			$mUser = new User(['scenario' => 'recovery']);
			// Добавляем обработчик события который отправляет сообщение с ключом подтверждения смены пароля на e-mail адрес пользователя.
			$mUser->on(User::EVENT_AFTER_VALIDATE_SUCCESS, [$this->module, 'onRecoveryConfirm']);

			if ($mUser->load(Yii::$app->request->post()) && $mUser->validate()) {
			    // Перенаправляем пользователя на главную страницу, и оповещаем его об успешном завершении запроса восставновления пароля.
			    Yii::$app->session->setFlash('success', Yii::t('users', 'Ссылка для восстановления пароля, была отправлена на указанный вами электронный адрес.'));
			    return $this->goHome();
			}
			// Рендерим представление.
			return $this->render('recovery', [
				'model' => $mUser
			]);
		}
	}

	/**
	 * Update data user (My Account)
	 */
	public function actionUpdate()
	{
		// Get current user 
		if ($mUser = User::findOne(Yii::$app->user->id)) {
			$mUser->setScenario('update');

			if ($mUser->load(Yii::$app->request->post()) && $mUser->save()) {
				
				Yii::$app->session->setFlash('success', Yii::t('users', 'Profile was updated'));
				return $this->redirect(['view', 'username' => $mUser->login]);
			}
			
			return $this->render('update', [
				'model' => $mUser,
			]);
		}
	}

	/**
	 * Remove User Avatar
	 */
	public function actionDeleteAvatar()
	{
		$mUser = User::findOne(Yii::$app->user->id);
		$mUser->setScenario('delete-avatar');
		$mUser->save(false);
	}
       
        /**
         * Show my comments
         */
        public function actionGuestbook() 
        {
                
            
                return $this->render('guestbook', [
				'model' => null,
			]);
        }
}