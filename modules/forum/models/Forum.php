<?php

namespace app\modules\forum\models;

use app\modules\forum\jobs\EmailJob;
// use app\modules\forum\validators\IgnoreListValidator;
use app\modules\forum\models\Questions;
use app\modules\forum\models\Forum;
use yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use dektrium\user\models\LoginForm;
use dektrium\user\models\User;
use dektrium\user\models\Profile;
use yii\data\ActiveDataProvider;


class Forum extends ActiveRecord
{
    const STATUS_DELETED = -1;
    const STATUS_UNREAD = 0;
    const STATUS_READ = 1;
    const STATUS_ANSWERED = 2;

    const EVENT_BEFORE_MAIL = 'before_mail';
    const EVENT_AFTER_MAIL = 'after_mail';
    public $cnt;
    public $orderamount;

    public static function tableName()
    {
        return '{{%forum}}';
    }

    public static function compose($from, $to, $title, $message = '', $context = null, $id)
    {
        $id = $_GET['id'];
        $model = new Forum;
        $model->from = $from;
        $model->to = $to;
        $model->title = $title;
        $model->message = $message;
        $model->context = $context;
        $model->status = self::STATUS_UNREAD; 
        $model->question_id = $id;
        $model->save();
        return $model;
    }
    public static function composeforum($to,$title,$from,  $message = '', $context = null, $id)
    {
        $id = $_GET['id'];
        $model = new Forum;
        $model->from = $from;
        $model->to = $to;
        $model->title = $title;
        $model->message = $message;
        $model->context = $context;
        $model->status = self::STATUS_UNREAD;
        $model->question_id = $id;
        $model->save();
        return $model;
    }
    /**
     * returns an array of possible recipients for the given user. Applies the ignorelist and applies possible custom
     * logic.
     * @param $for_user
     * @return mixed
     */
    public static function getPossibleRecipients($for_user)
    {
        $user = new Yii::$app->controller->module->userModelClass;

        $allowed_contacts = [];
        foreach (AllowedContacts::find()->select('is_allowed_to_write')->where(['user_id' => $for_user])->all() as $allowed_user)
            $allowed_contacts[] = $allowed_user->is_allowed_to_write;
 
        $users = $user::find();

        $users->where(['!=', 'id', Yii::$app->user->id]);

        if ($allowed_contacts)
            $users->andWhere(['id' => $allowed_contacts]);

        $users = $users->all();

        if (is_callable(Yii::$app->getModule('forum')->recipientsFilterCallback))
            $users = call_user_func(Yii::$app->getModule('forum')->recipientsFilterCallback, $users);

        return $users;
    }

    public function rules()
    {
        return [
            // [['to'], 'required'],
            [['to','registration_id'], 'integer'],
            [['title', 'message', 'context'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['orderAmount'], 'safe'],
            [['orderamount'], 'safe'],
            [['amount'], 'number'],
            [['to'], 'exist',
                'targetClass' => Yii::$app->getModule('forum')->userModelClass,
                'targetAttribute' => 'id',
                'message' => Yii::t('message', 'Recipient has not been found'),
            ]
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [ActiveRecord::EVENT_BEFORE_INSERT => 'hash'],
                'value' => md5(uniqid(rand(), true)),
            ],
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => null,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * Send E-Mail to recipients if configured.
     * @param $insert
     * @param $changedAttributes
     * @return mixed
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert && isset($this->recipient->email)) {
            $mailMessages = Yii::$app->getModule('forum')->mailMessages;

            if ($mailMessages === true
                || (is_callable($mailMessages) && call_user_func($mailMessages, $this->recipient))) {
                $this->sendEmailToRecipient();
            }
        }

        return parent::afterSave($insert, $changedAttributes);
    }

