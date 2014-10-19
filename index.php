<?php

require_once __DIR__.'/main.php';

$app = new \Slim\Slim(array(
    'name'=>'newsbiu',
    'mode'=>'development',
    'templates.path'=>'./templates',
    'cookies.encrypt'=>false,
    'cookies.lifetime'=>'7 days',
    'cookies.path'=>'/',
    'cookies.secure'=>false,
    'cookies.httponly'=>false,
    'weibo'=>Weibo::instance(),
    'user'=>User::instance(),
    'cron'=>Cron::instance(),
));

/*
 * development配置
 * */
$app->configureMode('development',function() use ($app){
    $app->config(array(
        'debug'=>true,
        'log.enabled'=>true,
        'log.level'=>\Slim\Log::DEBUG,
    ));
});
/*
 * production配置
 * */
$app->configureMode('production',function() use($app){
    $app->config(array(
        'debug'=>false,
        'log.enabled'=>true,
        'log.level'=>\Slim\Log::ERROR,
    ));
});

//建立数据库连接
$idiorm = Idiorm::instance();

/*
 * 维护模式
 * */
/*$app->get('/:maintain',function($maintain) use($app){
    $app->render('maintain.php');
})->conditions(array('maintain'=>'.*'));*/

$app->get('/',function() use ($app){
    $weibo = $app->config('weibo');
    $authorizeUrl = $weibo->getAuthorizeUrl();
    $pass = array();
    $pass['authorizeUrl'] = $authorizeUrl;
    $app->render('index.php',$pass);
});

$app->get('/weibo',function() use ($app){
    $redirect = false;
    //获取code
    $code = $app->request->get('code');
    if(!empty($code)){
        $weibo = $app->config('weibo');
        $tokenInfo = $weibo->getAccessToken($code);
        $userInfo = $weibo->getUser($tokenInfo['access_token'],$tokenInfo['uid']);
        $userToSave = array();
        $userToSave['uid'] = $tokenInfo['uid'];
        $userToSave['screen_name'] = $userInfo['screen_name'];
        $userToSave['profile_image_url'] = $userInfo['profile_image_url'];
        $userToSave['access_token'] = $tokenInfo['access_token'];
        $userToSave['expires_in'] = $tokenInfo['expires_in'];
        //保存accessToken
        $user = $app->config('user');
        //用户已存在
        if($user->userExistsByUID($tokenInfo['uid'])){
            $result = $user->refreshUser($userToSave);
        }else{
            $result = $user->saveUser($userToSave);
        }
        if($result){
            //添加cookie
            $app->response->setCookie('forkWiboUser',$tokenInfo['access_token']);
            $app->redirect('/home');
        }else{
            $redirect = true;
        }
    }else{
        $redirect = true;
    }

    if($redirect){
        $app->redirect('/error');
    }
});

/*
 * 用户主页面
 * */
$app->get('/home',function() use ($app){
    $cookies = $app->request->cookies;
    $cookie = $cookies['forkWiboUser'];
    $tab = $app->request->get('tab');
    if(!empty($cookie)){
        $user = $app->config('user');
        if($user->userExistsByCookie($cookie)){
            $userInfo = $user->getUserByCookie($cookie);
            $pass = array();
            $pass['userInfo'] = $userInfo;
            if(empty($tab) || 'real'==$tab){
                //实时微博
                $weibo = $app->config('weibo');
                $friendsWeibo = $weibo->getFriendsWeibo($cookie,0,15);
                $pass['tab'] = $tab;
                $pass['friendsWeibo'] = $friendsWeibo['statuses'];
                $app->render('home.php',$pass);
            }else if('timing'==$tab){
                //定时微博
                $timingWeibo = $user->getUserTimingWeibo($userInfo['uid'],15);
                $pass['tab'] = $tab;
                $pass['timingWeibo'] = $timingWeibo;
                $app->render('home.php',$pass);
            }else if('saved'==$tab){
                //保存的微博
            }else{
                $app->redirect('/error');
            }
        }else{
            //用户不存在
            $app->redirect('/error');
        }
    }else{
        $app->redirect('/');
    }
});

