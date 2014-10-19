<?php

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="http://cdn.bootcss.com/semantic-ui/0.16.1/css/semantic.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/home.css"/>
    <title>forkWibo</title>
</head>
<body>

<div class="content">
    <div class="ui secondary menu">
        <a class="item" href=<?php echo HOMEPAGE.'/home'; ?>>
            <h2>forkWibo</h2>
        </a>
        <a class="right item">
            <img src=<?php echo $userInfo['profile_image_url']; ?> alt=""/>
        </a>
    </div>
    <br/>
    <div class="ui tabular menu">
        <?php
            $real = $timing = $saved = '';
            $active = 'active';
            if(empty($tab) || 'real'==$tab){
                $real = $active;
            }else if('timing'==$tab){
                $timing = $active;
            }else if('saved'==$tab){
                $saved = $active;
            }
        ?>
        <a class="<?php echo $real; ?> item link" href=<?php echo HOMEPAGE.'/home?tab=real'; ?>>实时微博</a>
        <a class="<?php echo $timing; ?> item link" href=<?php echo HOMEPAGE.'/home?tab=timing'; ?>>定时微博</a>
<!--        <a class="--><?php //echo $saved; ?><!-- item link" href=--><?php //echo HOMEPAGE.'/home?tab=saved'; ?><!-->@forkWibo(TODO)</a>-->
        <a class="<?php echo $saved; ?> item link">@forkWibo(TODO)</a>
    </div>

    <?php
        if(empty($tab) || 'real'==$tab):
    ?>
    <div class="ui list" id="real">
        <?php
            foreach($friendsWeibo as $singleWeibo):
        ?>
        <div class="ui feed segment">
            <div class="event">
                <div class="label">
                    <img src=<?php echo $singleWeibo['user']['profile_image_url']; ?> alt="profile_image_url"/>
                </div>
                <div class="content">
                    <div class="date">

                    </div>
                    <div class="summary">
                        <a class="link" href=<?php echo WEIBO.'/'.$singleWeibo['user']['id']; ?>><?php echo $singleWeibo['user']['screen_name']; ?></a>
                    </div>
                    <div class="extra text">
                        <?php echo $singleWeibo['text']; ?>
                    </div>
                    <?php
                        if(!empty($singleWeibo['pic_urls'])):
                    ?>
                    <div class="extra images">
                        <?php
                            foreach($singleWeibo['pic_urls'] as $singlePic):
                        ?>
                        <img src=<?php echo $singlePic['thumbnail_pic']; ?> alt="thumbnail_pic"/>
                        <?php
                            endforeach;
                        ?>
                    </div>
                    <?php
                        endif;
                        //微博是转发。。
                        if(!empty($singleWeibo['retweeted_status'])):
                    ?>
                    <div class="extra text">
                        <?php echo '@'.$singleWeibo['retweeted_status']['user']['screen_name'].':'.$singleWeibo['retweeted_status']['text']; ?>
                    </div>
                    <?php
                        if(!empty($singleWeibo['retweeted_status']['pic_urls'])):
                    ?>
                    <div class="extra images">
                        <?php
                            foreach($singleWeibo['retweeted_status']['pic_urls'] as $singlePicOfRetweet):
                        ?>
                        <img src=<?php echo $singlePicOfRetweet['thumbnail_pic']; ?> alt="thumbnail_pic"/>
                        <?php
                            endforeach;
                        ?>
                    </div>
                    <?php
                        endif;
                    ?>
                    <?php
                        endif;
                    ?>
                </div>
            </div>
        </div>
        <?php
            endforeach;
        ?>
    </div>
    <div class="ui center aligned basic segment" id="load-section">
        <i class="ellipsis vertical icon" data-count="1" id="loadMore"></i>
        <i class="loading icon" id="load"></i>
    </div>
    <?php
        endif;
        if('timing'==$tab):
    ?>
    <div class="ui animated blue button">
        <div class="visible content">
            <i class="settings icon"></i>
        </div>
        <div class="hidden content">
            <a class="link" id="setting" href=<?php echo HOMEPAGE.'/setting'; ?>><small>定时设置</small></a>
        </div>
    </div>
    <div class="ui list">
        <?php
            foreach($timingWeibo as $singleWeibo):
        ?>
        <div class="ui feed segment">
            <div class="event">
                <div class="label">
                    <img src=<?php echo $singleWeibo['to_profile_image_url']; ?> alt="profile_image_url"/>
                </div>
                <div class="content">
                    <div class="date">

                    </div>
                    <div class="summary">
                        <a class="link" href=<?php echo WEIBO.'/'.$singleWeibo['to_uid']; ?>><?php echo $singleWeibo['to_screen_name']; ?></a>
                    </div>
                    <div class="extra text">
                        <?php
                            $weibo = json_decode($singleWeibo['weibo'],true);
                            echo $weibo['text'];
                        ?>
                    </div>
                    <?php
                        if(!empty($weibo['pic_urls'])):
                    ?>
                        <div class="extra images">
                            <?php
                                foreach($weibo['pic_urls'] as $singlePic):
                            ?>
                            <img src=<?php echo $singlePic['thumbnail_pic']; ?> alt="thumbnail_pic"/>
                            <?php
                                endforeach;
                            ?>
                        </div>
                    <?php
                        endif;
                        //转发微博
                        if(!empty($weibo['retweeted_status'])):
                    ?>
                    <div class="extra text">
                    <?php echo '@'.$weibo['retweeted_status']['user']['screen_name'].':'.$weibo['retweeted_status']['text']; ?>
                    </div>
                    <?php
                        if(!empty($weibo['retweeted_status']['pic_urls'])):
                    ?>
                    <div class="extra images">
                    <?php
                        foreach($weibo['retweeted_status']['pic_urls'] as $singlePicOfRetweet):
                    ?>
                    <img src=<?php echo $singlePicOfRetweet['thumbnail_pic']; ?> alt="thumbnail_pic"/>
                    <?php
                        endforeach;
                    ?>
                    </div>
                    <?php
                        endif;
                    ?>
                    <?php
                        endif;
                    ?>

                </div>
            </div>
        </div>
        <?php
            endforeach;
        ?>
    </div>
    <?php
        endif;
    ?>
