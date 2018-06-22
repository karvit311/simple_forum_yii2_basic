<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
/**
 * Инициализатор RBAC выполняется в консоли php yii rbac/init
 */
class RbacController extends Controller {

    public function actionInit() {
        $auth = Yii::$app->authManager;
        
        $auth->removeAll(); //На всякий случай удаляем старые данные из БД...
        
        // Создадим роли админа и редактора новостей
        $admin = $auth->createRole('admin');
        $editor = $auth->createRole('editor');
        
        // запишем их в БД
        $auth->add($admin);
        $auth->add($editor);
        
        $authorRule = new \app\rbac\AuthorRule;
        $auth->add($authorRule);

        $adminRule = new \app\rbac\AdminRule;
        $auth->add($adminRule);
        

        $viewAdminPage = $auth->createPermission('viewAdminPage');
        $viewAdminPage->description = 'Просмотр админки';
        
        $updateNews = $auth->createPermission('updateNews');
        $updateNews->description = 'Редактирование новости';
        

        $updateOwnNews = $auth->createPermission('updateOwnNews');
        $updateOwnNews->description = 'Редактирование собственной новости';
        $updateOwnNews->ruleName = $authorRule->name;

        $updateRegistrations = $auth->createPermission('updateRegistrations');
        $updateRegistrations->description = 'Редактирование главных топиков';
        $updateRegistrations->ruleName = $adminRule->name;
        
        $auth->add($viewAdminPage);
        $auth->add($updateNews);
        $auth->add($updateOwnNews);
        $auth->add($updateRegistrations);

        // Теперь добавим наследования. Для роли editor мы добавим разрешение updateOwnNews (редактировать собственную новость),
        // а для админа добавим собственные разрешения viewAdminPage и updateNews (может смотреть админку и редактировать любую новость)
        
        // Роли «Редактор новостей» присваиваем разрешение «Редактирование собственной новости»
        $auth->addChild($editor,$updateOwnNews);
        $auth->addChild($admin, $updateNews);
        $auth->addChild($admin, $viewAdminPage);
        $auth->addChild($admin, $updateRegistrations);


        // Назначаем роль admin пользователю с ID 1
        $auth->assign($admin, 1); 
        
        // Назначаем роль editor пользователю с ID 2
        $auth->assign($editor, 2);
        $auth->assign($editor, 7);
    }
}
