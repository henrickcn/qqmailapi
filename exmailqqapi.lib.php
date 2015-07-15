<?php
/**
 * 腾讯企业邮件OpenAPI接口公用类
 * User: Henrick
 * Date: 15-7-15
 * Time: 下午3:43
 */
class ExMailQQ
{
    private $key            = "";                                    //接口key值
    private $clientId       = "";                                    //管理员帐号
    private $grantType      = "client_credentials";
    private $accessTokenUrl = "https://exmail.qq.com/cgi-bin/token"; //获取access_token地址
    private $qqMailHost     = "http://openapi.exmail.qq.com:12211/"; //接口地址
    private $authKeyAPI     = "openapi/mail/authkey";                //获取authKey的接口地址
    private $loginExmail    = "https://exmail.qq.com/cgi-bin/login"; //一键登录
    private $getUserInfo    = "openapi/user/get";                    //获取用户信息

    /**
     * 初始化配置
     * @param string $key
     * @param string $clientId
     */
    public function __construct($key=null,$clientId=null){
        if($key){
            $this->key = $key;
        }
        if($clientId){
            $this->clientId = $clientId;
        }
    }

    /**
     * 获取access_token值方法
     */
    public function getAccessToken(){
        $data = array(
            'client_id'     => $this->clientId,
            'client_secret' => $this->key,
            'grant_type'    => $this->grantType
        );
        $resultStr = self::http($this->accessTokenUrl,'post',$data);
        $result = json_decode($resultStr,true);
        if(!isset($result['access_token'])){
            $result = array(
                'err' => 1,
                'msg' => '接口对接失败，原因为：'.$resultStr
            );
        }
        return $result;
    }

    /**
     * 获取用户的单点登录的AuthKey
     * @param $type
     * @param $accessToken
     * @param $mail
     */
    public function getAuthKey($type,$accessToken,$mail){
        $data = array(
            'alias'         => $mail,
        );
        $headerData = array(
            'Authorization' => $type." ".$accessToken
        );
        $result = self::http($this->qqMailHost.$this->authKeyAPI,'post',$data,$headerData);
        $result = json_decode($result,true);
        if(isset($result['errcode'])){
            return $result;
        }
        $login = $this->loginExmail.'?fun=bizopenssologin&method=bizauth&agent='.$this->clientId.'&user='.$mail.'&ticket='.$result['auth_key'];
        $result['url'] = $login;
        return $result;
    }

    public function getUserInfo(){
        $url = $this->qqMailHost.$this->getUserInfo;
    }


    /**
     * CURL请求方法
     * @param $url
     * @param $method
     * @param null $postfields
     * @param array $header_array
     * @param null $userpwd
     * @return mixed
     */
    public static function http ($url, $method="GET", $postfields = NULL, $header_array = array(), $userpwd = NULL)
    {
        $ci = curl_init();

        /* Curl 设置 */
        curl_setopt($ci, CURLOPT_USERAGENT, 'Mozilla/4.0');
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ci, CURLOPT_TIMEOUT, 30);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ci, CURLOPT_HEADER, FALSE);

        if ($userpwd) {
            curl_setopt($ci, CURLOPT_USERPWD, $userpwd);
        }

        $method = strtoupper($method);
        switch ($method) {
            case 'GET':
                if (! empty($postfields)) {
                    $url = $url . '?' . http_build_query($postfields);
                }
                break;
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, TRUE);
                if (! empty($postfields)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
                }
                break;
        }

        $header_array2 = array();

        foreach ($header_array as $k => $v) {
            array_push($header_array2, $k . ': ' . $v);
        }
        curl_setopt($ci, CURLOPT_HTTPHEADER, $header_array2);
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE);
        curl_setopt($ci, CURLOPT_URL, $url);
        $response = curl_exec($ci);
        curl_close($ci);
        return $response;
    }
}