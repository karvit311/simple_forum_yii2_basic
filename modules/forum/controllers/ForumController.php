<?php

namespace app\modules\forum\controllers;

use Yii;
use app\modules\forum\models\Forum;
use app\modules\forum\models\Questions;
use app\modules\forum\models\Registrations;
use app\modules\forum\models\Messages;
use app\modules\forum\models\Vote;
use app\modules\forum\models\ForumSearch;
use app\modules\forum\models\QuestionsSearch;
use dektrium\user\models\Profile;
use dektrium\user\models\LoginForm;
use dektrium\user\models\User;
use thyseus\message\models\AllowedContacts;
use thyseus\message\models\IgnoreListEntry;
use yii\db\IntegrityException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use dektrium\user\Finder;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use app\models\Cart;
// use app\modules\forum\models\Vote;
/**
 * MessageController implements the CRUD actions for Message model.
 */
class ForumController extends Controller
{
    /**
     * @inheritdoc
     */
    protected $finder; 
    public $message;
    const STATUS_DELETED = -1;
    const STATUS_UNREAD = 0;
    const STATUS_READ = 1;
    const STATUS_ANSWERED = 2;

    const EVENT_BEFORE_MAIL = 'before_mail';
    const EVENT_AFTER_MAIL = 'after_mail';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['sent','compose-add-comment','send','messages','compose-answer', 'view', 'get-date','forum', 'delete', 'mark-all-as-read', 'check-for-new-messages', 'mycomment','update','like'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['forum','like'],
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }


    /** Simply print the count of unread messages for the currently logged in user.
     * If it is only one unread message, display an link to it.
     * Useful if you want to implement a automatic notification for new users using
     * the longpoll method (e.g. query every 10 seconds).
     * To ensure the user is not being bugged too often, we only display the
     * "new messages" message once every <newMessagesEverySeconds> per session.
     * This defaults to 3600 (once every hour). */
    public function actionCheckForNewMessages()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;

        $session = Yii::$app->session;

        $key = 'last_check_for_new_messages';
        $last = 'last_response_when_checking_for_new_messages';

        if ($session->has($key)) {
            $last_check = $session->get($key);
        } else {
            $last_check = time();
        }

        $conditions = ['to' => Yii::$app->user->id, 'status' => 0];

        $count = Forum::find()->where($conditions)->count();

        $time_bygone = time() > $last_check + Yii::$app->getModule('forum')->newMessagesEverySeconds;

        if ($count == 1) {
            $message = Forum::find()->where($conditions)->one();

            if ($message) {
                if ($message->title != $session->get($last) || $time_bygone) {
                    echo Html::a($message->title, ['//forum/forum/view', 'hash' => $message->hash]);
                    Yii::$app->session->set($last, $message->title);
                } else
                    echo 0;
            }
        } else {
            if ($count != $session->get($last) || $time_bygone) {
                echo $count;
                Yii::$app->session->set($last, $count);

            } else {
                echo 0;
            }
        }

