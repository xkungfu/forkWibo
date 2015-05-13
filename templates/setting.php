<?php

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link href="http://cdn.bootcss.com/semantic-ui/0.16.1/css/semantic.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/0.16.1/css/semantic.min.css">
    <link rel="stylesheet" href="../assets/css/home.css"/>
    <title>forkWibo</title>
</head>
<body>

<div class="ui secondary menu">
    <a class="item" href=<?php echo HOMEPAGE.'/home'; ?>>
        <h2>forkWibo</h2>
    </a>
    <a class="right item">
        <img src=<?php echo $userInfo['profile_image_url']; ?> alt=""/>
    </a>
</div>
<br/>
<div class="ui secondary menu">
    <a class="item">
        <h3>定时微博设置</h3>
    </a>
</div>

<div class="ui form segment">
    <div class="three fields">
        <div class="field">
            <input type="text" name="screen-name" id="screen-name" placeholder="关注者昵称"/>
        </div>
        <div class="field">
            <div class="ui fluid selection dropdown">
                <div class="text">频率</div>
                <i class="dropdown icon"></i>
                <input type="hidden" name="timing" id="timing">
                <div class="menu">
                    <div class="item" data-value="1 天">1 天(默认)</div>
                    <div class="item" data-value="10 分钟">10 分钟</div>
                    <div class="item" data-value="1 小时">1 小时</div>
                    <div class="item" data-value="4 小时">4 小时</div>
                </div>
            </div>
        </div>
        <div class="field">
            <div class="ui blue submit button" id="add-timing">
                添加
            </div>
            <!--<div class="ui button" id="select">
                select value
            </div>-->
        </div>
    </div>

</div>

<div class="content">
    <div class="ui feed segment timing-old">
        <?php
        foreach($timingInfo as $singleTiming):
            ?>
            <div class="event">
                <div class="label">
                    <img src=<?php echo $singleTiming['to_profile_image_url'] ?> alt="to_profile_image_url"/>
                </div>
                <div class="content">
                    <div class="date">
                        <div class="ui icon button">
                            <i class="remove icon" id="remove-timing" data-touid=<?php echo $singleTiming['to_uid']; ?>></i>
                        </div>
                    </div>
                    <div class="summary">
                        <a class="link" href=<?php echo WEIBO.'/'.$singleTiming['to_uid']; ?>><?php echo $singleTiming['to_screen_name']; ?></a>
                    </div>
                    <div class="extra text">
                        <?php echo $singleTiming['timing']; ?>
                    </div>
                </div>
            </div>
        <?php
        endforeach;
        ?>
    </div>
</div>


<script type="text/x-handlerbars-template" id="template">
    <div class="ui feed segment">
        {{#each this}}
        <div class="event">
            <div class="label">
                <img src={{to_profile_image_url}} alt="to_profile_image_url"/>
            </div>
            <div class="content">
                <div class="date">
                    <div class="ui icon button">
                        <i class="remove icon" id="remove-timing" data-touid={{to_uid}}></i>
                    </div>
                </div>
                <div class="summary">
                    <a class="link" href={{user_url_helper}}>{{to_screen_name}}</a>
                </div>
                <div class="extra text">
                    {{timing}}
                </div>
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

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/0.16.1/javascript/semantic.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/2.0.0/handlebars.min.js"></script>
<script>
    (function(){
        $('.ui.selection.dropdown').dropdown();
        var source = $('#template').html();
        var template = Handlebars.compile(source);
        Handlebars.registerHelper('user_url_helper',function(to_uid){
            return 'http://weibo.com/'+to_uid;
        });
        //添加定时用户
        $('#add-timing').on('click',function(){
            var screenName = $('#screen-name').val();

            var timingVal = $('#timing').val();
            //timing默认1天
            if(!timingVal){
                timingVal = '1 天';
            }
//            console.log(screenName);
            if(screenName){
                $.ajax({
                    url: '/fpi/user/addTiming?screen_name='+screenName+'&timing='+timingVal,
                    type: 'GET',
                    contentType: 'application/json',
                    success: function(ret){
                        console.log('success');
//                        console.log(JSON.parse(ret));
                        var timingSetting = JSON.parse(ret);
                        var errorCode = timingSetting['errorCode'];
                        if(1971==errorCode){
                            $('#error-info').remove();
                            $('div.ui.form.segment').append('<div class="ui red label" id="error-info"><i class="attention icon"></i>尚未关注该用户 OR <a href="http://forkwibo.sinaapp.com/rules#api" target="_blank">微博API限制</a></div>');
                        }else if(1972==errorCode){
                            $('#error-info').remove();
                            $('div.ui.form.segment').append('<div class="ui red label" id="error-info"><i class="attention icon"></i>超过人数限制</div>');
                        }else if(1973==errorCode){
                            $('#error-info').remove();
                            $('div.ui.form.segment').append('<div class="ui red label" id="error-info"><i class="attention icon"></i>该用户已设置定时</div>');
                        }else if(1970==errorCode){
                            $('#error-info').remove();
                            $('div.ui.form.segment').append('<div class="ui red label" id="error-info"><i class="attention icon"></i>未知错误</div>');
                        }else if(0==errorCode){
                            //正确后置空
                            $('#screen-name').val('');

//                            console.log(timingSetting);

                            //返回正常
                            $('#error-info').remove();
                            $('.timing-old').remove();
                            $('.content').append( template(timingSetting['timingSetting']) );
                            $('.content > .ui.feed.segment').addClass('timing-old');
                            console.log('timing reload');
                        }
//                        console.log(timingSetting['timingSetting']);
                    },
                    error: function(xhr,status,errorThrown){
                        console.log('error');
                    },
                    complete: function(xhr,status){
                        console.log('complete');
                    }
                });
            }else{
                $('#error-info').remove();
                $('div.ui.form.segment').append('<div class="ui red label" id="error-info"><i class="attention icon"></i>关注者昵称不可为空</div>');
            }
        });
        //删除已添加的用户
        //bug!
        $('body').delegate('#remove-timing','click',function(){
            var toUid = $(this).data('touid');
            if(toUid){
                $.ajax({
                    url: '/fpi/user/removeTiming?to_uid='+toUid,
                    type: 'GET',
                    contentType: 'application/json',
                    success: function(ret){
                        var timingSetting = JSON.parse(ret);
                        var errorCode = timingSetting['errorCode'];
                        //删除成功
                        if(0==errorCode){
                            $('.timing-old').remove();
                            $('.content').append( template(timingSetting['timingSetting']) );
                            $('.content > .ui.feed.segment').addClass('timing-old');
                            console.log('timing reload');
                        }
                    },
                    error: function(xhr,status,errorThrown){

                    },
                    complete: function(xhr,status){
                        console.log('complete');
                    }
                });
            }

//            console.log(toUid);
        })

        //..
        $('#select').on('click',function(){
            var select = $('#timing').val();
            if(select){
                console.log(select);
            }
        });
    })()
</script>

</body>
</html>