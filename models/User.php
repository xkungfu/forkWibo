<?php

require_once __DIR__.'/../vendor/autoload.php';

class User {

    private static $user = null;

    public static function instance(){

        if(!isset(self::$user)){
            self::$user = new User();
        }
        return self::$user;
    }

    /*
     * 保存用户
     * */
    public function saveUser(array $userInfo){

        //save返回？true,false
        $user = ORM::for_table('user')->create();
        $user->uid = $userInfo['uid'];
        $user->screen_name = $userInfo['screen_name'];
        $user->profile_image_url = $userInfo['profile_image_url'];
        $user->access_token = $userInfo['access_token'];
        $user->expires_in = $userInfo['expires_in'];
        return $user->save();

    }
    /*
     * 用户已存在，刷新
     * */
    public function refreshUser(array $userInfo){

        //ORM update 无用

        //row_execute
        $uid = $userInfo['uid'];
        $screen_name = $userInfo['screen_name'];
        $profile_image_url = $userInfo['profile_image_url'];
        $access_token = $userInfo['access_token'];
        $expires_in = $userInfo['expires_in'];
        return ORM::raw_execute("UPDATE user SET screen_name = '$screen_name',profile_image_url = '$profile_image_url',access_token = '$access_token',expires_in = '$expires_in' WHERE uid = '$uid' ");
    }

    /*
     * 判断用户是否存在（避免改cookie
     * */
    public function userExistsByUID($uid){

        $user = ORM::for_table('user')
                ->where('uid',$uid)
                ->find_one();
        if($user){
            return true;
        }else{
            return false;
        }
    }
    public function userExistsByCookie($cookie){

        $user = ORM::for_table('user')
                ->where('access_token',$cookie)
                ->find_one();
        if($user){
            return true;
        }else{
            return false;
        }
    }

    /*
     * 获取用户信息
     * */
    public function getUserByCookie($cookie){

        $user = ORM::for_table('user')
                ->where('access_token',$cookie)
                ->find_one();
        return $user;
    }

    /*
     * 获取用户定时微博，默认15？no
     * 全部返回
     * */
    public function getUserTimingWeibo($uid,$limit=15){

        $weibo = ORM::for_table('weibo')
                 ->where('uid',$uid)
//        没有限制
//                 ->limit($limit)
                 ->find_array();
        return $weibo;
    }

    /*
     * 获取用户定时设置
     * */
    public function getUserTimingSetting($uid){

        $timing = ORM::for_table('timing')
                    ->where('uid',$uid)
                    ->find_many();
        return $timing;
    }

    /*
     * 用户是否关注某用户,返回用户uid,profile_image_url
     * */
    public function isFollowing($screenName,array $followingUsers){

        foreach($followingUsers as $user){
            if($screenName == $user['screen_name']){
                $toUserInfo = array();
                $toUserInfo['uid'] = $user['id'];
                $toUserInfo['profile_image_url'] = $user['profile_image_url'];
                return $toUserInfo;
            }
        }
        return array();
    }

    /*
     * 定时用户数量(判断是否超过
     * */
    public function timingCount($uid){

        $result = ORM::for_table('timing')
                    ->where('uid',$uid)
                    ->count();
        return $result;
    }

    /*
     * 定时已经存在？
     * */
    public function timingExists($uid,$to_uid){

        $result = ORM::for_table('timing')
                    ->where(array(
                        'uid'=>$uid,
                        'to_uid'=>$to_uid,
                    ))
                    ->count();
        return $result == 1;
    }

    /*
     * 添加定时
     * */
    public function addTiming(array $timingInfo){

        $timing = ORM::for_table('timing')->create();
        $timing->uid = $timingInfo['uid'];
        $timing->screen_name = $timingInfo['screen_name'];
        $timing->to_uid = $timingInfo['to_uid'];
        $timing->to_screen_name = $timingInfo['to_screen_name'];
        $timing->access_token = $timingInfo['access_token'];
        $timing->to_profile_image_url = $timingInfo['to_profile_image_url'];
        $timing->timing = $timingInfo['timing'];
        $timing->created_time = date('Y-m-d H:i:s',time());
        return $timing->save();
    }
    /*
     * 删除定时
     * */
    public function removeTiming(array $timingInfo){

        $result = ORM::for_table('timing')
                    ->where(array(
                        'uid'=>$timingInfo['uid'],
                        'to_uid'=>$timingInfo['to_uid'],
                    ))
        //add find_result_set before delete
                    ->find_result_set()
                    ->delete();
        return $result;
    }

    /*
     * 根据timing获取所有timing
     * 返回多个！！
     * */
    public function getTiming($timing){

        $timingInfo = ORM::for_table('timing')
                        ->where('timing',$timing)
        //find_many返回奇怪的东西
                        ->find_array();
        return $timingInfo;
    }

    /*
     * 保存定时微博
     * */
    public function saveTimingWeibo(array $timingWeibo){

        $weibo = ORM::for_table('weibo')->create();
        $weibo->uid = $timingWeibo['uid'];
        $weibo->to_uid = $timingWeibo['to_uid'];
        $weibo->to_screen_name = $timingWeibo['to_screen_name'];
        $weibo->to_profile_image_url = $timingWeibo['to_profile_image_url'];
        $weibo->weibo = $timingWeibo['weibo'];
        $weibo->created_time = $timingWeibo['created_time'];
        return $weibo->save();
    }

    /*
     * 更新timing的since_id,保证定时微博存储的均是最新，没有重复
     * */
    public function updateTiming(array $timingInfo){

        $since_id = $timingInfo['since_id'];
        $uid = $timingInfo['uid'];
        $to_uid = $timingInfo['to_uid'];
        return ORM::raw_execute("UPDATE timing SET since_id = '$since_id' WHERE uid = '$uid' And to_uid = '$to_uid'");
    }

    /*
     * 清空定时微博
     * */
    public function emptyTimingWeibo(){

        ORM::for_table('weibo')
        ->find_result_set()
        ->delete_many();
    }

    public function testDB(){
        $userInfo = array(
            'uid'=>123456,
            'access_token'=>'faojdfoiasjfo',
            'expires_in'=>492389
        );
        $result = $this->saveUser($userInfo);
        return $result;
    }
    public function testRefreshDB(){


    }
    public function testGetDB($cookie){

        $user = ORM::for_table('user')
                ->where('access_token',$cookie)
                ->find_one();
        return $user;
    }
    public function testCron($name){

        $test = ORM::for_table('cron')->create();
        $test->name = $name;
        $test->save();
    }

}