/*
 * 定时设置页面
 * */
$app->get('/setting',function() use ($app){
    $redirect = false;
    $cookies = $app->request->cookies;
    $cookie = $cookies['forkWiboUser'];
    $userInfo = array();
    $timingInfo = array();
    if(!empty($cookie)){
        $user =  $app->config('user');
        if($user->userExistsByCookie($cookie)){
            $userInfo = $user->getUserByCookie($cookie);
            $timingInfo = $user->getUserTimingSetting($userInfo['uid']);
        }else{
            $redirect = true;
        }
    }else{
        $redirect = true;
    }

    $pass = array();
    $pass['userInfo'] = $userInfo;
    $pass['timingInfo'] = $timingInfo;
    if($redirect){
        $app->redirect('/error');
    }else{
        $app->render('setting.php',$pass);
    }
});

/*
 * rules页面
 * */
$app->get('/rules',function() use ($app){
    $app->render('rules.php');
});

/*
 * 出错页面
 * */
$app->get('/error',function() use ($app){
    $app->render('error.php');
});

/*
 * FPI
 * */
$app->group('/fpi',function() use ($app){
    //有关微博的接口
    $app->group('/weibo',function() use ($app){
        //关注
        $app->get('/following',function() use ($app){

        });
    });
    //有关用户的接口
    $app->group('/user',function() use ($app){
        /*
         * error code
         * 1970:未知错误
         * 1971:未关注该用户
         * 1972:超过人数限制
         * 1973:该用户已设置定时
         * */
        //添加定时用户
        $app->get('/addTiming',function() use ($app){
            $cookies = $app->request->cookies;
            $cookie = $cookies['forkWiboUser'];
            $toScreenName = $app->request->get('screen_name');
            $timing = $app->request->get('timing');
            $pass = array();
            if(!empty($cookie) && !empty($toScreenName) && !empty($timing)){
                $user = $app->config('user');
                $userInfo = $user->getUserByCookie($cookie);
                $weibo = $app->config('weibo');

                $pass = array();
                $pass['errorCode'] = 0;

                //获取total_number
                $followingInfo4Total = $weibo->getUserFollowing($cookie,$userInfo['uid'],0,0);
                $totalNumber = $followingInfo4Total['total_number'];
                //接口可实际返回用户数量
                $actualNum = (int)($totalNumber * 0.3);
                $followingInfo = $weibo->getUserFollowing($cookie,$userInfo['uid'],$actualNum,0);
                $toUserInfo = $user->isFollowing($toScreenName,$followingInfo['users']);
                $isFollowing = false;
                $toUid = '';
                $toProfileImageUrl = '';
                if(!empty($toUserInfo)){
                    $isFollowing = true;
                    $toUid = $toUserInfo['uid'];
                    $toProfileImageUrl = $toUserInfo['profile_image_url'];
                }

                //是否关注该用户？
                if($isFollowing){
                    //关注数是否超过5个？
                    $timingCount = $user->timingCount($userInfo['uid']);
                    if($timingCount < 5){
                        if(!$user->timingExists($userInfo['uid'],$toUid)){
                            $timingInfo = array();
                            $timingInfo['uid'] = $userInfo['uid'];
                            $timingInfo['screen_name'] = $userInfo['screen_name'];
                            //cookie被故意修改
                            $timingInfo['access_token'] = $cookie;
                            $timingInfo['to_uid'] = $toUid;
                            $timingInfo['to_screen_name'] = $toScreenName;
                            $timingInfo['to_profile_image_url'] = $toProfileImageUrl;
                            $timingInfo['timing'] = $timing;
                            $user->addTiming($timingInfo);
                            //获取定时设置
                            $userTimingSetting = $user->getUserTimingSetting($userInfo['uid']);
                            $timingSetting = array();
                            foreach($userTimingSetting as $singleTiming){
                                $tmp = array();
                                $tmp['uid'] = $singleTiming['uid'];
                                $tmp['screen_name'] = $singleTiming['screen_name'];
                                $tmp['to_uid'] = $singleTiming['to_uid'];
                                $tmp['to_screen_name'] = $singleTiming['to_screen_name'];
                                $tmp['to_profile_image_url'] = $singleTiming['to_profile_image_url'];
                                $tmp['timing'] = $singleTiming['timing'];
                                $timingSetting[] = $tmp;
                            }
                            $pass['errorCode'] = 0;
                            $pass['timingSetting'] = $timingSetting;
                        }else{
                            $pass['errorCode'] = 1973;
                        }
                    }else{
                        $pass['errorCode'] = 1972;
                    }
                }else{
                    $pass['errorCode'] = 1971;
                }
            }else{
                $pass['errorCode'] = 1970;
            }
            echo json_encode($pass);
        });
        //删除定时用户
        $app->get('/removeTiming',function() use ($app){
            $cookies = $app->request->cookies;
            $cookie = $cookies['forkWiboUser'];
            $toUid = $app->request->get('to_uid');
            $pass = array();
            if(!empty($cookie) && !empty($toUid)){
                $user = $app->config('user');
                $userInfo = $user->getUserByCookie($cookie);
                //可能to_uid错误？
                $timingInfo = array();
                $timingInfo['uid'] = $userInfo['uid'];
                $timingInfo['to_uid'] = $toUid;
                //删除成功
                if($user->removeTiming($timingInfo)){
                    //获取定时设置
                    $userTimingSetting = $user->getUserTimingSetting($userInfo['uid']);
                    $timingSetting = array();
                    foreach($userTimingSetting as $singleTiming){
                        $tmp = array();
                        $tmp['uid'] = $singleTiming['uid'];
                        $tmp['screen_name'] = $singleTiming['screen_name'];
                        $tmp['to_uid'] = $singleTiming['to_uid'];
                        $tmp['to_screen_name'] = $singleTiming['to_screen_name'];
                        $tmp['to_profile_image_url'] = $singleTiming['to_profile_image_url'];
                        $tmp['timing'] = $singleTiming['timing'];
                        $timingSetting[] = $tmp;
                    }
                    $pass['errorCode'] = 0;
                    $pass['timingSetting'] = $timingSetting;
                }
            }else{
                //未知错误
                $pass['errorCode'] = 1970;
            }
            echo json_encode($pass);
        });
        //获取更多微博
        $app->get('/getWeibo',function() use ($app){
            //cookie获取 合一
            $cookies = $app->request->cookies;
            $cookie = $cookies['forkWiboUser'];
            $count = $app->request->get('count');
            $pass = array();
            if(!empty($cookie) && !empty($count)){
//                $user = $app->config('user');
//                $userInfo = $user->getUserByCookie($cookie);
                $count = (int)$count * 15;
                $weibo = $app->config('weibo');
                $friendsWeibo = $weibo->getFriendsWeibo($cookie,0,$count);
                if(!empty($friendsWeibo['statuses'])){
                    $pass['errorCode'] = 0;
                    $pass['statuses'] = $friendsWeibo['statuses'];
                }else{
                    //1974 获取用户微博失败
                    $pass['errorCode'] = 1974;
                }
            }
            echo json_encode($pass);
        });

    });
});

/*
 * cron
 * */
$app->get('/cron/cron10min',function() use ($app){
    $cron = $app->config('cron');
    var_dump($cron->run('10 分钟'));
});
$app->get('/cron/cron1hour',function() use ($app){
    $cron = $app->config('cron');
    $cron->run('1 小时');
});
$app->get('/cron/cron4hour',function() use ($app){
    $cron = $app->config('cron');
    $cron->run('4 小时');
});
$app->get('/cron/cron1day',function() use ($app){
    $cron = $app->config('cron');
    $cron->run('1 天');
});
$app->get('/cron/cron2day',function() use ($app){
    $cron = $app->config('cron');
    $cron->emptyTimingWeibo();
});



$app->run();