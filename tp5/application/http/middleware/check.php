<?php

namespace app\http\middleware;
use think\facade\Session;
use think\facade\Cache;
use app\api\model\Auth;

class check
{
    function checkauth(){
        $role_id=session('user.job');
        if(1==$role_id){
            return true;
        }
        $controller=request()->controller();
        $action=request()->action();
        if($controller=='index' && $action=='index'){
            return true;
        }

        $role=session('user');
        $role_auth_ids=$role['ids'];
        $auth=Auth::where([
            ['auth_c','eq',$controller],
            ['auth_a','eq',$action]
        ])->find();
        if(!$auth){
            return true;
        }
        $auth_id=$auth['id'];
        if(!in_array($auth_id,explode(',',$role_auth_ids))){
            return false;
        }else{
            return true;
        }
    }
    
    public function handle($request, \Closure $next)
    {
        $data=$request->param();
        if($data['num']==Cache::get($data['name'])){
            if(Session::get('user')){
                Cache::set($data['name'],$data['num'],1800);
                if($this->checkauth()){
                    return $next($request);
                }else{
                    return json(['code'=>'403','msg'=>'没有权限']);
                }

                
            }
        }else{
            return json(['code'=>402,'msg'=>'登录超时，请重新登录']);
        }
        // return $next($request);
    }
}
