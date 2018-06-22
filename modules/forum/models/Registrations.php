<?php

namespace app\modules\forum\models;

use Yii;
use app\modules\forum\models\Questions;
use dektrium\user\models\User;
/**
 * This is the model class for table "registretion".
 *
 * @property integer $id
 * @property string $name
 * @property integer $question_id
 * @property integer $user_id
 */
class Registrations extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'registrations';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id','count'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Topics',
            'user_id' => 'User ID',
            'orderAmount' => Yii::t('app', 'Quantity of messages'),
            'count' => 'Review',
            'created_at' => 'Date',
        ];
    }
    
    public function getOrderAmount()
    {
        return $this->hasMany(Questions::className(), ['registration_id' => 'id'])->sum('amount');
    }
    public function getQuestions()
    {
        return $this->hasOne(Questions::className(), ['registration_id' => 'id']);
    }
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
