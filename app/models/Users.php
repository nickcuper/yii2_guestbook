<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property string $user_id
 * @property string $login
 * @property string $password
 * @property string $email
 * @property string $fname
 * @property string $lname
 * @property string $vname
 * @property integer $is_active
 * @property integer $role_id
 * @property integer $state_id
 * @property string $zip
 * @property string $city
 * @property string $address
 * @property string $phone_cell
 * @property string $phone_work
 * @property string $date_register
 *
 * @property Roles $role
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login', 'password', 'email'], 'required'],
            [['is_active', 'role_id', 'state_id', 'date_register'], 'integer'],
            [['login', 'email', 'address'], 'string', 'max' => 255],
            [['password'], 'string', 'max' => 32],
            [['fname', 'lname', 'city'], 'string', 'max' => 80],
            [['vname', 'phone_cell', 'phone_work'], 'string', 'max' => 20],
            [['zip'], 'string', 'max' => 10],
            [['login'], 'unique'],
            [['email'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app', 'User ID'),
            'login' => Yii::t('app', 'Login'),
            'password' => Yii::t('app', 'Password'),
            'email' => Yii::t('app', 'Email'),
            'fname' => Yii::t('app', 'Fname'),
            'lname' => Yii::t('app', 'Lname'),
            'vname' => Yii::t('app', 'Vname'),
            'is_active' => Yii::t('app', 'Is Active'),
            'role_id' => Yii::t('app', 'Role ID'),
            'state_id' => Yii::t('app', 'State ID'),
            'zip' => Yii::t('app', 'Zip'),
            'city' => Yii::t('app', 'City'),
            'address' => Yii::t('app', 'Address'),
            'phone_cell' => Yii::t('app', 'Phone Cell'),
            'phone_work' => Yii::t('app', 'Phone Work'),
            'date_register' => Yii::t('app', 'Date Register'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Roles::className(), ['role_id' => 'role_id']);
    }
}
