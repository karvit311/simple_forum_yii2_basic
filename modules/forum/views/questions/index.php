<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use dektrium\user\models\User;
use app\modules\forum\models\Questions;
use app\modules\forum\models\Forum;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\forum\models\QuestionsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Questions';

if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success alert-dismissable">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        <h4><i class="icon fa fa-check"></i>Created!</h4>
        <?= Yii::$app->session->getFlash('success') ?>
    </div>
<?php endif; ?>

<div class="questions-index">

    <?php $id = $_GET['id'];?>
    <br>
    <?php foreach ($questions as $key => $question) {}?>

        <?php if(!isset($question->id)&& (Yii::$app->user->isGuest )) {?>
            <hr>
            <p> You should to register to leave questions!</p>
            <hr>
            <?= Html::a('Register', ['/user/register'], ['class' => 'btn btn-success']) ?>
        <?php } ?>

        <?php if(!isset($question->id) && (!Yii::$app->user->isGuest)){ ?>
            <h3 id="questionTitle"> This topic doesn't have  questions yet. You can be first! </h3>
        <?php }?>

        <?php  if (!Yii::$app->user->isGuest){ ?>
            <p> 
                <?= Html::a('Create Questions', ['create?id='.$id], ['class' => 'btn btn-success', 'id' => 'my_btn']) ?>
            </p>
        <?php } ?>
        
        <?php if(isset($question->id) ){?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'summary' => '',
            'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                ['attribute' =>  'name',
                'value' => function($data){
                    return Html::a(Html::encode($data->name), Url::to(['/forum/forum/forum', 'id' => $data->id]));
                       
                    },
                'format' => 'raw',
                ],
                [ 
                'attribute' => 'icon',
                'label' => 'author',
                'format' => 'html',
                'contentOptions' => ['style'=>'width: 160px;'],
                'content' => function($data){
                    return  '<p>'. Html::a($data->profile['name'], Url::to(['/user/'.$data->profile['user_id']])). '</p>'. Html::a(Html::img('/img/users/'.$data->profile['avatar'],['width' => '70px'],['alt' => 'Profile Picture', 'class' => 'img img-rounded'])  ,Url::to(['/user/'.$data->profile['user_id']])) ;
                    },
                ],
                ['attribute' => 'orderAmount',
                'value' => function($data){
                    if($data->orderAmount != null){ 
                        return Html::encode($data->orderAmount) . ' messages';}
                    else{
                        return 'There are no comments yet.';
                    }
                },
                'format' => 'raw',
                ],
                [
                    'label' => 'Review',
                    'value' =>  function ($model) {
                        if($model->forum['count'] !== null){ 
                            return $model->forum['count'];
                        }else{
                            return 0;
                        }
                    },
                    
                ],

                ['class' => 'yii\grid\ActionColumn',
                'header' => 'Actions',
                'headerOptions' => ['style' => 'color:#337ab7'],
                'template' => '{view}{update}{delete}',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            if(!Yii::$app->user->isGuest){
                                if($model->user['username'] == Yii::$app->user->identity->username){ 
                                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                                                'title' => Yii::t('app', 'lead-view'),
                                    ]);
                                } 
                            }
                        },
                        'update' => function ($url, $model) {
                            if(!Yii::$app->user->isGuest){
                                if($model->user['username'] == Yii::$app->user->identity->username){ 
                                    return Html::a('<span class="glyphicon glyphicon-pencil""></span>', $url, [
                                                'title' => Yii::t('app', 'lead-update'),
                                    ]);
                                }
                            }
                        },
                        'delete' => function ($url, $model) {
                            if(!Yii::$app->user->isGuest){
                                if($model->user['username'] == Yii::$app->user->identity->username){ 
                                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                                'title' => Yii::t('app', 'lead-delete'),
                                    ]);
                                }
                            }
                        },
                    ],
                ],
            ],
        ]);
    }?>
</div>
