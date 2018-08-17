<?php
/**
 * Created by PhpStorm.
 * User: python
 * Date: 2018/8/13
 * Time: 下午11:55
 */ ?>
<!DOCTYPE html>
<html>
<head>
    <base href="<?= $basepath; ?>">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <title>慧远故里育英助学-个人中心</title>
    <link rel="stylesheet" href="/static/css/weui.css">
    <link rel="stylesheet" href="/static/css/swiper.min.css">
    <link rel="stylesheet" href="/static/css/me.css?v=201808141019">
    <script src="/static/js/index.min.js"></script>


</head>
<body >

<div id="pageContainer">

    <div class="page page-user">

        <div id="userInfo">

            <header
                style="background-image: url(<?= $headimgurl?>)"></header>

            <div class="lay-box lay-box-head">

                <div class="imgwarper">
                    <img class="avatar-pic"
                         src="<?= $headimgurl?>">
                </div>

                <span class="nickname">
                    <?= $nickname?>
                </span>

            </div>

        </div>

        <div class="lay-box" id="userAchievements">
            <div class="g-flex">
                <div class="g-flex-m ">
                    <h3>捐款记录</h3>
                </div>

            </div>
            <div class="list">
                <div class="list-header">
                    <div>姓名</div>
                    <div>手机号</div>
                    <div>金额</div>
                    <div>日期</div>
                </div>

                <div class="list-body" id="list">
                    <div v-if="list.length >  0" class="row" v-for="(item,index) in list" :key="index">
                        <div v-cloak>{{ item.username}}</div>
                        <div v-cloak>{{ item.phone }}</div>
                        <div v-cloak>{{ item.money}}元</div>
                        <div v-cloak>{{ item.createdAt }}</div>
                    </div>
                    <div  v-if="list.length ==  0">
                        无记录
                    </div>


                </div>

            </div>

        </div>


    </div>


</div>


<!-- tabbar-->
<div class="mod-tabbar" id="tabbar">

    <a href="/" class="mod-tabbar-item j-mta">
        <svg class="mod-tabbar-icon" viewBox="0 0 48 48" fill-rule="evenodd">
            <path
                d="M30,44v-6c0-3.3-2.7-6-6-6c-3.3,0-6,2.7-6,6v6H8V25l-5,0L24,4l21,21l-5,0v19H30z"></path>
        </svg>
        <div class="mod-tabbar-txt ">首页</div>
    </a>

    <a href="/index.php/me/index" class="mod-tabbar-item j-mta" name="uc">
        <svg class="mod-tabbar-icon" viewBox="0 0 48 48" id="icon-tabbar-user" fill-rule="evenodd">
            <path fill="#DF2413"
                  d="M38,44 L38,36 C38,33.790861 36.209139,32 34,32 L14,32 C11.790861,32 10,33.790861 10,36 L10,44 L6,44 L6,36 C6,31.581722 9.581722,28 14,28 L34,28 C38.418278,28 42,31.581722 42,36 L42,44 L38,44 Z M24,24 C17.9248678,24 13,19.0751322 13,13 C13,6.92486775 17.9248678,2 24,2 C30.0751322,2 35,6.92486775 35,13 C35,19.0751322 30.0751322,24 24,24 Z M24,20 C27.8659932,20 31,16.8659932 31,13 C31,9.13400675 27.8659932,6 24,6 C20.1340068,6 17,9.13400675 17,13 C17,16.8659932 20.1340068,20 24,20 Z"></path>
        </svg>
        <div class="mod-tabbar-txt clr_dft">我的</div>
    </a>

</div>

</body>
<script type="text/javascript" src="/static/js/vue.js"></script>
<script src="/static/js/zepto.min.js"></script>
<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="/static/js/weui.js"></script>
<script src="/static/js/swiper.js"></script>
<script src="/static/js/me.js?v=201808161003"></script>

</html>
