<?php

namespace app\api\controller;

use think\Controller;
use think\Request;
use app\api\model\Integral as IntegralModel;

class Integral extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index($id)
    {
        //
        $data=Db::query("select * from (select * from integral order by id desc) as data where id=$id group by bid order by id desc");
        foreach($data as $v){
            $list=IntegralModel::where('bid',$v['bid'])->order('total_integral','desc')->column('total_integral');
            $v['name']=Building::where('id',$v['bid'])->column()[0];
            $ll=$v['total_integral'];
            foreach($list as $k=>$n){
                if($ll==$n){
                    $v['pai']=($k+1);
                }
            }
        }
        return json(['code'=>200,'data'=>$data]);
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
    public function save(Request $request)
    {
        //
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
        $list=IntegralModel::where('bid',$id)->select();
        return json(['code'=>200,'data'=>$list]);
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
