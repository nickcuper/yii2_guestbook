<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "state".
 *
 * @property integer $state_id
 * @property integer $country_id
 * @property string $name
 */
class State extends \yii\db\ActiveRecord
{
    /**
     * Key chache
     */
    const CACHE_STATE_LIST_DATA = 'arrayOfStates';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'state';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country_id', 'name'], 'required'],
            [['country_id'], 'integer'],
            [['name'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'state_id' => Yii::t('app', 'State ID'),
            'country_id' => Yii::t('app', 'Country ID'),
            'name' => Yii::t('app', 'Name'),
        ];
    }
    
    /**
     * Return list of state 
     * @param int $country_id Id country
     * @return array ['name' => '{name}', 'state_id' => {state_id}]
     */
    public static function getStatesArray($country_id)
    {
     
            $key = self::CACHE_STATE_LIST_DATA . $country_id;
            $value = Yii::$app->getCache()->get($key);
            if ($value === false || empty($value)) {
                    $value = self::find()
                            ->select(['state_id', 'name'])
                            ->where(['country_id'=> $country_id])
                            ->orderBy('name ASC')
                            ->asArray()->all();

                    Yii::$app->cache->set($key, $value);
            }
            return $value;
    }
}
