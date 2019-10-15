<?php

namespace app\api\controller;

use think\Controller;
use think\Request;
use app\api\model\Information as InformationModel;
use app\api\model\News;
use app\api\model\Building;

class Information extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        $data=InformationModel::select();
        foreach($data as $v){
            $v['bid']=Building::where('id',$v['bid'])->column('building_name')[0];
        }
        $res=[
            'code'=>200,
            'data'=>$data,
        ];
        return json($res);
    }
    public function news(){
        $data=News::select();
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
        $data=Building::field('building_name,id')->select();
        $res=[
            'code'=>200,
            'data'=>$data
        ];
        return json($res);
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
        $data=$request->param();
            $re=InformationModel::create($data);
            if($re){
                $res=[
                    'code'=>200,
                ];
            }else{
                $res=[
                    'code'=>300,
                    'msg'=>'添加失败'
                ];
            }
        
        return json($res);
    }
    public function nsave(){
        $data=request()->param();
        $re=News::create($data);
            if($re){
                $res=[
                    'code'=>200,
                ];
            }else{
                $res=[
                    'code'=>300,
                    'msg'=>'添加失败'
                ];
            }
        return json($res);
    }
    
    public function psou(){
        $data=request()->param();
        $where=[];
        if(array_key_exists('bid',$data)){
            $ids=Building::where('building_name','like','%'.$data['bid'].'%')->column('id');
            $where[]=['bid','in',$ids];
        }
        if(array_key_exists('time',$data)){
            $ss=InformationModel::where($where)->whereTime('create_time',$data['time'])->select();
        }else{
            $ss=InformationModel::where($where)->select();
        }
        if($ss){
            foreach($ss as $v){
                $v['bid']=Building::where('id',$v['bid'])->column('building_name')[0];
            }
        }
        $res=[
            'code'=>200,
            'data'=>$ss
        ];
        return json($res);
    }
    public function nsou(){
        $type=request()->param()['time'];
        $data=News::whereTime('create_time',$type)->select();
        $res=[
            'code'=>200,
            'data'=>$data
        ];
        return json($res);
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
        $data=InformationModel::where('id',$id)->find();
       
        $res=[
            'code'=>200,
            'data'=>$data
        ];
        return json($res);
    }
    public function nedit($id){
        $data=News::where('id',$id)->find();
        $res=[
            'code'=>200,
            'data'=>$data
        ];
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
        $data=$request->param();
            unset($data['type']);
            InformationModel::update($data,['id'=>$id]);
        return json(['code'=>200]);
    }

    public function nupdate(Request $request,$id){
        $data=$request->param();
        News::update($data,['id'=>$id]);
        return json(['code'=>200]);
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
            InformationModel::destroy($id);
        return json(['code'=>200]);
    }
    public function ndelete($id){
        News::destroy($id);
        return json(['code'=>200]);
    }

    public function ntong(){
        $type=request()->param()['type'];
        if($type==1){
            $s=strtotime('-12days');
            $data=Db::name('news')->whereTime('time', 'between', [date('Y-m-d',$s), date('Y-m-d',time())])->field("DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d') as date,count(*) as total")
            ->group("DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d')")->select();
        }else if($type==2){
            $s=strtotime('-12week');
            $data=Db::name('news')->whereTime('time',[date('Y-m-d',$s),date('Y-m-d',time())])->field("WEEK(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d')) as week,count(*) as total")
            ->group("WEEK(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d'))")->select();
        }else if($type==3){
            $s=strtotime('-12month');
            $data=Db::name('news')->whereTime('time',[date('Y-m-d',$s),date('Y-m-d',time())])->field("MONTH(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d')) as month,count(*) as total")
            ->group("MONTH(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d'))")->select();
        }else if($type==4){
            $s=strtotime('-12quarter');
            $data=Db::name('news')->whereTime('time',[date('Y-m-d',$s),date('Y-m-d',time())])->field("QUARTER(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d')) as quarter,count(*) as total")
            ->group("QUARTER(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d'))")->select();
        }else if($type==5){
            $s=strtotime('-12year');
            $data=Db::name('news')->whereTime('time',[date('Y-m-d',$s),date('Y-m-d',time())])->field("YEAR(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d')) as year,count(*) as total")
            ->group("YEAR(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d'))")->select();
        }
        $res=[
            'code'=>200,
            'data'=>$data
        ];
        return json($res);
    }
    public function ptong(){
        $type=request()->param()['type'];
        if($type==1){
            $s=strtotime('-12days');
            $data=Db::name('infromation')->whereTime('time', 'between', [date('Y-m-d',$s), date('Y-m-d',time())])->field("DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d') as date,count(*) as total")
            ->group("DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d')")->select();
        }else if($type==2){
            $s=strtotime('-12week');
            $data=Db::name('infromation')->whereTime('time',[date('Y-m-d',$s),date('Y-m-d',time())])->field("WEEK(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d')) as week,count(*) as total")
            ->group("WEEK(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d'))")->select();
        }else if($type==3){
            $s=strtotime('-12month');
            $data=Db::name('infromation')->whereTime('time',[date('Y-m-d',$s),date('Y-m-d',time())])->field("MONTH(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d')) as month,count(*) as total")
            ->group("MONTH(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d'))")->select();
        }else if($type==4){
            $s=strtotime('-12quarter');
            $data=Db::name('infromation')->whereTime('time',[date('Y-m-d',$s),date('Y-m-d',time())])->field("QUARTER(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d')) as quarter,count(*) as total")
            ->group("QUARTER(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d'))")->select();
        }else if($type==5){
            $s=strtotime('-12year');
            $data=Db::name('infromation')->whereTime('time',[date('Y-m-d',$s),date('Y-m-d',time())])->field("YEAR(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d')) as year,count(*) as total")
            ->group("YEAR(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d'))")->select();
        }
        $res=[
            'code'=>200,
            'data'=>$data
        ];
        return json($res);
    }
}
