<?php

return [
    /*
      'class' => 'yii\db\Connection',
      'dsn' => 'mysql:host=192.168.13.113;dbname=yii2basic',
      'username' => 'root',
      'password' => 'Q3tech123',
      'charset' => 'utf8',
     */
    
    
            'class' => '\exchangecore\yii2\progress\driver\db\Connection',
            'driverName' => 'progress',
            'dsn' => 'odbc:Progress',
            'username' => 'sysprogress',
    
];