    /**
     * The new message should be send to the recipient via e-mail once.
     * By default, Yii::$app->mailer is used to do so.
     * If you want do enqueue the mail in an queue like yii2-queue or nterms/yii2-mailqueue you
     * can configure this in the module configuration.
     * You can configure your application specific mail views using themeMap.
     *
     * @see https://github.com/yiisoft/yii2-queue
     * @see https://github.com/nterms/yii2-mailqueue
     * @see http://www.yiiframework.com/doc-2.0/yii-base-theme.html
     */
    public function sendEmailToRecipient()
    {
        $mailer = Yii::$app->{Yii::$app->getModule('forum')->mailer};

        $this->trigger(Forum::EVENT_BEFORE_MAIL);

        if (!file_exists($mailer->viewPath)) {
            $mailer->viewPath = '@vendor/thyseus/yii2-message/mail/';
        }

        $mailing = $mailer->compose(['html' => 'message', 'text' => 'text/message'], [
            'model' => $this,
            'content' => $this->message
        ])
            ->setTo($this->recipient->email)
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setSubject($this->title);

        if (is_a($mailer, 'nterms\mailqueue\MailQueue')) {
            $mailing->queue();
        } else if (Yii::$app->getModule('forum')->useMailQueue) {
            Yii::$app->queue->push(new EmailJob([
                'mailing' => $mailing,
            ]));
        } else {
            $mailing->send();
        }

        $this->trigger(Forum::EVENT_AFTER_MAIL);
    }

    public function delete()
    {
        return $this->updateAttributes(['status' => Forum::STATUS_DELETED]);
    }

    public function getRecipientLabel()
    {
        if (!$this->recipient)
            return Yii::t('message', 'Removed user');
        else
            return $this->recipient->username;
    }


    public function getAllowedContacts()
    {
        return $this->hasOne(AllowedContacts::className(), ['id' => 'user_id']);
    }

    public function getRecipient()
    {
        return $this->hasOne(Yii::$app->getModule('forum')->userModelClass, ['id' => 'to']);
    }

    public function getSender()
    {
        $b = $this->hasOne(Yii::$app->getModule('forum')->userModelClass, ['id' => 'from']);
         return $b;
    }

    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'from']); 

    }

    public function model($id)
    {
        $count = $this->Forum($id);
        return $count;
    }
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('message', '#'),
            'from' => Yii::t('message', 'from'),
            'to' => Yii::t('message', 'to'),
            'title' => Yii::t('message', 'title'),
            'message' => Yii::t('message', 'message'),
            'created_at' => Yii::t('message', 'sent at'),
            'context' => Yii::t('message', 'context'),
            'orderAmount' => Yii::t('app', 'Quantity of messages'),
            'orderamount' => Yii::t('app', 'Count'),
            'registration_id' => 'Registration ID',
        ];
    }
    public function getCustomer()
    {
        return $this->hasOne(Questions::className(), ['id' => 'question_id']);
    }
    public function getRegistrations()
    {
        return $this->hasOne(Registrations::className(), ['id' => 'registration_id']);
    }

    public function searchc($params) 
    {
        $id = Yii::$app->user->id;
        $query = Forum::find()->where(['from' => $id]);
        $subQuery = Forum::find()
            ->where(['from' => $id]);
     
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
     
        /**
         * Setup your sorting attributes
         * Note: This is setup before the $this->load($params) 
         * statement below
         */
         $dataProvider->setSort([
            'attributes' => [
                'id',
                'name',
                'message',
            ]
        ]);        
     
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
     
        $query->andFilterWhere([
            'id' => $this->id,
        ]);
     
        $query->andFilterWhere(['like', 'name', $this->name]);
     
        return $dataProvider;
    }
    public function search($params) 
    {
        $query = Questions::find();
        $subQuery = Forum::find()
            ->select('question_id, SUM(amount) as order_amount')
            ->groupBy('question_id');
        $query->leftJoin(['orderSum' => $subQuery], 'orderSum.question_id = id');
     
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
     
        /**
         * Setup your sorting attributes
         * Note: This is setup before the $this->load($params) 
         * statement below
         */
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
     
        $query->andFilterWhere([
            'id' => $this->id,
        ]);
     
        $query->andFilterWhere(['like', 'name', $this->name]);
     
        // filter by order amount
        $query->andWhere(['orderSum.order_amount' => $this->orderAmount]);
     
        return $dataProvider;
}


}
