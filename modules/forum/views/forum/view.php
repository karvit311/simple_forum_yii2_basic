<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Forum */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Mycomments', 'url' => ['/forum/forum/mycomment?id='.Yii::$app->user->identity->id]];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="forum-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'hash' => $model->hash], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id,'hash' => $model->hash], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'message:ntext',
            'created_at',
            'context',
        ],
    ]) ?>

</div>

