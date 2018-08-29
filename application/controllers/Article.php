<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 文章管理
 * User: python
 * Date: 2018/8/29
 * Time: 下午12:09
 */
class Article  extends  CI_Controller
{
    public function test(){

        $basepath = 'http://'.$_SERVER['SERVER_NAME'].'/index.php/';


        $this->load->view('article',array("basepath"=>$basepath,"nickname"=>"Python","headimgurl"=>"http://thirdwx.qlogo.cn/mmopen/vi_32/DYAIOgq83epjyic1oiaaia2eX6U2CZ1icKWVkI2np3L8hHibP5ce7SJDrOtmkHJC5fFhSPaiahaE1YNVqX8vaDNZUeUw/132"));


    }
    public function index(){


        $basepath = 'http://'.$_SERVER['SERVER_NAME'].'/index.php/';

        if($this->session->has_userdata("openid")){

            $nickname = $this->session->nickname;

            $headimgurl=$this->session->headimgurl;

        }else{

            $this->load->library('Wechat_SDK');
            $url = $this->wechat_sdk->_CreateOauthUrlForCode('http://'.$_SERVER['SERVER_NAME'].'/index.php/article/getOpenid/');
            Header("Location: $url");
            exit();

        }

        $this->load->view('article',array("basepath"=>$basepath,"nickname"=>$nickname,"headimgurl"=>$headimgurl));


    }

    public function getOpenid(){

        $this->load->library('Wechat_SDK');

        $code = $this->input->get('code');

        $data = $this->wechat_sdk->getOpenidAndAccessToken($code);

        $data = $this->wechat_sdk->getUserinfo($data);

        $basepath = 'http://'.$_SERVER['SERVER_NAME'].'/index.php/';

        $this->load->view('article',array('basepath'=>$basepath,"nickname"=>$data['nickname'],"headimgurl"=>$data['headimgurl']));

    }

    public function save(){

        $this->load->model('Article_model');

        $result = array(
            "status"=>"no",
            "param" =>array(),
            "message"=>""
        );

        $this->load->library('form_validation');


        $data=$this->input->post();

        $this->form_validation->set_data($data);

        $this->form_validation->set_rules('title', 'title', 'required');

        $this->form_validation->set_rules('author', 'author', 'required');

        $this->form_validation->set_rules('article', 'article', 'required');

        if($this->form_validation->run() && $this->Article_model->save()){

            $result['status'] = "ok";

            $result['message'] = "投稿成功!";

        }else{

            $result['status'] = "no";

            $result['message'] = "投稿失败";

        }

        $this->output->set_output(json_encode($result));


    }
}