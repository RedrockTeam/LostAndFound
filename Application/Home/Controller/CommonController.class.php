<?php
/**
 * Created by PhpStorm.
 * User: firk1n
 * Date: 15/11/14
 * Time: 下午12:10
 */

namespace Home\Controller;

use Think\Controller\RestController;

class CommonController extends RestController{

    // 微信curl主地址
    private $wxUrl = 'http://hongyan.cqupt.edu.cn/MagicLoop/index.php?s=/addon/Api/Api/';


    public function _initialize(){

        if(!checkKey(I('key'))){
            $this->ajaxReturn(array(
                'msg' => '连接错误!',
                'status' => 403
                ));
        }
    }

    /**
     * 检查是否绑定学号
     * @param $openId
     * @return mixed 绑定的状态 1 or 0
     */
    public function _checkBind($openId) {
        return $this->_curl($openId, "bindVerify")['status'];
    }

    /**
     * 检查是否关注重邮小帮手
     * @param $openId
     * @return mixed 关注的话返回200
     */
    public function _checkCareXBS($openId) {
        $url = 'openidVerify';
        $info = $this->_curl($openId, $url);

        return $info['status'];
    }



    /**
     * 通过code获取openid
     * @param $code 参数code
     * @return mixed 返回openid的值
     */
    public function _getOpenId($code) {
        $info = $this->_curl(null, 'webOauth', $code);
        return $info['data']['openid'];
    }

    /**
     * 通过openid获取学号
     * @param $openid
     * @return int
     */
    public function _getStuNum($openid){
        $stuNum = $this->_curl($openid, "bindVerify")['stuId'];
        return $stuNum;
    }

    /**
     * 通过openid获取真实姓名
     * @param $openid
     * @return string
     */
    public function _getRealName($openid){

        $stuNum = $this->_curl($openid, "bindVerify")['realname'];
        return $stuNum;
    }

    /**
     * 获取用户的微信头像和微信昵称
     * @param $openId
     * @return array 返回用户昵称和头像信息
     */
    public function _getUserInfo($openId) {
        $res = $this->_curl($openId, "userInfo");
        $headImageUrl = substr($res['data']['headimgurl'], 0, -1) . "64";
        $nickname = $res['data']['nickname'];
        $result = array('nickName' => $nickname, 'headImageUrl' => $headImageUrl);
        return $result;
    }

    /**
     * 自用 curl通用函数
     * @param null $openId 微信端的openId
     * @param $uri 目标地址
     * @param null $code
     * @return mixed 返回的结果
     */
    public function _curl($openId=null, $uri, $code=null) {
        $timestamp = time();
        $string = "";
        $arr = "abcdefghijklmnopqistuvwxyz0123456789ABCDEFGHIGKLMNOPQISTUVWXYZ";
        for ($i=0; $i<16; $i++) {
            $y = rand(0,41);
            $string .= $arr[$y];
        }
        $secret = sha1(sha1($timestamp).md5($string).'redrock');
        if($code == null){
            $post_data = array (
                "timestamp" => $timestamp,
                "string" => $string,
                "secret" => $secret,
                "openid" => $openId,
                "token" => "gh_68f0a1ffc303"
            );
        }else if($openId == null){
            $post_data = array (
                "timestamp" => $timestamp,
                "string" => $string,
                "secret" => $secret,
                "token" => "gh_68f0a1ffc303",
                'code' => $code
            );
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->wxUrl.$uri);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $return = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($return, true);
        return $result;
    }
}