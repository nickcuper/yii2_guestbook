<?php

namespace app\modules\api\models;

use Yii;
use yii\db\ActiveRecord;
use yii\caching\MemCache;

/**
 * This is the model class for table "statistic_read".
 *
 * @property integer $statistic_read_id
 * @property string $random_var
 * @property string $date_created
 */
class StatisticRead extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'statistic_read';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_created'], 'safe'],
            [['random_var'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'statistic_read_id' => 'Statistic Read ID',
            'random_var' => 'Random Var',
            'date_created' => 'Date Created',
        ];
    }

    public static function findOne($param)
    {
        $cache_label = 'statistic_read:statistic_read_id=' . $param;
        $cache = Yii::$app->getCache()->get($cache_label);
        if($cache === false) {
                $cache = parent::findOne($param);
                Yii::$app->cache->set($cache_label, $cache);
        }
        
        return $cache;
    }

}
