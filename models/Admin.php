<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "admin".
 *
 * @property integer $id
 * @property string $login
 * @property string $pass
 * @property string $phone
 * @property integer $view_orders
 * @property integer $accept_orders
 * @property integer $delete_orders
 * @property integer $add_tovar
 * @property integer $edit_tovar
 * @property integer $delete_tovar
 * @property integer $accept_reviews
 * @property integer $delete_reviews
 * @property integer $view_clients
 * @property integer $delete_clients
 * @property integer $add_news
 * @property integer $delete_news
 * @property integer $add_category
 * @property integer $delete_category
 * @property integer $view_admin
 * @property string $fio
 * @property string $role
 * @property string $email
 */
class Admin extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['view_orders', 'accept_orders', 'delete_orders', 'add_tovar', 'edit_tovar', 'delete_tovar', 'accept_reviews', 'delete_reviews', 'view_clients', 'delete_clients', 'add_news', 'delete_news', 'add_category', 'delete_category', 'view_admin'], 'integer'],
            [['fio'], 'string'],
            [['login', 'pass', 'role'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 50],
            [['email'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Login',
            'pass' => 'Pass',
            'phone' => 'Phone',
            'view_orders' => 'View Orders',
            'accept_orders' => 'Accept Orders',
            'delete_orders' => 'Delete Orders',
            'add_tovar' => 'Add Tovar',
            'edit_tovar' => 'Edit Tovar',
            'delete_tovar' => 'Delete Tovar',
            'accept_reviews' => 'Accept Reviews',
            'delete_reviews' => 'Delete Reviews',
            'view_clients' => 'View Clients',
            'delete_clients' => 'Delete Clients',
            'add_news' => 'Add News',
            'delete_news' => 'Delete News',
            'add_category' => 'Add Category',
            'delete_category' => 'Delete Category',
            'view_admin' => 'View Admin',
            'fio' => 'Fio',
            'role' => 'Role',
            'email' => 'Email',
        ];
    }
}
