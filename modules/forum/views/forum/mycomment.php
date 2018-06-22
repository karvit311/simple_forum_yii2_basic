<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

?>
<table id="mycomment" class="display" width="100%" cellspacing="0">
    <thead>
        <tr>
            <th>Topics</th>
            <th>Questions</th>
            <th>Comments</th>
            <th>Created at</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($mycomments as $key => $mycomment) {?>
            <tr>
                <td><?= $mycomment->registrations['name']?></td>
                <td><?= $mycomment->customer['name']?></td>
                <td><?= $mycomment->message; ?></td>
                <td> <?= $mycomment->created_at; ?></td>
                <td style="width: 4%; padding:0; "><?= Html::a('Update', ['update', 'hash' => $mycomment->hash], ['class' => 'btn editMyButton btn-primary']) ?></td>
                <td style="width: 4%; padding:0;"><?= Html::a('Delete', ['delete', 'id' => $mycomment->id,'hash' => $mycomment->hash], [
                    'class' => 'btn editMyButton btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                        ],
                    ]) ?>
                </td>
            </tr>
        <?php }?>
    </tbody>
    <tfoot>
        <tr>
            <th>Name</th>
            <th>Position</th>
            <th>Office</th>
            <th>Extn.</th>
            <th>Start date</th>
            <th>Salary</th>
        </tr>
    </tfoot>
</table>

