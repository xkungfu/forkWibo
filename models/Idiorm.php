<?php

//SAE Mysql 不支持innoDB，故使用idiorm(若支持则使用ReadBean)

require_once __DIR__.'/../vendor/autoload.php';

class Idiorm {

    private static $idiorm = null;

    public static function instance(){

        if(!isset(self::$idiorm)){
            self::$idiorm = new Idiorm();
        }
        return self::$idiorm;
    }

    public function __construct(){
        ORM::configure('mysql:host='.SAE_MYSQL_HOST_M.';port='.SAE_MYSQL_PORT.';dbname='.SAE_MYSQL_DB);
        ORM::configure('username',SAE_MYSQL_USER);
        ORM::configure('password',SAE_MYSQL_PASS);

        ORM::configure('return_result_sets', true);

        ORM::configure('driver_options',array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
    }

}