<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {



	public function index()
	{


        if(!$this->session->has_userdata("openid")){

            $this->load->library('Wechat_SDK');
            $url = $this->wechat_sdk->_CreateOauthUrlForCode('http://'.$_SERVER['SERVER_NAME'].'/index.php/welcome/getOpenid/');
            Header("Location: $url");
            exit();

        }else{

            $basepath = 'http://'.$_SERVER['SERVER_NAME'].'/index.php/';

            $this->load->view('jk',array('basepath'=>$basepath));

        }

	}



    public function makeOrder(){

        $this->load->model('Order_model');

        $result = array(
            "status"=>"no",
            "param" =>array(),
            "message"=>""
        );

        $this->load->library('form_validation');

        $username = $this->input->post('username');

        $phone = $this->input->post('phone');

        $money = $this->input->post('money');

        $data=array(

            'username'=>$username,
            'phone' => $phone,
            'money'=>$money
        );

        $this->form_validation->set_data($data);

        $this->form_validation->set_rules('username', 'Username', 'required');

        $this->form_validation->set_rules('phone', 'Phone', 'required|regex_match[/^1[3|4|5|7|8][0-9]{9}$/]');

        $this->form_validation->set_rules('money', 'Money', 'required|greater_than_equal_to[0.01]');

        if($this->form_validation->run()){

           $rs = $this->Order_model->makeOrder();

            $result['status'] = "ok";

            $result['param'] = $rs;

            $result['message'] = "下单成功";

        }else{

            $result['status'] = "no";

            $result['message'] = "下单失败";

        }

        $this->output->set_output(json_encode($result));


    }

    /**
     * 获取用户openid
     * @return mixed
     */
	public function getOpenid(){

        $this->load->library('Wechat_SDK');

        $code = $this->input->get('code');

        $data = $this->wechat_sdk->getOpenidAndAccessToken($code);

        $data = $this->wechat_sdk->getUserinfo($data);

        $basepath = 'http://'.$_SERVER['SERVER_NAME'].'/index.php/';

        $this->load->view('jk',array('basepath'=>$basepath));

    }


    /**
     *  付款通知
     */
    public function notice(){

        $this->load->library("WxPayConfig");

        $this->load->library("PayNotifyCallBack");

        $this->paynotifycallback->Handle($this->wxpayconfig,false);

    }





}
