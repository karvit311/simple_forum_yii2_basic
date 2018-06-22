<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

?>
<?php 
// echo Yii::$app->user->identity->username;
  
       foreach($admin as $admin_name){
            if(!Yii::$app->user->isGuest && $admin_name->login ==Yii::$app->user->identity->username){
                 $final_admin_name = $admin_name->login;
           
        if(!Yii::$app->user->isGuest && $final_admin_name==Yii::$app->user->identity->username){?>
    	<p id="reg-auth-title" align="right"><a  class="top-auth" href="/admin/admin/login">Вход в админку</a></p>
    		<?php }
             }
        }
    ?>

<div id="imgAfterConfirm">
    <?= Html::img('/images/17.png',['width' => '1040px', 'height' => '200px','alt' => 'Profile Picture', 'class' => 'img img-rounded']);?>
</div>
<div id="linkToForum">
    <?= Html::a('<strong>GO TO FORUM </strong>', ['/forum/registrations/index'], ['class' => 'btn btn-primary']) ?>
</div>


 