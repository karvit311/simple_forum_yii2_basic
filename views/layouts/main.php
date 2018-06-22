<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\controllers\AdminController;
use thyseus\message\models\Message;
use thyseus\message\models\LoginForm;
use dektrium\user\models\User;
use yii\helpers\Url;


AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => '<button id="brandLabel"><i class="fa fa-comments" aria-hidden="true"></i> Forum</button>',
        'brandUrl' => [ '/forum/registrations/index'],
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $messagelabel = '<span class="glyphicon glyphicon-envelope"> </span>';
    $user = Yii::$app->user->id;
    $unread = Message::find()->where(['to' => $user, 'status' => 0])->count();
    if ($unread > 0)
        $messagelabel .= '(' . $unread . ')';
        echo Nav::widget([
            'encodeLabels' => false, // important to display HTML-code (glyphicons)
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => [
                ['label' => '<i class="fa fa-user" aria-hidden="true"></i> Account', 'url' => ['/user/'. Yii::$app->user->id ],
                'visible' => !Yii::$app->user->isGuest,
                ],
                [
                'label' => $messagelabel,
                'url' => '',
                'visible' => !Yii::$app->user->isGuest, 
                'items' => [

                    ['label' => 'Inbox', 'url' => ['/message/message/inbox']],
                    ['label' => 'Sent', 'url' => ['/message/message/sent']],
                    ['label' => 'Compose a Message', 'url' => ['/message/message/compose']],
                    ['label' => 'Manage your Ignorelist', 'url' => ['/message/message/ignorelist']],
                    ]

                ],
                ['label' => '<i class="fa fa-commenting" aria-hidden="true"></i> My comments', 'url' => ['/forum/forum/mycomment?id='. Yii::$app->user->id,],
                'visible' => !Yii::$app->user->isGuest,
                ],
             
                [
                    'label' => '<i class="fa fa-cogs" aria-hidden="true"></i> Settings',
                    'visible' => !Yii::$app->user->isGuest,
                    'items' => [
                        '<li class="divider"></li>',
                        '<li class="dropdown-header">Settings</li>',
                        ['label' => 'Account', 'url' => '/user/settings/account'],
                        ['label' => 'Profile', 'url' => '/user/settings/profile'],
                        ['label' => 'Networks', 'url' => '/user/settings/networks'],
                        
                    ],
                ],
                Yii::$app->user->isGuest ? 

                    ['label' => '<i class="fa fa-sign-in" aria-hidden="true"></i> Sign in', 'url' => ['/user/security/login']] :

                    ['label' => '<i class="fa fa-sign-out" aria-hidden="true"></i> Sign out (' . Yii::$app->user->identity->username . ')',
                        'url' => ['/user/security/logout'],
                        'linkOptions' => ['data-method' => 'post']],
                    ['label' => '<i class="fa fa-sign-in" aria-hidden="true"></i> Register', 'url' => ['/user/registration/register'], 'visible' => Yii::$app->user->isGuest],
                    ],
                ]);
                
        NavBar::end();

    ?>
    <div class="container">
        <?= 
            Breadcrumbs::widget([
                'homeLink' => [ 
                    'label' => Yii::t('yii', 'Home'),
                    'url' => '/forum/registrations/index',
                ],
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) 
        ?>
        <button id="scrollDown"  onclick="scrollDown()"><i class="fa fa-arrow-down fa-2x" aria-hidden="true"></i></button>
        <?= $content ?>
    </div>
</div>
<button id="scrollUp" onclick="scrollWin()"><i class="fa fa-arrow-up fa-2x" aria-hidden="true"></i></button>
<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Immigration forum<?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
