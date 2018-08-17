<?php

/**
 * Created by PhpStorm.
 * User: python
 * Date: 2018/8/14
 * Time: 下午4:35
 */
class Order_model  extends CI_Model
{
    public $id;
    public $username;
    public $phone;
    public $money;
    public $createdAt;
    public $nickname;
    public $openid;
    public $headimgurl;
    public $ispay;
    public $out_trade_no;

    const ORDER_STATUS_PAY_OK = 1;

    const ORDER_STATUS_PAY_NO = 0;


    public function makeOrder(){

        try{

            if($this->session->has_userdata("openid")){

                $this->id = time();
                $this->username = $this->input->post("username");
                $this->phone = $this->input->post("phone");
                $this->money = $this->input->post("money");
                $this->createdAt = time();
                $this->nickname = $this->session->nickname;
                $this->openid = $this->session->openid;
                $this->ispay= Order_model::ORDER_STATUS_PAY_NO;
                $this->headimgurl=$this->session->headimgurl;
                $this->out_trade_no = self::getOut_trade_no();
                $this->db->insert('order', $this);

                $this->load->library("WxPayUnifiedOrder");

                $wxPayUnifiedOrder = $this->wxpayunifiedorder;

                $wxPayUnifiedOrder->SetBody("慧远故里育英助学捐款");
                $wxPayUnifiedOrder->SetOut_trade_no($this->out_trade_no);
                $wxPayUnifiedOrder->SetTotal_fee($this->money * 100);
                $wxPayUnifiedOrder->SetTime_start(date("YmdHis"));
                $wxPayUnifiedOrder->SetTime_expire(date("YmdHis", time() + 600));
                $wxPayUnifiedOrder->SetTrade_type("JSAPI");
                $wxPayUnifiedOrder->SetOpenid($this->openid);

                $this->load->library("WxPayConfig");

                $wxPayUnifiedOrder->SetNotify_url($this->wxpayconfig->GetNotifyUrl());

                $this->load->library("Wechat_SDK");

                $order = $this->wechat_sdk->unifiedOrder($this->wxpayconfig,$wxPayUnifiedOrder);

                $jsApiParameters = $this->wechat_sdk->GetJsApiParameters($order);

                log_message("info",$jsApiParameters);

                return $jsApiParameters;


            }else{

                $this->load->library('Wechat_SDK');
                $url = $this->wechat_sdk->_CreateOauthUrlForCode();
                Header("Location: $url");
                exit();
            }



        }catch (Exception $e){



        }


    }
    private function getOut_trade_no(){

        @date_default_timezone_set("PRC");

        $order_date = date('Y-m-d');

        $order_id_main = date('YmdHis') . rand(10000,99999);

        $order_id_len = strlen($order_id_main);

        $order_id_sum = 0;

        for($i=0; $i<20; $i++){

            $order_id_sum += (int)(substr($order_id_main,$i,1));

        }

        $order_id = $order_id_main . str_pad((100 - $order_id_sum % 100) % 100,2,'0',STR_PAD_LEFT);

        return $order_id;

    }

    /**
     * 修改订单状态
     */
    public function changePayStatus($out_trade_no){


        $this->db->where('out_trade_no', $out_trade_no);

        $this->db->update('order', array("ispay"=>self::ORDER_STATUS_PAY_OK));

    }

    /**
     * 获取用户的捐款记录
     */
    public function getlist(){

        if($this->session->has_userdata("openid")){

            $this->db->select('username, phone, money,createdAt');

            $this->db->where(array('openid'=>$this->session->openid,'ispay'=>self::ORDER_STATUS_PAY_OK));

            $this->db->order_by('createdAt', 'DESC');

            $query = $this->db->get('order');

            $this->load->helper('date');

            $arr = array();

            foreach ($query->result_array() as $rs){

                $arr[] = array(

                    "username"=>$rs["username"],
                    "phone"=>$rs["phone"],
                    "money"=>$rs["money"],
                    "createdAt"=>date("Y-m-d H:i",$rs['createdAt']),
                );

            }

            return $arr;


        }else{

            $this->load->library('Wechat_SDK');
            $url = $this->wechat_sdk->_CreateOauthUrlForCode();
            Header("Location: $url");
            exit();


        }



    }


}