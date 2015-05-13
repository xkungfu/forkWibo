<?php

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link href="http://cdn.bootcss.com/semantic-ui/0.16.1/css/semantic.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/0.16.1/css/semantic.min.css">
    <link rel="stylesheet" href="../assets/css/main.css"/>
    <title>forkWibo</title>
</head>
<body>

    <a href="https://github.com/xuqingfeng/forkWibo" target="_blank"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://camo.githubusercontent.com/a6677b08c955af8400f44c6298f40e7d19cc5b2d/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f677261795f3664366436642e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_gray_6d6d6d.png"></a>
    <div>
        <div class="ui secondary menu">
            <a class="item">
                <h2 class="header">
                    forkWibo
                    <div class="ui right red label">Beta</div>
                </h2>
            </a>

        </div>

        <div class="ui center aligned grid">
            <div class="sixteen wide column">
                <div class="ui basic segment">
                    <h1 id="heading">再也不用担心别人删微博了;)</h1>
                    <a class="ui blue button start-use-button" href=<?php echo $authorizeUrl; ?>>
                        开始使用
                    </a>
                </div>
            </div>

        </div>

    </div>

    <br/>
    <br/>

    <div class="ui center aligned grid">
        <div class="five wide column">
            <div class="ui basic segment">
                <h4 id="intro">查看我的微博</h4>
            </div>
        </div>
        <div class="six wide column">
            <div class="ui basic segment">
                <h4 id="intro">定时获取关注用户的微博</h4>
            </div>
        </div>
        <div class="five wide column">
            <div class="ui basic segment">
                <h4 id="intro">保存@forkWibo的微博(TODO)</h4>
            </div>
        </div>
    </div>

</body>
</html>
