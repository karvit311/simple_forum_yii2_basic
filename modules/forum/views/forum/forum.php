<?php

use app\modules\forum\models\Forum;
use app\modules\forum\models\Vote;
use \yii\widgets\Pjax;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;
use kartik\growl\GrowlAsset;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use dektrium\user\models\User;
use yii\widgets\LinkPager;
use cinghie\userextended\models\Profile;
use yii\widgets\ListView;
use yii\data\ActiveDataProvider;
use yii\helpers\HtmlPurifier;
use yii\grid\GridView;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/* @var $this yii\web\View */
/* @var $message app\models\Message */
rmrevin\yii\fontawesome\AssetBundle::register($this);
?>
<html>
    <head></head>
    <div id="goHome">
        <?php foreach ($registrations_id as $key => $registration_id) {} ?>
        <a href="/forum/questions/index?id=<?= $registration_id->registration_id?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> back</a>
    </div>
    <?php foreach ($messages as $key => $message) { } ?>
    <?php $id =$_GET['id']; ?>
    <body>
        <?php if(!isset($message->id) && (!Yii::$app->user->isGuest)){?>
            <p><span class="TopicAdd"><?= $username->username  ?></span>, you can leave your comment to topic:  <span class="TopicAdd">"<?= $topic->name ?>" .</span>
            </p>
            <hr>
            <p> <?= Html::a(Yii::t('message', 'Add a comment') . ' <i class="fa fa-plus"></i>', ['compose-add-comment?id='. $id ], ['class' => 'btn btn-success']) ?> </p>
        <?php } else { ?>
            <br>
            <div id="forTable">
                <?php if(Yii::$app->session->hasFlash('error')){  ?>
                    <div class="alert alert-error alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <h4><i class="icon fa fa-check"></i>Saved!</h4>
                        <?= Yii::$app->session->getFlash('error'); ?>
                    </div>
                <?php } ?>
                <?php foreach ($messages as $key => $message) {?>
                    
                    <table id="tableTable" style="">
    <tr id="headerOfTable" >
        <th  colspan="2">
            <?php 
            foreach ($registretions as $key => $registretion) {
            echo( "\n".  Html::a($registretion->name,['/forum/questions/index?id='.$registretion->id]));
            } 
            foreach ($questions as $key => $question) {
                echo (', '. $question->name);
            }?>
        </th>
    </tr>
    <tr id="titleTable">
        <td id="dateTable">
           <i class="fa fa-user" aria-hidden="true"></i>  <a href="/user/<?= $message->sender['id'] ?>" style="text-decoration: underline;"><?= $from = $message->sender['username']; ?></a>
        </td>
        <td>
            <?php Yii::t('message', ','); ?> <?= Yii::$app->formatter->asDate($message['created_at'],  'yyyy-MM-dd HH:mm:ss');?>
        </td>
    </tr>
    <tr id="bodyTable">
        <td id="info_user">
            <img src="/img/users/<?= $icon = $message->profile['avatar'];?>" width="130px" height="130" alt="Avatar" /><br>
            <small>From: <?= $from = $message->profile['location'];?></small><br>
            <small>Registration: <?= Yii::$app->formatter->asDate($message['created_at'], 'long'); ?></small><br>
                  
            <small>Messages: <?= ($subQuery->where(['from' => $message->sender['id']])->sum('orderamount'));
            ?></small><br>    
        </td>
        <td id="messageBody">
            <?php if($message->recipient['id'] !== null){
                if($message->sender['id'] ==$message['from']){ ?>
                    <blockquote>
                        <small><?= Yii::t('message', 'Message from'); ?>: <?= $from = $message->recipient['username'];?></small><br>
                        <?= Yii::t('message', ''); ?><?php // Html::encode($this->title); ?>
                        <?= $message->title ;?>
                    </blockquote>
                <?php }
            }?>
            <p>
                <div id="messageAnswer">
                    <?= $message['message'] ? $message['message'] : ('<mark>' . Yii::t('message', 'No message content given') . '.</mark>'); ?><br>
                </div>
            </p>
        </td>
    </tr>
    <tr id="footerTable">
        <td id="datefooterTable">
            
        </td>
        <td>
            <div id="answers">
                <?php echo Html::a('<i class="fa fa-reply"  aria-hidden="true"></i> ' . Yii::t('message', 'Answer'),['/forum/forum/compose-answer?id='.$id.'&answers='.$message->hash.'&to='.$message->from], ['class' => 'button']);?>
            </div>
            <div id="like">

                <button class="likegood" tid="<?= $message->id ?>">Нравится</button> 
                <p style="padding-left: 5px; " id="likegoodcount<?= $message->id; ?>"><?= $message->yes_like; ?></p> 
            </div>
   
        </td>
    </tr>
</table>           
                <?php }?>
            </div>
            <div id="box" style="display: none;">
                <?php 
                $id = $_GET['id'];
                $form = ActiveForm::begin([
                    'id'=>'form',
                    'method' => 'post',
                    'action' => ['/forum/forum/messages?id='.$id],
                ]);?>
                <?php  $model= new Forum();?>
                    <div class="container">
                        <?= $form->field($model,'message')->textarea(['rows' => 6,'id' => 'message', 'style'=>'width:100%'])->label(false) ;?>
                        <?= Html::submitButton('submit', ['class' => 'submit','id' => 'submitAddComment']) ?>
                    </div>
                <?php  ActiveForm::end(); ?> 
            </div>
            <p>
                <?php if(!Yii::$app->user->isGuest){?>
                    <?= Html::a(Yii::t('message', 'Add a comment') . ' <i class="fa fa-plus"></i>', ['#' ], ['class' => 'btn btn-success', 'id' => 'toggler']);
                } else {
                    \Yii::$app->session->setFlash('warning', Yii::t('message', 'To leave the comments you should to sign up!'));
                    if (Yii::$app->session->hasFlash('warning')){  ?>
                        <div  class="alert alert-warning">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <strong>Warning!</strong>
                            <?= Yii::$app->session->getFlash('warning') ?>
                        </div>
                    <?php } 
                }?>
            </p><br>
        <?php } ?>
        <div id="pagination" >
                <?=  LinkPager::widget([
                'pagination' => $pages,
                'registerLinkTags' => true
                ]);?> 
        </div>
        <br>
        <?php if(isset($message->id)){ ?>
            <div id="forumBackQuestion">
                <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i> ' . Yii::t('message', 'Back to Questions'), ['/forum/questions/index?id=' . $registration_id->registration_id]) ;?>
            </div>
        <?php } ?>   
    </body>
</html>
  