        Yii::$app->session->set($key, time());
    }

    /**
     * Lists all Message models where i am the author.
     * @return mixed
     */
    public function actionSent()
    {
        $searchModel = new ForumSearch();
        $searchModel->from = Yii::$app->user->id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        Yii::$app->user->setReturnUrl(['//forum/forum/sent']);

        $users = ArrayHelper::map(
            Forum::find()->where(['from' => Yii::$app->user->id])->select('to')->groupBy('to')->all(), 'to', 'recipient.username');

        return $this->render('sent', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'users' => $users,
        ]);
    }

    /**
     * Mark all messages as read
     * @param integer $id
     * @return mixed
     */
    public function actionMarkAllAsRead()
    {
        foreach (Forum::find()->where([
            'to' => Yii::$app->user->id,
            'status' => Forum::STATUS_UNREAD])->all() as $message)
            $message->updateAttributes(['status' => Forum::STATUS_READ]);

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Displays a single Message model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($hash)
    {
        
        $model = $this->findModel($hash);
        return $this->render('view', [
            'model' => $model,
        ]);
    }
    public function actionForum($id,$answers = null)
    {
        $id = $_GET['id'];

        If ((!isset($_SESSION['countid']))||($id != $_SESSION['countid']))
        {
            $querycount = Forum::find()->where(['question_id' => $id])->all();
            $querycount_count = Forum::find()->where(['question_id' => $id])->count();
            if($querycount_count>0){ 
                foreach ($querycount as $key => $resultcount) {
                    $newcount = $resultcount->count + 1;
                }
                $update = Forum::find()->where(['question_id' => $id])->one();
                $update->count = $newcount;
                $update->update();
            }
        }
        $model = new Forum();
        $registrations_id = Questions::find()->leftJoin('registrations','registrations.id=questions.registration_id')->where(['questions.id' =>$id])->all();
        foreach ($registrations_id as $key => $registration_id) {}
        $messages = $this->findModelForum($id);
        $query = Forum::find()->where(['question_id' => $id])->andWhere(['!=','status', self::STATUS_DELETED])->orderBy(['id' => SORT_DESC]);
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 6]); 
        foreach ($messages as $key => $message) {}
        $pages->pageSizeParam = false;
        $messages = $query->offset($pages->offset)
        ->limit($pages->limit)
        ->all();
        $subQuery = Forum::find()
            ->select('from, SUM(amount) as orderamount' )
            ->groupBy('from');
        $registretions = Registrations::find()->where(['id' => $id])->all();
        $questions = Questions::find()->where(['id' => $id])->all();
        $username = User::find()->where(['id' => Yii::$app->user->id])->one();
        $topic = Questions::find()->where(['id' => $id])->one();
        return $this->render('forum', [
            'messages' => $messages,
            'answers' => $answers,
            'registretions' =>$registretions,
            'registration_id'=> $registration_id,
            'registrations_id' => $registrations_id,
            'questions' =>$questions,
            'topic' => $topic,
            'username' => $username,
            'pages' => $pages,
            'subQuery' => $subQuery,
            'model' => $model,
        ]);
    }
    public function actionLike()
    {
        if($_SERVER["REQUEST_METHOD"] == "POST")
        {   $session = Yii::$app->session;
            if (!isset($_SESSION['likeid'])||($_SESSION['likeid'] !=(int)$_POST["id"]))
            {
                $id = (int)$_POST["id"];   
                $result = Forum::find()->where(['id' => $id])->all();
                $count = Forum::find()->where(['id' => $id])->count();
                    foreach ($result as $key => $row) 
                    {
                        $new_count = $row->yes_like + 1;
                        $update = Forum::find()->where(['id'=> $id])->one();
                        $update->yes_like= $new_count;
                        $update->update();
                    } 
                    echo $update->yes_like; 
                $_SESSION['likeid'] = (int)$_POST["id"];
            }else{
                echo '-1';
            }
        }
    }
    public function actionCountMinus()
    {
        $request = Yii::$app->request;
        if($request->isPost)
        {
            $id = $_POST["id"];
            $count = Cart::find()->where(['cart_id' => $id])->andWhere(['cart_ip'=> $_SERVER['REMOTE_ADDR']])->andWhere(['>','cart_count',0])->count();

            $result = Cart::find()->where(['cart_id' => $id])->andWhere(['cart_ip'=> $_SERVER['REMOTE_ADDR']])->andWhere(['>','cart_count',0])->all();
            If ($count > 0)
            { 
                foreach ($result as $key => $row) {}
                $new_count = $row->cart_count - 1;
                $update = Cart::find()->where(['cart_ip'=> $_SERVER['REMOTE_ADDR']])->andWhere(['cart_id'=> $id])->andWhere(['>','cart_count',0])->one();
                $update->cart_count= $new_count;
                $update->update();
                if ((is_numeric($update->cart_count))) { 
                    echo $update->cart_count; 
                } 
            }
        }
        return $this->render('count-minus');
    }
    /**
     * Finds the Message model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Message the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($hash)
    {
        $message = Forum::find()->where(['hash' => $hash])->one();
        
        if (!$message)
            throw new NotFoundHttpException(Yii::t('message', 'The requested message does not exist.'));

        if (Yii::$app->user->id != $message->to && Yii::$app->user->id != $message->from)
            throw new ForbiddenHttpException(Yii::t('message', 'You are not allowed to access this message.'));

        return $message;
    }
    protected function findQuestionsForum($hash){
        $questions = Questions::find()->where(['hash' => $hash])->one();
        return $questions;
    }
    protected function findUserForum($hash){
        $profile = User::find()->where(['hash' => $hash])->one();
        return $profile;
    }
    protected function findModelForum($id)
    {   $id = $_GET['id'];
        $messages = Forum::find()->where(['question_id' => $id])->all();
        return $messages;
    }
    

    /**
     * Compose a new Message.
     * When it is an answers to a message ($answers is set) it will set the status of the original message to 'Answered'.
     * You can set an 'context' to link this message on to an entity inside your application. This should be an
     * id or slug or other identifier.
     * If $to and $add_to_recipient_list is set, the recipient will be added to the allowed contacts list. The sender
     * will also be included in the recipient?s allowed contact list. Use this to allow first contact between users
     * in an application where contacts are limited.
     * If creation is successful, the browser will be redirected to the referrer, or 'inbox' page if not set.
     * When this action is called by an Ajax Request, the view is prepared to return a partial view.
     * @see README.md
     * @var $to integer|null The 'recipient' attribute will be prefilled with the user of this id
     * @var $answers string|null This message will be marked as an answer to the message of this hash
     * @var $context string|null This message is related to an entity accessible through this url
     * @var $add_to_recipient_list bool This users did not yet have contact, add both of them to their contact list
     * @since 0.3.0
     * @throws NotFoundHttpException When the user is not found in the database anymore.
     * @throws ForbiddenHttpException When the user is on the ignore list.
     * @return mixed
     */
    public function actionComposeAddComment($to = null, $answers = null, $context = null, $add_to_recipient_list = false, $id)
    {
        $id = $_GET['id'];
        if (Yii::$app->request->isAjax) {
            $this->layout = false;
        }
        $registrations_id = Questions::find()->leftJoin('registrations','registrations.id=questions.registration_id')->all();
        foreach ($registrations_id as $key => $registration_id) {
        }

        if ($add_to_recipient_list && $to) {
            $this->add_to_recipient_list($to);
        }

        $model = new Forum();
        $possible_recipients = Forum::getPossibleRecipients(Yii::$app->user->id);

        if (!Yii::$app->user->returnUrl) {
            Yii::$app->user->setReturnUrl(Yii::$app->request->referrer);
        }

        if ($answers) {
            $origin = Forum::find()->where(['hash' => $answers])->one();
            if (!$origin) {
                throw new NotFoundHttpException(Yii::t('message', 'Message to be answered can not be found'));
            }
        }
        if (Yii::$app->request->isPost) { 

            $recipients = Yii::$app->request->post()['Forum'];

            if (is_numeric($recipients)) # Only one recipient given
                $recipients = [$recipients];

                $model = new Forum();
                $model->load(Yii::$app->request->post());
                $model->from = Yii::$app->user->id;
                $model->registration_id = $registration_id['registration_id'];
                $model->title = null;
                $model->amount = '1';
                $model->question_id = $id;
                $model->status = Forum::STATUS_UNREAD;
                $model->save();

                if ($answers) {
                    if ($origin &&  $origin->status == Forum::STATUS_READ) {
                        $origin->updateAttributes(['status' => Forum::STATUS_ANSWERED]);
                    }
                }
            return Yii::$app->request->isAjax ? true : $this->redirect(['/forum/forum/forum?id='.$id]);
        } else {
            if ($to) {
                $model->to = [$to];
            }

            if ($context) {
                $model->context = $context;
            }
            return $this->render('compose-add-comment', [
                'model' => $model,
                'answers' => $answers,
                'origin' => isset($origin) ? $origin : null,
                'context' => $context,
                'dialog' => Yii::$app->request->isAjax,
                'allow_multiple' => true,
                'possible_recipients' => ArrayHelper::map($possible_recipients, 'id', 'username'),
                'registration_id' => $registration_id,
            ]);
        }
    }

    public function actionComposeAnswer($to = null, $answers = null, $context = null, $add_to_recipient_list = false, $id)
    {
        $id = $_GET['id'];
        if (Yii::$app->request->isAjax) {
            $this->layout = false;
        }

        if ($add_to_recipient_list && $to) {
            $this->add_to_recipient_list($to);
        }
        $registrations_id = Questions::find()->leftJoin('registrations','registrations.id=questions.registration_id')->all();
        foreach ($registrations_id as $key => $registration_id) {}
        $model = new Forum();
        $possible_recipients = Forum::getPossibleRecipients(Yii::$app->user->id);

        if (!Yii::$app->user->returnUrl) {
            Yii::$app->user->setReturnUrl(Yii::$app->request->referrer);
        }

        if ($answers) {
            $origin = Forum::find()->where(['hash' => $answers])->one();
            if (!$origin) {
                throw new NotFoundHttpException(Yii::t('message', 'Message to be answered can not be found'));
            }
        }
        if (Yii::$app->request->isPost) { 

            $recipients = Yii::$app->request->post()['Forum']['to'];

            if (is_numeric($recipients)) # Only one recipient given
                $recipients = [$recipients];

            foreach ($recipients as $recipient_id) {
                $model = new Forum();
                $model->load(Yii::$app->request->post());
                $model->from = Yii::$app->user->id;
                $model->to = $recipient_id;
                $model->amount = '1';
                $model->question_id = $id;
                $model->registration_id = $registration_id['registration_id'];
                $model->status = Forum::STATUS_UNREAD;
                $model->save();

                if ($answers) {
                    if ($origin ) {
                        $origin->updateAttributes(['status' => Forum::STATUS_ANSWERED]);
                    }
                }
             }
            return Yii::$app->request->isAjax ? true : $this->redirect(['/forum/forum/forum?id='.$id]);
        } else {
            if ($to) {
                $model->to = [$to];
            }

            if ($context) {
                $model->context = $context;
            }

            if ($answers) {
                $prefix = Yii::$app->getModule('forum')->answerPrefix;

                // avoid stacking of prefixes (Re: Re: Re:)
                if (substr($origin->title, 0, strlen($prefix)) !== $prefix) {
                    $model->title = $prefix . $origin->message;
                } else {
                    $model->title = 'Re: '.$origin->message;
                }

                $model->context = $origin->context;
            }
            return $this->render('compose-answer', [
                'model' => $model,
                'answers' => $answers,
                'origin' => isset($origin) ? $origin : null,
                'context' => $context,
                'dialog' => Yii::$app->request->isAjax,
                'allow_multiple' => true,
                'possible_recipients' => ArrayHelper::map($possible_recipients, 'id', 'username'),
            ]);
        }
    }
    public function actionGetDate($message=null)   
    {
        $model = new Messages();
        if ($model->load(Yii::$app->request->post())) {

        $model->message = $message;
        $model->save();}  
        $data = Messages::find()->asArray()->all();
        $data= json_encode($data);
        return $data;
    }
    public function actionUpdate($hash)
    {
        $model = $this->findModel($hash);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'hash' => $model->hash]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
    public function actionMycomment($id)
    {   
        $id = Yii::$app->user->id;
        $searchModel = new ForumSearch();
        $dataProvider = $searchModel->searchc(Yii::$app->request->queryParams);
        $registrations_id = Questions::find()->leftJoin('registrations','registrations.id=questions.registration_id')->where(['questions.id' =>$id])->all();
        foreach ($registrations_id as $key => $registration_id) {}
        $forums_id = Forum::find()->all();
        foreach ($forums_id as $key => $forum_id) {}
        $messages = $this->findModelForum($id);
        foreach ($messages as $key => $message) {}
        $mycomments = Forum::find()->where(['from' => $id])->andWhere(['!=','status', self::STATUS_DELETED])->all();
        return $this->render('mycomment', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'mycomments' => $mycomments,
            'forums_id' => $forums_id,
            'messages' => $messages,
        ]);
    }
    public function actionMessages($id)
    {
        $id = $_GET['id'];
        $model = new Forum();
        if ($model->load(Yii::$app->request->post())) {

            $model->from = Yii::$app->user->id;
            $model->question_id = $id;
            $model->title = 'FORUM';
            $model->amount = '1';
            $model->status = Forum::STATUS_UNREAD;
            $model->save();
        }
        return $this->redirect(['/forum/forum/forum?id='.$id]);
    }

    public function actionSend()
    {
        $model =new Messages();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        return ActiveForm::validate($model);
        }

    }
    
    public function add_to_recipient_list($to)
    {
        if ($recipient = User::findOne($to)) {
            try {
                $ac = new AllowedContacts();
                $ac->user_id = Yii::$app->user->id;
                $ac->is_allowed_to_write = $to;
                $ac->save();

                $ac = new AllowedContacts();
                $ac->user_id = $to;
                $ac->is_allowed_to_write = Yii::$app->user->id;
                $ac->save();
            } catch (IntegrityException $e) {
                // ignore integrity constraint violation in case users are already connected
            }
        } else throw new NotFoundHttpException();
    }

    /**
     * Deletes an existing Message model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($hash)
    {
        $model = $this->findModel($hash);

        if ($model->from != Yii::$app->user->id)
            throw new ForbiddenHttpException;

        $model->delete();

        return $this->redirect(['mycomment?id='. $model->id]);
    }
}
