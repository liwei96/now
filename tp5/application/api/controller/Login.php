<?php

namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Session;
use think\Validate;
use app\api\model\Staff;
use app\api\model\Role;


class Login extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    function createNoncestr( $length = 32 ) 
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {  
            $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
        }  
        return $str;
    }
    public function login(Request $request)
    {
        //
        $data=$request->param()['value'];
        $phone=Staff::where('name',$data['name'])->column('urgent')[0];
        if(!$phone){
            return json(['code'=>300]);
        }
        $ma=$data['ma'];
        $rule=[
            'name'=>'require',
            'password'=>'require|length:6,12'
        ];
        $msg=[
            'name.require'=>'用户名不能为空',
            'password.require'=>'密码不能为空',
            'password.length'=>'密码长度为6到12位'
        ];
        $validate=new Validate($rule,$msg);
        if(!$validate->check($data)){
            $error=$validate->getError();
            $this->error($error);
        }
        if(!$ma==cache($phone)){
            return json(['code'=>1001,'msg'=>'验证码错误']);
        }
        $password=encrypt_password($data['password']);
        $num=$this->createNoncestr(8);
        $re=Staff::where([['name','eq',$data['name']],['password','eq',$password]])->find();
        if($re){ 
            $re['ids']=Role::where('id',$re['job'])->column(['ids'])[0];
            session('user',$re);
            cache($phone,null);
            cache($re['name'],$num,1800);
            return json(['code'=>200,'num'=>$num,'re'=>$re]);
        }else{
            return json(['code'=>300]);
        }
    }

    public function getcode(){
        $data=request()->param();
        $phone=Staff::where('name',$data['name'])->column('urgent')[0];
        if(!$phone){
            return json(['code'=>300]);
        }
        $ma=mt_rand(1000,9999);
        $register_time = cache($phone.'time') ? : 0;
            if (time() - $register_time < 60) {
                $res = [
                    'code' => 10003,
                    'msg' => '发送太频繁，稍后再试',
                ];
                return json($res);
            }
            
            $result=sendmsg($phone,$ma);
            if($result){
                cache($phone,$ma,300);
                cache($phone.'time',time(),60);
                $res=[
                    'code' => 200,
                ];
                return json($res);
            }else{
                $res=[
                    'code' => 300,
                    'msg' => '发送失败'
                ];
                return json($res);
            }
    }
    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
