<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-7-15
 * Time: 下午4:29
 */
session_start();
require "exmailqqapi.lib.php";

class Api
{
    public $exmailObj;

    public function __construct($config=array('Key'=>'','ClientId'=>'')){

        if(strtolower(gettype($this->exmailObj))!='object'){
            $data = isset($_SESSION['config']['Key']) && !empty($_SESSION['config']['Key']) ? $_SESSION['config']:$config;
            $this->exmailObj = new ExMailQQ($data['Key'],$data['ClientId']);
        }
        $this->_getAccessToken();
    }

    //检测是否能获取Token
    public function getAccessToken(){
        $this->_getAccessToken();
        echo json_encode(array('err'=>0));
    }

    //单点登录
    public function goExmail(){
        $result = $this->exmailObj->getAuthKey($_SESSION['api']['token_type'],$_SESSION['api']['access_token'],$_POST['Mail']);
        if(isset($result['errcode'])){
            echo json_encode(array('err'=>1,'msg'=>$result['msg']));
            exit;
        }
        $info = array('err'=>0,'url'=>$result['url']);
        echo json_encode($info);
    }

    //获取用户信息
    public function getUserInfo(){
        $result = $this->exmailObj->getUserInfo($_SESSION['api']['token_type'],$_SESSION['api']['access_token'],$_POST['Mail']);
        if(isset($result['errcode'])){
            echo json_encode(array('err'=>1,'msg'=>$result['msg']));
            exit;
        }
    }

    /**
     * 获取AccessToken值
     */
    protected function _getAccessToken(){
        if(!isset($_SESSION['api']['expires_in']) || $_SESSION['api']['expires_in']<=time()){
            $result = $this->exmailObj->getAccessToken();
            if(isset($result['err']) && $result['err']==1){
                echo json_encode($result);exit;
            }
            $result['expires_in'] = time()+$result['expires_in']-60;
            $_SESSION['api'] = $result;
        }
    }
}

if(is_array($_POST)){
    $config = '';
    if(isset($_POST['Key'])){
        $config = array(
            'Key'      => $_POST['Key'],
            'ClientId' => $_POST['ClientId']
        );
    }
    $api = new Api($config);
    if($_GET['opt']){
        $action = $_GET['opt'];
        if(!isset($_SESSION['config'])){
            $_SESSION['config'] = $config;
        }
        $api->$action();
    }
}