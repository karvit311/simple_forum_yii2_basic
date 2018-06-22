<?php

namespace app\modules\forum\models;

use Yii;

/**
 * This is the model class for table "vote".
 *
 * @property integer $id
 * @property integer $entity
 * @property integer $target_id
 * @property integer $user_id
 * @property string $user_ip
 * @property integer $value
 * @property integer $created_at
 */
class Vote extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vote';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['entity', 'target_id', 'value', 'created_at'], 'required'],
            [['entity', 'target_id', 'user_id', 'value', 'created_at'], 'integer'],
            [['user_ip'], 'string', 'max' => 39],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'entity' => 'Entity',
            'target_id' => 'Target ID',
            'user_id' => 'User ID',
            'user_ip' => 'User Ip',
            'value' => 'Value',
            'created_at' => 'Created At',
        ];
    }
}
