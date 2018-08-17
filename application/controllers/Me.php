<?php

defined('BASEPATH') OR exit('No direct script access allowed');


/**
 * 我的
 * User: python
 * Date: 2018/8/15
 * Time: 下午11:24
 */
class Me  extends CI_Controller
{

    public function index(){

        $basepath = 'http://'.$_SERVER['SERVER_NAME'].'/index.php/';

        if($this->session->has_userdata("openid")){

            $nickname = $this->session->nickname;

            $headimgurl=$this->session->headimgurl;

        }else{

            $this->load->library('Wechat_SDK');
            $url = $this->wechat_sdk->_CreateOauthUrlForCode('http://'.$_SERVER['SERVER_NAME'].'/index.php/me/getOpenid/');
            Header("Location: $url");
            exit();

        }

        $this->load->view('me',array("basepath"=>$basepath,"nickname"=>$nickname,"headimgurl"=>$headimgurl));

    }

    public function getOpenid(){


        $this->load->library('Wechat_SDK');

        $code = $this->input->get('code');

        $data = $this->wechat_sdk->getOpenidAndAccessToken($code);

        $data = $this->wechat_sdk->getUserinfo($data);

        $basepath = 'http://'.$_SERVER['SERVER_NAME'].'/index.php/';

        $this->load->view('me',array('basepath'=>$basepath,"nickname"=>$data['nickname'],"headimgurl"=>$data['headimgurl']));


    }

    public function getdata(){

        $result = array(
            "status"=>"ok",
            "res"=>[],
            "message"=>""
        );

        $this->load->model('Order_model');

        $res = $this->Order_model->getlist();

        if(count($res) > 0){

            $result['res'] = $res;

            $result['message']="获取记录成功!";

        }else{

            $result['status'] = "no";

            $result['message']="暂无记录!";

        }

        $this->output->set_output(json_encode($result));


    }

}