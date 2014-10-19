<?php

require_once __DIR__.'/../vendor/autoload.php';

use \Httpful\Request as Request;

class Weibo {

    private static $weibo = null;
    private $apiUrl = 'https://api.weibo.com';
    //change to your own app settings
    private $appKey = 'Your App Key';
    private $appSecret = 'Your App Secret';
    private $redirectUri = 'Your Redirect Uri';

    public static function instance(){

        if(!isset(self::$weibo)){
            self::$weibo = new Weibo();
        }
        return self::$weibo;
    }

    public function getAuthorizeUrl(){

        return $this->apiUrl.'/oauth2/authorize?client_id='.$this->appKey.'&redirect_uri='.$this->redirectUri.'&scope=all&display=default';
    }

    /*
     * 获取accessToken
     * */
    public function getAccessToken($code){

        //weibo api 是坑货吗
        $data = array(
            'client_id'=>$this->appKey,
            'client_secret'=>$this->appSecret,
            'grant_type'=>'authorization_code',
            'code'=>$code,
            'redirect_uri'=>$this->redirectUri
        );
        $response = Request::post($this->apiUrl.'/oauth2/access_token?client_id='.$this->appKey.'&client_secret='.$this->appSecret.'&grant_type=authorization_code&code='.$code.'&redirect_uri='.$this->redirectUri)
                    ->body($data)
                    ->send();

        return json_decode($response,true);
    }

    /*
     * 获取用户详细信息
     * */
    public function getUser($accessToken,$uid){

        $response = Request::get($this->apiUrl.'/2/users/show.json?access_token='.$accessToken.'&uid='.$uid)->send();
        return json_decode($response,true);
    }

    /*
     * 获取授权用户微博
     * 默认15条
     * */
    public function getFriendsWeibo($accessToken,$since_id = 0,$count = 15){

        $response = Request::get($this->apiUrl.'/2/statuses/friends_timeline.json?access_token='.$accessToken.'&since_id='.$since_id.'&count='.$count)->send();
        return json_decode($response,true);
    }

    /*
     * 获取授权用户关注列表（API有限制
     * */
    public function getUserFollowing($accessToken,$uid,$count,$cursor = 0){

        if(0==$count){
            $response = Request::get($this->apiUrl.'/2/friendships/friends.json?access_token='.$accessToken.'&uid='.$uid.'&cursor='.$cursor)->send();
            return json_decode($response,true);
        }else{
            $response = Request::get($this->apiUrl.'/2/friendships/friends.json?access_token='.$accessToken.'&uid='.$uid.'&count='.$count.'&cursor='.$cursor)->send();
            return json_decode($response,true);
        }

    }

    public function testGET(){

        $response = Request::get('http://httpbin.org/get')->send();
        return $response->body;
    }

    public function testPOST(){

        $data = array(
            'name'=>'hi',
            'value'=>'there'
        );
        $response = Request::post('http://httpbin.org/post')
                    ->body($data)
                    ->send();
        return $response;
    }


}