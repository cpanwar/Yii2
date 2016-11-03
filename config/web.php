<?php

$params = require(__DIR__ . '/params.php');
putenv("ODBCINI=/etc/odbc.ini");
$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'urlManager' => [
                'enablePrettyUrl' => true,
                'enableStrictParsing' => true,
                'showScriptName' => false,
                'baseUrl' => '/',
                'rules' => [
                                //['class' => 'yii\rest\UrlRule', 'controller' => 'invoice'],
                                'GET invoice'  => 'invoice/list',
                                'POST invoice' => 'invoice/create',
                                'PUT <invoice:[\w-]+>/<id:\d+>'    => '<invoice>/update',
                                'DELETE <invoice:[\w-]+>/<id:\d+>' => '<invoice>/delete',
                                '<invoice:[\w-]+>/<id:\d+>'        => '<invoice>/view',
                           ]         
                ],    
        'request' => [
            'class' => 'yii\web\Request',
            //'enableCookieValidation' => false,
            'cookieValidationKey' => 'kO8iHt9xUGpPne1zGgy0rqncBNmHnc86',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],        
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [

            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
    ],
    'params' => $params,
];


if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
