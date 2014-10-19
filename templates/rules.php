<?php

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="http://cdn.bootcss.com/semantic-ui/0.16.1/css/semantic.min.css" rel="stylesheet">
    <style>
        .link {
            text-decoration: none;
        }
    </style>
    <title>forkWibo</title>
</head>
<body>

<div class="ui secondary menu">
    <a class="item" href=<?php echo HOMEPAGE.'/home'; ?>>
        <h2>forkWibo</h2>
    </a>
</div>

<div class="ui secondary menu">
    <a class="item">
        <h3>一些规则</h3>
    </a>
</div>

<div class="ui piled segment">
    <h4>定时微博</h4>
    <p>
        至多添加5个关注用户
    </p>
    <p>
        定时设置在下一个时间点生效（即：若设置1小时，forkWibo会在一小时之后爬取）
    </p>
    <p>
        频率默认为每1天爬取一次
    </p>
    <p>
        定时微博每2天会自动清空一次
    </p>
</div>

<br/>

<div class="ui piled segment" id="api">
    <h4>微博API限制</h4>
    <p>
        至多获取用户关注列表的30%(其实是没钱给)
    </p>
    <p>
        <small>若添加时提示没有关注该用户，尝试：先取消关注，后再关注(保证该用户位于你关注列表的前30%)</small>
    </p>
</div>

<br/>

<div class="ui piled segment">
    <h4>关于保存@forkWibo的微博</h4>
    <p>
        没钱给新浪，估计是实现不了。。
    </p>
</div>

<br/>

<div class="ui horizontal icon divider">
    <i class="circular heart icon"></i>
</div>

<div class="ui center aligned basic segment">
    <small><a class="link" href=<?php echo HOMEPAGE.'/humans.txt'; ?>>About</a> | &copy; jsxqf 2014 | <a class="link" href=<?php echo HOMEPAGE.'/rules'; ?>>Rules</a></small>
</div>


</body>
</html>