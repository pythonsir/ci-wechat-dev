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
    <base href="<?=$basepath;?>">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <title>慧远故里育英助学-捐款</title>
    <link rel="stylesheet" href="/static/css/weui.css">
    <link rel="stylesheet" href="/static/css/swiper.min.css">
    <link rel="stylesheet" href="/static/css/site.css?v=201808141019">
    <script src="/static/js/index.min.js"></script>

</head>
<body>
<div class="banner-cont">
    <div class="banner-hd">
        <h3 class="banner-hd-title">
            慧远风范、大爱无疆
        </h3>
    </div>

    <div class="swiper-container">
        <div class="swiper-wrapper">
            <div class="swiper-slide"><img style="width:100%" src="/static/images/banner1.jpg?v=201808141141"></div>
            <div class="swiper-slide"><img style="width:100%" src="/static/images/banner2.jpg?v=201808141141"></div>
        </div>
        <!-- 如果需要分页器 -->
        <div class="swiper-pagination"></div>

    </div>

</div>

<div class="page__bd">

    <div class="weui-cells__title">以下几项为必填写</div>
    <div class="weui-cells weui-cells_form" id="form">
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">姓名</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" name="username" type="text" required  emptytips="请输入姓名" placeholder="请输入姓名"/>
            </div>
            <div class="weui-cell__ft"> <i class="weui-icon-warn"></i> </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd">
                <label class="weui-label">手机号</label>
            </div>
            <div class="weui-cell__bd">
                <input class="weui-input" name="phone" type="tel"  required  pattern="^\d{11}$"
                       maxlength="11"  placeholder="请输入手机号" emptytips="请输入手机号" notmatchtips="请输入正确的手机号" />
            </div>
            <div class="weui-cell__ft"> <i class="weui-icon-warn"></i> </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd">
                <label class="weui-label">金额(￥)</label>
            </div>
            <div class="weui-cell__bd">
                <input class="weui-input" name="money" type="number" required  emptytips="捐款金额大于1" placeholder="输入捐款金额"   />
            </div>
            <div class="weui-cell__ft"> <i class="weui-icon-warn"></i> </div>
        </div>
    </div>

    <div class="footBar-m footBar-dft" style="margin-top: 10px;">
        <a  href="javascript:" id="formSubmitBtn" class="footBar-m footBar-dft button m-donate-it" >我要捐款</a>
    </div>

    <div class="weui-footer" style="margin-top: 20px;">
        <p class="weui-footer__text">Copyright © 2018-2022 山西耐特斯达科技有限公司</p>
    </div>

</div>


<!-- tabbar-->
<div class="mod-tabbar" id="tabbar">

    <a href="/" class="mod-tabbar-item">
        <svg class="mod-tabbar-icon" viewBox="0 0 48 48" fill-rule="evenodd">
            <path fill="#DF2413"
                  d="M30,44v-6c0-3.3-2.7-6-6-6c-3.3,0-6,2.7-6,6v6H8V25l-5,0L24,4l21,21l-5,0v19H30z"></path>
        </svg>
        <div class="mod-tabbar-txt clr_dft">首页</div>
    </a>

    <a href="/index.php/me/index" class="mod-tabbar-item j-mta" name="uc">
        <svg class="mod-tabbar-icon" viewBox="0 0 48 48" id="icon-tabbar-user" fill-rule="evenodd">
            <path
                d="M38,44 L38,36 C38,33.790861 36.209139,32 34,32 L14,32 C11.790861,32 10,33.790861 10,36 L10,44 L6,44 L6,36 C6,31.581722 9.581722,28 14,28 L34,28 C38.418278,28 42,31.581722 42,36 L42,44 L38,44 Z M24,24 C17.9248678,24 13,19.0751322 13,13 C13,6.92486775 17.9248678,2 24,2 C30.0751322,2 35,6.92486775 35,13 C35,19.0751322 30.0751322,24 24,24 Z M24,20 C27.8659932,20 31,16.8659932 31,13 C31,9.13400675 27.8659932,6 24,6 C20.1340068,6 17,9.13400675 17,13 C17,16.8659932 20.1340068,20 24,20 Z"></path>
        </svg>
        <div class="mod-tabbar-txt">我的</div>
    </a>

</div>
</body>
<script src="/static/js/zepto.min.js"></script>
<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="/static/js/weui.js"></script>
<script src="/static/js/swiper.js"></script>
<script src="/static/js/site.js?v=201808171052001"></script>
</html>
