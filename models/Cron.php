<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../main.php';

class Cron {

    private static $cron = null;

    public static function instance(){

        if(!isset(self::$cron)){
            self::$cron = new Cron();
        }
        return self::$cron;
    }

    /*
     * 运行定时任务
     * */
    public function run($timing){
        $msg = 'yo';
        $user = User::instance();
        $timingInfo = $user->getTiming($timing);

        $weibo = Weibo::instance();
        if($timingInfo){
            $msg = 'timingInfo not empty';
            //有用户设置了该时间间隔
            foreach($timingInfo as $singleTiming){
                $friendsWeibo = $weibo->getFriendsWeibo($singleTiming['access_token'],$singleTiming['since_id'],100);
                $statuses = $friendsWeibo['statuses'];
                $msg = 'friends weibo';
                $sinceIds = array();
                $now = date('Y-m-d H:i:s',time());
                $data = array();
                $data['timingInfo'] = $timingInfo;
                $data['statuses'] = $statuses;
                foreach($statuses as $singleWeibo){
                    if($singleTiming['to_uid']==$singleWeibo['user']['id']){
                        //微博用户uid和定时to_uid相等，表示需要记录
                        //其实只需第一个（最大）的since_id
                        $sinceIds[] = $singleWeibo['id'];
                        $timingWeibo = array();
                        $timingWeibo['uid'] = $singleTiming['uid'];
                        $timingWeibo['to_uid'] = $singleWeibo['user']['id'];
                        $timingWeibo['to_screen_name'] = $singleWeibo['user']['screen_name'];
                        $timingWeibo['to_profile_image_url'] = $singleWeibo['user']['profile_image_url'];
                        //直接保存json数据
                        $timingWeibo['weibo'] = json_encode($singleWeibo);
                        $timingWeibo['created_time'] = $now;
                        $msg = 'saveTimingWeibo';
                        $user->saveTimingWeibo($timingWeibo);
                    }
                }
                //更新since_id
                if(!empty($sinceIds)){
                    $updateTimingInfo = array();
                    $updateTimingInfo['uid'] = $singleTiming['uid'];
                    $updateTimingInfo['to_uid'] = $singleTiming['to_uid'];
                    $updateTimingInfo['since_id'] = $sinceIds[0];
                    $user->updateTiming($updateTimingInfo);
                }

            }
        }else{
            $msg = 'empty timingInfo';
        }
        return $msg;
    }

    /*
     * 定时清空定时微博
     * */
    public function emptyTimingWeibo(){
        $user = User::instance();
        $user->emptyTimingWeibo();
    }

}