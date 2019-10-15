<?php

namespace app\api\controller;

use think\Controller;
use think\Request;
use app\api\model\Zhi as ZhiModel;

class Zhi extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        $mais=ZhiModel::where('type','买房')->select();
        $tus=ZhiModel::where('type','投资')->select();
        $dais=ZhiModel::where('type','贷款')->select();
        $res=[
            'mais'=>$mais,
            'tus'=>$tus,
            'dais'=>$dais,
            'code'=>200
        ];
        return json($res);
    }

    public function type(){
        $type=request()->param()['type'];
        if($type==1){
            $data=ZhiModel::where('type','eq',1)->select();
        }else if($type==2){
            $data=ZhiModel::where('type','eq',2)->select();
        }else if($type==3){
            $data=ZhiModel::where('type','eq',3)->select();
        }
        $res=[
            'code'=>200,
            'data'=>$data
        ];
        return json($res);
    }
    public function sou(){
        $name=request()->param()['title'];
        $data=ZhiModel::where('title','like','%'.$name.'%')->select();
        $res=[
            'code'=>200,
            'data'=>$data
        ];
        return json($res);
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
        $data=$request->param()['value'];
        $data['type']=$request->param()['type'];
        ZhiModel::create($data);
        return json(['code'=>200]);
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
        $data=ZhiModel::where('id',$id)->find();
        $res=[
            'code'=>200,
            'data'=>$data
        ];
        return json($res);
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
        $data=$request->param()['value'];
        ZhiModel::update($data,['id'=>$id]);
        return json(['code'=>200]);
    }

    /**'
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
        ZhiModel::destroy($id);
        return json(['code'=>200]);
    }
}
