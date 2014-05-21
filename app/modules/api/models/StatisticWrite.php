<?php

namespace app\modules\api\models;

use Yii;
use yii\db\ActiveRecord;
use yii\caching\MemCache;
/**
 * This is the model class for table "statistic_write".
 *
 * @property integer $statistic_write_id
 * @property string $random_var
 * @property string $date_created
 */
class StatisticWrite extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'statistic_write';
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
            'statistic_write_id' => 'Statistic Write ID',
            'random_var' => 'Random Var',
            'date_created' => 'Date Created',
        ];
    }

}
