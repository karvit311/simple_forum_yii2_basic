<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $jsOptions = [
        'position' => View::POS_HEAD
    ];
    public $css = [
        'css/site.css',
        'css/site2.css',
        'https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css',
        'css/jquery_confirm.css',
    ];
    public $js = [
        'js/site.js',
        'https://cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.js',
        'https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js',
        'js/jquery.js',
        'js/jquery-1.12.4.min.js',
        'js/jquery-1.10.2.min.map',
        'js/jquery_confirm.js',
        'js/jquery.form.validation.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'rmrevin\yii\fontawesome\AssetBundle',
    ];
}
