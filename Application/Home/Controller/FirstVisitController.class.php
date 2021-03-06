<?php
namespace Home\Controller;


/**
 * 第一次访问页面控制器
 */
class FirstVisitController extends CommonController {

    /**
     * 第一次访问首页
     * 返回用户信息包括 昵称, 头像地址, 真实姓名, 学号
     */
    public function index(){
        $userInfo = $this->_getUserInfo(session('openid'));
        $userInfo['realName'] = $this->_getRealName(session('openid'));
        $userInfo['stu_num'] = $this->_getStuNum(session('openid'));

        $this->ajaxReturn(array(
            'user_info'=> $userInfo
        ));
    }

    /**
     * 处理更新的信息
     */
    public function handleInfo(){
        $where['stu_num'] = I('stu_num');
        $headUrl = $this->_getUserInfo(session('openid'))['headImageUrl'];
        $user = M('user_info')->where($where)->find();

        // 如果用户已存在
        if($user){
            $data = array(
                'user_id' => $user['user_id'],
                'stu_name' => I('real_name'),
                'phone_num' => I('phone'),
                'tencent_num' => I('qq'),
                'headImgUrl' => $headUrl
            );

            $result = M('user_info')->save($data);
        }else{
            $addData = array(
                'stu_num' => I('stu_num'),
                'stu_name' => I('real_name'),
                'phone_num' => I('phone'),
                'tencent_num' => I('qq'),
                'headImgUrl' => $headUrl
            );

            $r = M('user_info')->add($addData);

            if(!is_null($r)){
                session('relace_user_id', $r);
                $result = 1;
            }
        }
        $this->ajaxReturn(array(
            'status'=> $result
        ));
    }
}