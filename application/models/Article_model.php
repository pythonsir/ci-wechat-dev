<?php

/**
 * Created by PhpStorm.
 * User: python
 * Date: 2018/8/29
 * Time: 下午5:34
 */
class Article_model extends CI_Model
{
    public $id;
    public $nickname;
    public $createdAt;
    public $openid;
    public $headimgurl;
    public $title;
    public $author;
    public $ispub;
    public $article;

    const ATTICLE_IS_PUB_0 = 0;

    const ATTICLE_IS_PUB_1 = 1;

    public function save(){

        try{

            if($this->session->has_userdata("openid")){

                $this->id = time();
                $this->createdAt = date("Y-m-d H:i:s",time());
                $this->nickname = $this->session->nickname;
                $this->openid = $this->session->openid;
                $this->headimgurl=$this->session->headimgurl;
                $this->title = $this->input->post("title");
                $this->author = $this->input->post("author");
                $this->article =  $this->input->post("article");
                $this->ispub =  self::ATTICLE_IS_PUB_0;
                $this->db->insert('article', $this);
            }else{
                $this->load->library('Wechat_SDK');
                $url = $this->wechat_sdk->_CreateOauthUrlForCode('http://'.$_SERVER['SERVER_NAME'].'/index.php/article/getOpenid/');
                Header("Location: $url");
                exit();
            }

            return true;


        }catch (Exception $e){

            log_message("info",$e->getMessage());

            return false;

        }

    }

}