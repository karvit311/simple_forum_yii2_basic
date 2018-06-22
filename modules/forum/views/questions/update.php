<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\forum\models\Questions */

$this->title = 'Update Questions: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Create questions in this topic', 'url' => ['index', 'id' => $idRegistretion->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="questions-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
