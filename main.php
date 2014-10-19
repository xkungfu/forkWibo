<?php

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/models/Weibo.php';
require_once __DIR__.'/models/User.php';
require_once __DIR__.'/models/Idiorm.php';
require_once __DIR__.'/models/Cron.php';


define('HOMEPAGE','http://forkwibo.sinaapp.com');
define('WEIBO','http://weibo.com');

date_default_timezone_set('Asia/Shanghai');
