<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\RegistretionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>

<div class="registration-index">
    <div id="imgAfterConfirm">
    
        <?= Html::img('/images/17.png',['width'=> '1040px', 'height' => '200px','alt' => 'Profile Picture', 'class' => 'img img-rounded']);?>
        
    </div>
    <?php  if ((!Yii::$app->user->isGuest) && (Yii::$app->user->identity->username == 'admin' )){ ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'summary' => '',
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                ['attribute' =>  'name',
                    'value' => function($data){
                        return Html::a(Html::encode($data->name), Url::to(['/forum/questions/index', 'id' => $data->id]));
                        },
                    'format' => 'raw',
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
                    'attribute' => 'count',
                    'value' =>  function ($model) {
                        if($model->questions['count'] !== null){ 
                            return $model->questions['count'];
                        }else{
                            return 0;
                        }
                    },
                    
                ],
                [ 
                'label' =>'Date',
                'value' => 'created_at'
                     
                ],
                ['class' => 'yii\grid\ActionColumn',
                ],
            ],
        ]); ?>
    <?php } else { ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'summary' => '',
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                ['attribute' =>  'name',
                'value' => function($data){
                    return Html::a(Html::encode($data->name), Url::to(['/forum/questions/index', 'id' => $data->id]));
                    },
                'format' => 'raw',
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
                    'attribute' => 'count',
                    'value' =>  function ($model) {
                        if($model->questions['count'] !== null){ 
                            return $model->questions['count'];
                        }else{
                            return 0;
                        }
                    },
                    
                ],
                [ 
                'label' =>'Date',
                'value' => 'created_at', 
                ],
                ['class' => 'yii\grid\ActionColumn',
                    'visible' => '0',
                ],
            ],
        ]);
    }?>
</div>

    

 