</div>

<script type="text/x-handlebars-template" id="template">
    <div class="ui list">
        {{#each this}}
        <div class="ui feed segment">
            <div class="label">
                <img src={{user.profile_image_url}} alt="profile_image_url"/>
            </div>
            <div class="content">
                <div class="date">
                </div>
                <div class="extra text">
                    {{text}}
                </div>
                {{#if pic_urls}}
                    <div class="extra images">
                        {{#each pic_urls}}
                        <img src={{thumbnail_pic}} alt="thumbnail_pic"/>
                        {{/each}}
                    </div>
                {{/if}}
                {{#if retweeted_status}}
                    <div class="extra text">
                        @{{retweeted_status.user.screen_name}}:{{retweeted_status.text}}
                    </div>
                    {{#if retweeted_status.pic_urls}}
                        <div class="extra images">
                            {{#each retweeted_status.pic_urls}}
                            <img src={{thumbnail_pic}} alt="thumbnail_pic"/>
                            {{/each}}
                        </div>
                    {{/if}}
                {{/if}}
            </div>
        </div>
        {{/each}}
    </div>
</script>



<div class="ui horizontal icon divider">
    <i class="circular heart icon"></i>
</div>

<div class="ui center aligned basic segment">
    <small><a class="link" href=<?php echo HOMEPAGE.'/humans.txt'; ?>>About</a> | &copy; jsxqf 2014 | <a class="link" href=<?php echo HOMEPAGE.'/rules'; ?>>Rules</a></small>
</div>

<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/handlebars.js/2.0.0/handlebars.min.js"></script>
<script>
    (function(){
        //直接使用sematic-ui loading icon

        var source = $('#template').html();
        var template = Handlebars.compile(source);

        $('#load').hide();

        $('#loadMore').on('click',function(){
            var target = $(this);
            var loadCount = target.data('count');
            loadCount = parseInt(loadCount);
            if(loadCount < 3){
                //至多加载一次
                loadCount = loadCount + 1;
                target.data('count',loadCount);
                target.hide();
                $('#load').show();

                $.ajax({
                    url: '/fpi/user/getWeibo?count='+loadCount,
                    type: 'GET',
                    //TODO
                    contentType: 'application/json',
                    success : function(ret){
                        var pass = JSON.parse(ret);
                        if(0==pass['errorCode']){
                            $('#real').remove();
                            $('.content').append( template(pass['statuses']) );
                        }else if(1974==pass['errorCode']){
                            console.log('load more fail');
                        }
                    },
                    error: function(xhr,status,errorThrown){
                        console.log('load more error');
                    },
                    complete: function(xhr,status,errorThrown){
                        $('#load').hide();
                        console.log('load more complete');
                    }

                });
            }


//            console.log(loadCount);
        });
    })()
</script>
</body>
</html>