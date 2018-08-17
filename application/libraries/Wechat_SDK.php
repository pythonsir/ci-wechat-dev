<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 封装微信开发类库
 *
 */
class Wechat_SDK
{

    protected $CI;

    public function __construct(){

        $CI = & get_instance();

        $CI->config->load('wechat_sdk');

        $this->CI = $CI;
    }


    /**
     *
     * 构造获取code的url连接
     * @param string $redirectUrl 微信服务器回跳的url，需要url编码
     *
     * @return 返回构造好的url
     */
    public function _CreateOauthUrlForCode($url)
    {
        $config = $this->CI->config->item('wx');
        $urlObj["appid"] = $config['appID'];
        $urlObj["redirect_uri"] = urlencode($url);
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "snsapi_userinfo";
        $urlObj["state"] = "STATE"."#wechat_redirect";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
    }


    /**
     *
     * 查询订单，WxPayOrderQuery中out_trade_no、transaction_id至少填一个
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayConfigInterface $config  配置对象
     * @param WxPayOrderQuery $inputObj
     * @param int $timeOut
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public  function orderQuery($config, $inputObj, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/orderquery";
        //检测必填参数
        if(!$inputObj->IsOut_trade_noSet() && !$inputObj->IsTransaction_idSet()) {
            show_error("订单查询接口中，out_trade_no、transaction_id至少填一个！");
        }
        $inputObj->SetAppid($config->GetAppId());//公众账号ID
        $inputObj->SetMch_id($config->GetMerchantId());//商户号
        $inputObj->SetNonce_str(self::getNonceStr());//随机字符串

        $inputObj->SetSign($config);//签名
        $xml = $inputObj->ToXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($config, $xml, $url, false, $timeOut);
        $this->CI->load->library("WxPayResults");
        $result =$this->CI->wxpayresults->Init($config, $response);
        self::reportCostTime($config, $url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }


    /**
     *
     * 拼接签名字符串
     * @param array $urlObj
     *
     * @return 返回已经拼接好的字符串
     */
    public function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v)
        {
            if($k != "sign"){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }


    /**
     *
     * 构造获取open和access_toke的url地址
     * @param string $code，微信跳转带回的code
     *
     * @return 请求的url
     */
    public function __CreateOauthUrlForOpenid($code)
    {
        $config = $this->CI->config->item('wx');
        $urlObj["appid"] = $config['appID'];
        $urlObj["secret"] = $config['appSecret'];
        $urlObj["code"] = $code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
    }

    /**
     * 通过code换取网页授权access_token
     * @return mixed
     */
    public function getOpenidAndAccessToken($code){


        $url = $this->__CreateOauthUrlForOpenid($code);

        //初始化curl
        $ch = curl_init();

        $curlVersion = curl_version();

        $config = $this->CI->config->item('wx');

        //设置超时
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        //运行curl，结果以jason形式返回
        $res = curl_exec($ch);
        curl_close($ch);

        //取出openid
        $data = json_decode($res,true);

        if(isset($data['errcode'])){

            $url = 'http://'.$_SERVER['SERVER_NAME'];

            Header("Location: $url");

            exit();

        }

        if(isset($data['openid'])){

            log_message('info', 'openid: '.$data['openid']);

            return $data;
        }

    }


    /**
     * 拉取用户信息(需scope为 snsapi_userinfo)
     * @param $data
     * @return mixed
     */
    public function getUserinfo($data){

        $urlObj["access_token"] = $data['access_token'];
        $urlObj["openid"] = $data['openid'];
        $urlObj["lang"] = 'zh_CN';
        $bizString = $this->ToUrlParams($urlObj);
        $url = "https://api.weixin.qq.com/sns/userinfo?".$bizString;

        //初始化curl
        $ch = curl_init();

        $curlVersion = curl_version();

        //设置超时
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        //运行curl，结果以jason形式返回
        $res = curl_exec($ch);
        curl_close($ch);

        $userinfo = json_decode($res,true);

        $this->CI->session->set_userdata(array('nickname'=>$userinfo['nickname'],'headimgurl'=>$userinfo['headimgurl'],'openid'=>$userinfo['openid']));

        log_message('info', '用户信息: '.$userinfo['openid'].'|'.$userinfo['nickname'].'|'
            .'|'.$userinfo['headimgurl']);

        return $userinfo;


    }


    /**
     *
     * 统一下单，WxPayUnifiedOrder中out_trade_no、body、total_fee、trade_type必填
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayConfigInterface $config  配置对象
     * @param WxPayUnifiedOrder $inputObj
     * @param int $timeOut
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public function unifiedOrder($config, $inputObj, $timeOut = 6)
    {



        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        //检测必填参数
        if(!$inputObj->IsOut_trade_noSet()) {

            show_error("缺少统一支付接口必填参数out_trade_no！");

        }else if(!$inputObj->IsBodySet()){
            show_error("缺少统一支付接口必填参数body！");
        }else if(!$inputObj->IsTotal_feeSet()) {
            show_error("缺少统一支付接口必填参数total_fee！");
        }else if(!$inputObj->IsTrade_typeSet()) {
            show_error("缺少统一支付接口必填参数trade_type！");
        }

        //关联参数
        if($inputObj->GetTrade_type() == "JSAPI" && !$inputObj->IsOpenidSet()){


            show_error("统一支付接口中，缺少必填参数openid！trade_type为JSAPI时，openid为必填参数！");
        }
        if($inputObj->GetTrade_type() == "NATIVE" && !$inputObj->IsProduct_idSet()){
            show_error("统一支付接口中，缺少必填参数product_id！trade_type为JSAPI时，product_id为必填参数！");
        }

        //异步通知url未设置，则使用配置文件中的url
        if(!$inputObj->IsNotify_urlSet() && $config->GetNotifyUrl() != ""){
            $inputObj->SetNotify_url($config->GetNotifyUrl());//异步通知url
        }

        $inputObj->SetAppid($config->GetAppId());//公众账号ID
        $inputObj->SetMch_id($config->GetMerchantId());//商户号
        $inputObj->SetSpbill_create_ip($_SERVER['REMOTE_ADDR']);//终端ip
        $inputObj->SetNonce_str(self::getNonceStr());//随机字符串

        //签名
        $inputObj->SetSign($config);
        $xml = $inputObj->ToXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($config, $xml, $url, false, $timeOut);
        $this->CI->load->library("WxPayResults");
        $result = $this->CI->wxpayresults->Init($config, $response);
        self::reportCostTime($config, $url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     *
     * 上报数据， 上报的时候将屏蔽所有异常流程
     * @param WxPayConfigInterface $config  配置对象
     * @param string $usrl
     * @param int $startTimeStamp
     * @param array $data
     */
    private  function reportCostTime($config, $url, $startTimeStamp, $data)
    {
        //如果不需要上报数据
        $reportLevenl = $config->GetReportLevenl();
        if($reportLevenl == 0){
            return;
        }
        //如果仅失败上报
        if($reportLevenl == 1 &&
            array_key_exists("return_code", $data) &&
            $data["return_code"] == "SUCCESS" &&
            array_key_exists("result_code", $data) &&
            $data["result_code"] == "SUCCESS")
        {
            return;
        }

        //上报逻辑
        $endTimeStamp = self::getMillisecond();

        $this->CI->load->library("WxPayReport");

        $objInput = $this->CI->wxpayreport;
        $objInput->SetInterface_url($url);
        $objInput->SetExecute_time_($endTimeStamp - $startTimeStamp);
        //返回状态码
        if(array_key_exists("return_code", $data)){
            $objInput->SetReturn_code($data["return_code"]);
        }
        //返回信息
        if(array_key_exists("return_msg", $data)){
            $objInput->SetReturn_msg($data["return_msg"]);
        }
        //业务结果
        if(array_key_exists("result_code", $data)){
            $objInput->SetResult_code($data["result_code"]);
        }
        //错误代码
        if(array_key_exists("err_code", $data)){
            $objInput->SetErr_code($data["err_code"]);
        }
        //错误代码描述
        if(array_key_exists("err_code_des", $data)){
            $objInput->SetErr_code_des($data["err_code_des"]);
        }
        //商户订单号
        if(array_key_exists("out_trade_no", $data)){
            $objInput->SetOut_trade_no($data["out_trade_no"]);
        }
        //设备号
        if(array_key_exists("device_info", $data)){
            $objInput->SetDevice_info($data["device_info"]);
        }

        try{
            self::report($config, $objInput);
        } catch (Exception $e){
            //不做任何处理
        }
    }


    /**
     *
     * 测速上报，该方法内部封装在report中，使用时请注意异常流程
     * WxPayReport中interface_url、return_code、result_code、user_ip、execute_time_必填
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayConfigInterface $config  配置对象
     * @param WxPayReport $inputObj
     * @param int $timeOut
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public  function report($config, $inputObj, $timeOut = 1)
    {
        $url = "https://api.mch.weixin.qq.com/payitil/report";
        //检测必填参数
        if(!$inputObj->IsInterface_urlSet()) {
            show_error("接口URL，缺少必填参数interface_url！");
        } if(!$inputObj->IsReturn_codeSet()) {
        show_error("返回状态码，缺少必填参数return_code！");
    } if(!$inputObj->IsResult_codeSet()) {
        show_error("业务结果，缺少必填参数result_code！");
    } if(!$inputObj->IsUser_ipSet()) {
        show_error("访问接口IP，缺少必填参数user_ip！");
    } if(!$inputObj->IsExecute_time_Set()) {
        show_error("接口耗时，缺少必填参数execute_time_！");
    }
        $inputObj->SetAppid($config->GetAppId());//公众账号ID
        $inputObj->SetMch_id($config->GetMerchantId());//商户号
        $inputObj->SetUser_ip($_SERVER['REMOTE_ADDR']);//终端ip
        $inputObj->SetTime(date("YmdHis"));//商户上报时间
        $inputObj->SetNonce_str(self::getNonceStr());//随机字符串

        $inputObj->SetSign($config);//签名
        $xml = $inputObj->ToXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($config, $xml, $url, false, $timeOut);
        return $response;
    }

    /**
     *
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return 产生的随机字符串
     */
    public static function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    /**
     * 获取毫秒级别的时间戳
     */
    private static function getMillisecond()
    {
        //获取毫秒的时间戳
        $time = explode ( " ", microtime () );
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode( ".", $time );
        $time = $time2[0];
        return $time;
    }

    /**
     * 以post方式提交xml到对应的接口url
     *
     * @param WxPayConfigInterface $config  配置对象
     * @param string $xml  需要post的xml数据
     * @param string $url  url
     * @param bool $useCert 是否需要证书，默认不需要
     * @param int $second   url执行超时时间，默认30s
     */
    private static function postXmlCurl($config, $xml, $url, $useCert = false, $second = 30)
    {
        $ch = curl_init();
        $curlVersion = curl_version();
        $ua = "WXPaySDK/3.0.9 (".PHP_OS.") PHP/".PHP_VERSION." CURL/".$curlVersion['version']." "
            .$config->GetMerchantId();

        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        $proxyHost = "0.0.0.0";
        $proxyPort = 0;
        $config->GetProxy($proxyHost, $proxyPort);
        //如果有配置代理这里就设置代理
        if($proxyHost != "0.0.0.0" && $proxyPort != 0){
            curl_setopt($ch,CURLOPT_PROXY, $proxyHost);
            curl_setopt($ch,CURLOPT_PROXYPORT, $proxyPort);
        }
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
        curl_setopt($ch,CURLOPT_USERAGENT, $ua);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if($useCert == true){
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            //证书文件请放入服务器的非web目录下
            $sslCertPath = "";
            $sslKeyPath = "";
            $config->GetSSLCertPath($sslCertPath, $sslKeyPath);
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, $sslCertPath);
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, $sslKeyPath);
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            show_error("curl出错，错误码:$error");
        }
    }


    /**
     *
     * 获取jsapi支付的参数
     * @param array $UnifiedOrderResult 统一支付接口返回的数据
     *
     * @return json数据，可直接填入js函数作为参数
     */
    public function GetJsApiParameters($UnifiedOrderResult)
    {
        if(!array_key_exists("appid", $UnifiedOrderResult)
            || !array_key_exists("prepay_id", $UnifiedOrderResult)
            || $UnifiedOrderResult['prepay_id'] == "")
        {
            show_error("参数错误");
        }

        $this->CI->load->library('WxPayJsApiPay');

        $jsapi = $this->CI->wxpayjsapipay;
        $jsapi->SetAppid($UnifiedOrderResult["appid"]);
        $timeStamp = time();
        $jsapi->SetTimeStamp("$timeStamp");
        $jsapi->SetNonceStr(self::getNonceStr());
        $jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);

        $this->CI->load->library('WxPayConfig');

        $config = $this->CI->wxpayconfig;

        $jsapi->SetPaySign($jsapi->MakeSign($config));

        $parameters = json_encode($jsapi->GetValues());

        return $parameters;
    }


    /**
     *
     * 支付结果通用通知
     * @param function $callback
     * 直接回调函数使用方法: notify(you_function);
     * 回调类成员函数方法:notify(array($this, you_function));
     * $callback  原型为：function function_name($data){}
     */
    public  function notify($config, $callback, &$msg)
    {
        if (!isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
            # 如果没有数据，直接返回失败
            return false;
        }

        //如果返回成功则验证签名
        try {
            //获取通知的数据
            $xml = $GLOBALS['HTTP_RAW_POST_DATA'];

            $this->CI->load->library("WxPayNotifyResults");

            $result =$this->CI->wxpaynotifyresults->Init($config, $xml);
        } catch (Exception $e){
            $msg = $e->getMessage();
            return false;
        }

        return call_user_func($callback, $result);
    }


    /**
     * 直接输出xml
     * @param string $xml
     */
    public  function replyNotify($xml)
    {
        echo $xml;
    }


}