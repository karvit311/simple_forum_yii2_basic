<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\forum\models\Questions */

$this->title = 'Create Questions';
$id = $_GET['id'];
$this->params['breadcrumbs'][] = ['label' => 'Questions', 'url' => ['index?id='.$id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="questions-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>