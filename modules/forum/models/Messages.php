<?php

namespace app\modules\forum\models;

use Yii;
// use yii\base\Model;

/**
 * This is the model class for table "messages".
 *
 * @property int $message_id
 * @property string $username
 * @property string $message
 */
class Messages extends \yii\db\ActiveRecord
// class Messages extends Model

{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'messages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message'], 'required'],
            [['message'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'message_id' => 'Message ID',
            'username' => 'Username',
            'message' => 'Message',
        ];
    }

    public function getName()
    {
        return $this->hasOne(User::className(),['message_id'=>'id']);
    }  
}
