<?php
namespace app\modules\users\models\query;

use yii\db\ActiveQuery;
use app\modules\users\models\User;

/**
 * Class PostQuery
 * @package app\modules\blogs\models\query
 * Class custom query model [[User]]
 */
class UserQuery extends ActiveQuery
{
	/**
	 * Get Only Activated users
	 * @param ActiveQuery $query
	 */
	public function active()
	{
		$this->andWhere('is_active = :status', [':status' => User::STATUS_ACTIVE]);
		return $this;
	}

	/**
	 * Get only InActivated users
	 * @param ActiveQuery $query
	 */
	public function inactive()
	{
		$this->andWhere('is_active = :status', [':status' => User::STATUS_INACTIVE]);
		return $this;
	}

	/**
	 * Get users only role ['user role']
	 * @param ActiveQuery $query
	 */
	public function registered()
	{
		$this->andWhere('role_id = :role_user', [':role_user' => User::ROLE_USER]);
		return $this;
	}
        
	/**
	 * Get all active users without "me"
	 * @param ActiveQuery $query
	 */
	public function mypartners()
	{
		$this->andWhere('is_active = :status AND user_id != :me', [
                        ':status' => User::STATUS_ACTIVE,
                        ':me' => \Yii::$app->user->id,
                    
                        ]);
		return $this;
	}
}