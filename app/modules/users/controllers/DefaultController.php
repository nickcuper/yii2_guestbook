<?php
namespace app\modules\users\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\HttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Url;

use app\controllers\AppController;

// models
use app\modules\users\models\User;
use app\modules\users\models\Comment;
use app\modules\users\models\LoginForm;

// fileApi
use app\extensions\fileapi\actions\UploadAction;
use app\extensions\fileapi\actions\DeleteAction;

/**
 * Base Controller Users Module
 */

class DefaultController extends AppController
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
						'actions' => ['index', 'logout', 'password', 'update', 'guestbook', 'partners', 'comments'],
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
	function actionPartners()
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
		// Add an event handler that sends a message with the activation key on the e-mail address that was specified during registration.
		if ($this->module->activeAfterRegistration === false) {
			$model->on(User::EVENT_AFTER_INSERT, [$this->module, 'onSignup']);
		}

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			// If needed activate account
			if ($this->module->activeAfterRegistration === false) {

				Yii::$app->session->setFlash('success', Yii::t('users', 'Congratulations! Account was created and your email was sent mail with activation key'));
			} else {
				// Auth user and show message
				Yii::$app->getUser()->login($model);
				Yii::$app->session->setFlash('success', Yii::t('users', 'Congratulations! Account was created'));
			}

			return $this->goHome();
		}

		return $this->render('signup', [
			'model' => $model
		]);
	}

	/**
	 * Auth user
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
                // render page with form if not found params
		if (!$email && !$key) {

			$mUser = new User(['scenario' => 'recovery']);
			// Add an event handler that sends a message with a key to the password change e-mail address of the user.
			$mUser->on(User::EVENT_AFTER_VALIDATE_SUCCESS, [$this->module, 'onRecoveryConfirm']);

			if ($mUser->load(Yii::$app->request->post()) && $mUser->validate()) {
			    // Redirect the user to the main page, and it notifies the successful completion of the request Restitution password.
			    Yii::$app->session->setFlash('success', Yii::t('users', 'Link to reset your password has been sent to your specified email address.'));
			    return $this->goHome();
			}

			return $this->render('recovery', [
				'model' => $mUser
			]);

		} else {

                        // Check params and try recovery password
                        if ($mUser = User::find()->where(['and', 'email = :email', 'auth_key = :auth_key'], [':email' => $email, ':auth_key' => $key])->active()->one()) {
                                    $mUser->setScenario('recovery');
                                    // add Event Handler for send email
                                    $mUser->on(User::EVENT_AFTER_UPDATE, [$this->module, 'onRecoveryPassword']);

                                    if ($mUser->save(false)) {
                                            // In case of a successful password recovery, redirect the user to the main page, and notifies the user about the successful completion of the recovery process.
                                            Yii::$app->session->setFlash('success', Yii::t('users', 'Password has been successfully restored and sent to the specified email address. Please check the mail!'));
                                    }
                            } else {
                                    // In case of the user with the given arguments do not exist in the database, alerting the user of the error.
                                    Yii::$app->session->setFlash('danger', Yii::t('users', 'Неправильный запрос подтверждения смены пароля. Пожалуйста попробуйте ещё раз!'));
                            }

                            return $this->goHome();

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
         * Show comment, if username is Null show my comments
         * @param string $username Login user
         */
        public function actionGuestbook($username = null)
        {

                if ($username && $mUserByUsername = User::findActiveByLogin($username)) {
                    $userId = $mUserByUsername->user_id;
                } else {
                    $userId = Yii::$app->user->id;
                }

                Comment::changeStatus();

                return $this->render('guestbook', [
				'user_id' => $userId,
				'login' => $username,
			]);
        }


        /**
	 * Create new comment
	 */
	public function actionComments()
	{

		$model = new Comment(['scenario' => 'create']);
		Yii::$app->response->format = Response::FORMAT_JSON;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$level = Yii::$app->request->post('level');
                        // if Answer to other comment
			$isAnswer = Yii::$app->request->post('is_answer');

			if ($level !== null) {
				$level = ($level < $this->module->maxLevel)
                                        ? $level + 1 : $this->module->maxLevel;
			} else {
				$level = 0;
			}

                        // TODO hard code remove later
                        if (!$isAnswer) {
                            Yii::$app->session->setFlash('success', Yii::t('users', 'Comment was added'));
                            return $this->redirect('/guestbook');
                        }

			return [
			    'success' => $this->renderPartial('@app/modules/users/widgets/comments/views/_index_item', [
			    	'model' => $model,
			    	'level' => $level,
			    	'maxLevel' => $this->module->maxLevel
			    ])
			];

		} else {

                         // TODO hard code remove later
                        if (!Yii::$app->request->getIsAjax()) {
                            Yii::$app->session->setFlash('danger', Yii::t('users', 'Please enter text'));
                            return $this->redirect('/guestbook');
                        }

			return ['errors' => ActiveForm::validate($model)];
		}
	}
}