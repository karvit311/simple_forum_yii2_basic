<?php

namespace app\modules\forum\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use dektrium\user\models\User;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\modules\forum\models\Registrations;
use cinghie\userextended\models\Profile;
/**
 * This is the model class for table "questions".
 *
 * @property integer $id
 * @property string $name
 * @property integer $forum_id
 * @property integer $user_id
 */
class Questions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'questions';
    }
      public function behaviors()
    {
        return [
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [ActiveRecord::EVENT_BEFORE_INSERT => 'hash'],
                'value' => md5(uniqid(rand(), true)),
            ]
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
            [['amount'], 'number'],
            [['orderAmount'], 'safe'],
            [['user_id','count'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Topic',
            'user_id' => 'User ID',
            'count' => 'Reviews',
            'orderAmount' => Yii::t('app', 'Quantity of messages'),
            'username' => Yii::t('app', 'username'),

        ];
    }
    public function getOrderAmount()
    {
        return $this->hasMany(Forum::className(), ['question_id' => 'id'])->sum('amount');
    }
    public  function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']); 
    }
    public  function getRegistrations()
    {
        return $this->hasOne(Registrations::className(), ['id' => 'registration_id']); 
    }
    public  function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'user_id']); 
    }
    public  function getIcon(){
        return  $this->user ? $this->user->icon : 'icon';
    }
    public function getForum()
    {
        return $this->hasOne(Forum::className(), ['question_id' => 'id']);
    }
    public function search($params)
    {
        $query = Registrations::find();
        $subQuery = Questions::find()
            ->select('registration_id, id')
            ->groupBy('registration_id');
        $query->leftJoin(['orderSum' => $subQuery], 'orderSum.registration_id = id');
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'id',
                'name',
                'orderAmount' => [
                    'asc' => ['orderSum.order_amount' => SORT_ASC],
                    'desc' => ['orderSum.order_amount' => SORT_DESC],
                    'label' => 'Order Name'
                ]
            ]
        ]);  

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }      
        return $dataProvider;
    }

    public static function create($id, $name ='')
    {
        $id = $_GET['id'];
        $model = new Questions;
        $model->load(Yii::$app->request->post());
        $model->name = $name;
        $model->amount = '1';
        $model->user_id = Yii::$app->user->id;
        $model->registration_id = $id;
        $model->save();
        return $model;
    }
    
}
