<?php

namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Db;
use think\Session;
use app\api\model\User as UserModel;
use app\api\model\Staff;
use app\api\model\Shouhou;
use app\api\model\Shouqian;
use app\api\model\Shouzhong;
use app\api\model\Area;
use app\api\model\Building;
use app\api\model\Gen;

class User extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        if(session('user.guide')!=1){
            $ids=Staff::where('pid',session('user.id'))->column('id');
            $ids=getids($ids);
            $data=UserModel::where('s_id','in',$ids)->select();
        }else{
            $data=UserModel::where('s_id','eq',session('user.id'))->select();
        }
        
        foreach($data as $v){
            $v['project']=Building::where('id',$v['project'])->column('building_name')[0];
            $tt=Gen::where('u_id',$v['id'])->order('id','desc')->limit(1)->column('t_time');
            if($tt){
                $v['t_time']=date('Y-m-d H:i',$tt[0]);
                $v['g_time']=date('Y-m-d H:i',Gen::where('u_id',$v['id'])->order('id','desc')->limit(1)->column('create_time')[0]);
            }
           
            
        }
        return json($data);
    }

    public function lists(){
        $list=Building::field("building_name,id")->select();
        return json(['code'=>200,'list'=>$list]);
    }
    public function area(){
        $list = Area::where('pid', 0)->select();

        $data = [];
        foreach ($list as $v) {
            $data[$v['area_name']] = Area::where('pid', $v['id'])->select();
        }
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
    public function changeg($id)
    {
        //
        UserModel::update(['s_id'=>0],['id'=>$id]);
        return json(['code'=>200]);
    }

    public function changes($id){
        UserModel::update(['s_id'=>Session::get('user.id')],['id'=>$id]);
        return json(['code'=>200]);
    }

    public function change(){
        $yuans=Staff::where('guide',1)->select();
        $data=[];
        foreach($yuans as $v){
            $data[$v['id']]=Attribute::where('id',$v['id'])->column('area_name');
        }
        $res=[
            'code'=>200,
            'data'=>$data
        ];
        return json($res);
    }
    public function get($id){
        $data=Staff::where('pid',$id)->column('id','name');
        $res=[
            'code'=>200,
            'data'=>$data
        ];
        return json($res);
    }
    public function changed(){
        $data=request()->param()['value'];
        UserModel::update(['s_id'=>$data['s_id']],['id'=>$data['id']]);
        return json(['code'=>200]);
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
        $data['leixing']=implode(',',$data['leixing']);
        $data['ceng']=implode(',',$data['ceng']);
        $dd=[];
        foreach($data['region'] as $v){
            $dd[]=Area::where('area_name',$v)->column('id')[0];
        }
        $data['region']=implode(',',$dd);
        $data['huxing']=implode(',',$data['huxing']);
        $data['mianji']=implode(',',$data['mianji']);
        $data['zong']=implode(',',$data['zong']);
        $data['k_time']=substr($data['k_time'],0,10);
        $data['time']=substr($data['time'],0,10);
        // 默认测试数据
        $data['s_id']=11;
        UserModel::create($data);
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

    public function qindex(){
        $data=Shouqian::select();
        $res=['code'=>200,'data'=>$data];
        return json($res);
    }
    public function qedit($id){
        $data=Shouqian::where('id',$id)->find();
        $res=['code'=>200,'data'=>$data];
        return json($res);
    }
    public function hindex(){
        $data=Shouhou::select();
        $res=['code'=>200,'data'=>$data];
        return json($res);
    }
    public function hedit($id){
        $data=Shouhou::where('id',$id)->find();
        $res=['code'=>200,'data'=>$data];
        return json($res);
    }
    public function qsave(){
        $data=request()->param()['value'];
        $data['u_id']=request()->param()['u_id'];
        $data['time']=substr($data['time'],0,10);
        Shouqian::create($data);
        return json(['code'=>200]);
    }
    public function hsave(){
        $data=request()->param()['value'];
        $data['u_id']=request()->param()['u_id'];
        Shouhou::create($data);
        return json(['code'=>200]);
    }
    public function zsave(){
        $data=request()->param();
        $data['u_id']=request()->param()['u_id'];
        Shouzhong::create($data);
        return json(['code'=>200]);
    }
    public function qupdate($id){
        $data=request()->param()['value'];
        Shouqian::update($data,['id'=>$id]);
        return json(['code'=>200]);
    }
    public function hupdate($id){
        $data=request()->param()['value'];
        Shouhou::update($data,['id'=>$id]);
        return json(['code'=>200]);
    }
    public function qdelete($id){
        Shouqian::destroy($id);
        return json(['code'=>200]);
    }
    public function hdelete($id){
        Shouhou::destroy($id);
        return json(['code'=>200]);
    }

    public function type(){
        $type=request()->param()['type'];
        if($type=='公客'){
            $data=UserModel::where('s_id','eq','0')->select();
        }else if($type=='私客'){
            $data=UserModel::where('s_id','eq',session('user.id'))->select();
        }
        $res=[
            'code'=>200,
            'data'=>$data
        ];
        return json($res);
    }
    public function sou(){
        $tiao=request()->param()['value'];
        $where=[];
        $where[]=['s_id','eq',session('user.id')];
        if (array_key_exists('building_name',$tiao)) {
            if($tiao['building_name']){
                $id=Building::where('building_name','like','%'.$tiao['building_name'].'%')->column('id');
                $where[]=['project','in',$id];
            }
        }
        if (array_key_exists('region',$tiao)) {
            if($tiao['region']){
                $ss=$tiao['region'][2];
                $l=Area::where('area_name','eq',$ss)->column('id')[0];
                $where[]=['region','in',$l];
            }
        }
        if (array_key_exists('huxing',$tiao)) {
            if($tiao['huxing']){
                $where[]=['huxing','in',$tiao['huxing']];
            }
        }
        
        
        
        $data=UserModel::where($where)->select();
        foreach($data as $v){
            $v['project']=Building::where('id',$v['project'])->column('building_name')[0];
            $tt=Gen::where('u_id',$v['id'])->order('id','desc')->limit(1)->column('t_time');
            if($tt){
                $v['t_time']=date('Y-m-d H:i',$tt[0]);
                $v['g_time']=date('Y-m-d H:i',Gen::where('u_id',$v['id'])->order('id','desc')->limit(1)->column('create_time')[0]);
            }
        }
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
        $data=UserModel::where('id',$id)->find();
        $data['leixing']=explode(',',$data['leixing']);
        $data['ceng']=explode(',',$data['ceng']);
        $data['region']=explode(',',$data['region']);
        $l=$data['region'][0];
        
        $two=Area::where('id',$l)->column('pid')[0];
        
        $two=Area::where('id',$two)->find();
        $one=Area::where('id',$two['pid'])->column('area_name')[0];
        $data['one']=[$one,$two['area_name']];
        $data['two']=Area::where('pid','eq',$two['id'])->field('area_name,id')->select();
        $data['huxing']=explode(',',$data['huxing']);
        $data['zong']=explode(',',$data['zong']);
        $data['mianji']=explode(',',$data['mianji']);
        $data['project']=Building::where('id',$data['project'])->column('building_name')[0];
        $qian=Shouqian::where('u_id',$id)->select();
        $hou=Shouhou::where('u_id',$id)->select();
        $res=[
            'code'=>200,
            'data'=>$data,
            'qian'=>$qian,
            'hou'=>$hou
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
        $data['leixing']=implode(',',$data['leixing']);
        $data['ceng']=implode(',',$data['ceng']);
        $dd=[];
        foreach($data['region'] as $v){
            $dd[]=Area::where('area_name',$v)->column('id')[0];
        }
        $data['region']=implode(',',$dd);
        $data['mianji']=implode(',',$data['mianji']);
        $data['zong']=implode(',',$data['zong']);
        $data['huxing']=implode(',',$data['huxing']);
        $data['k_time']=substr($data['k_time'],0,10);
        $data['time']=substr($data['time'],0,10);
        UserModel::update($data,['id'=>$id]);
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
        UserModel::destroy($id);
        return json(['code'=>200]);
    }
    public function tong(){
        $type=request()->param()['value']['type'];
        if($type==1){
            $s=strtotime('-12days');
            $data=Db::name('user')->whereTime('create_time',[date('Y-m-d',$s),date('Y-m-d',time())])->field("DATE_FORMAT(FROM_UNIXTIME(create_time),'%Y-%m-%d') as date,count(*) as total")
            ->group("DATE_FORMAT(FROM_UNIXTIME(create_time),'%Y-%m-%d')")->select();
        }else if($type==2){
            $s=strtotime('-12week');
            $data=Db::name('user')->whereTime('create_time',[date('Y-m-d',$s),date('Y-m-d',time())])->field("WEEK(DATE_FORMAT(FROM_UNIXTIME(create_time),'%Y-%m-%d')) as week,count(*) as total")
            ->group("WEEK(DATE_FORMAT(FROM_UNIXTIME(create_time),'%Y-%m-%d'))")->select();
        }else if($type==3){
            $s=strtotime('-12month');
            $data=Db::name('user')->whereTime('create_time',[date('Y-m-d',$s),date('Y-m-d',time())])->field("MONTH(DATE_FORMAT(FROM_UNIXTIME(create_time),'%Y-%m-%d')) as month,count(*) as total")
            ->group("YEAR(DATE_FORMAT(FROM_UNITIME(create_time),'%T-%m-%d'))")->select();
        }else if($type==4){
            $s=strtotime('-12quarter');
            $data=Db::name('user')->whereTime('create_time',[date('Y-m-d',$s),date('Y-m-d',time())])->field("QUARTER(DATE_FORMAT(FROM_UNIXTIME(create_time),'%Y-%m-%d')) as quarter,count(*) as total")
            ->group("QUARTER(DATE_FORMAT(FROM_UNIXTIME(create_time),'%Y-%m-%d'))")->select();
        }else if($type==5){
            $s=strtotime('-12year');
            $data=Db::name('user')->whereTime('create_time',[date('Y-m-d',$s),date('Y-m-d',time())])->field("YEAR(DATE_FORMAT(FROM_UNIXTIME(create_time),'%Y-%m-%d')) as year,count(*) as total")
            ->group("YEAR(DATE_FORMAT(FROM_UNIXTIME(create_time),'%Y-%m-%d'))")->select();
        }
        $res=[
            'code'=>200,
            'data'=>$data
        ];
        return json($res);
    }

    public function bing(){
        $type=request()->param()['value']['type'];
        $s=strtotime('-3days');
        if($type==1){
            
            $t=strtotime('-7days');
            $data=Db::name('user')->whereTime('create_time',[date('Y-m-d',$t),date('Y-m-d',time())])->field("count(*) as total")->select();
            $new=Db::name('record')->whereTime('create_time',[date('Y-m-d',$s),date('Y-m-d',time())])->field("count(*) as total")->select();
            $old=Db::name('record')->whereTime('create_time',[date('Y-m-d',$t),date('Y-m-d',$s)])->field("count(*) as total")->select();
        }else if($type==2){
            $t=strtotime('-1month');
            $data=Db::name('user')->whereTime('create_time',[date('Y-m-d',$t),date('Y-m-d',time())])->field("count(*) as total")->select();
            $new=Db::name('record')->whereTime('create_time',[date('Y-m-d',$s),date('Y-m-d',time())])->field("count(*) as total")->select();
            $old=Db::name('record')->whereTime('create_time',[date('Y-m-d',$t),date('Y-m-d',$s)])->field("count(*) as total")->select();
        }else if($type==3){
            $t=strtotime('-1quarter');
            $data=Db::name('user')->whereTime('create_time',[date('Y-m-d',$t),date('Y-m-d',time())])->field("count(*) as total")->select();
            $new=Db::name('record')->whereTime('create_time',[date('Y-m-d',$s),date('Y-m-d',time())])->field("count(*) as total")->select();
            $old=Db::name('record')->whereTime('create_time',[date('Y-m-d',$t),date('Y-m-d',$s)])->field("count(*) as total")->select();
        }else if($type==4){
            $t=strtotime('-1year');
            $data=Db::name('user')->whereTime('create_time',[date('Y-m-d',$t),date('Y-m-d',time())])->field("count(*) as total")->select();
            $new=Db::name('record')->whereTime('create_time',[date('Y-m-d',$s),date('Y-m-d',time())])->field("count(*) as total")->select();
            $old=Db::name('record')->whereTime('create_time',[date('Y-m-d',$t),date('Y-m-d',$s)])->field("count(*) as total")->select();
        }

        $res=[];
        $res['data']=$data;
        $res['new']=$new;
        $res['old']=$old;
        $res['code']=200;
        return json($res);
    }

    public function like($id){
        $data=UserModel::where('id',$id)->find();
        $where=[];
        $where[]=['zong','in',$data['zong']];
        $where[]=['building_xingshi','eq',$data['leixing']];
        $where[]=['cenggao','in',$data['ceng']];
        $where[]=['cate_id','in',$data['region']];
        $where[]=['building_huxing','in',$data['huxing']];
        $data=Building::where($where)->select();
        $res=[
            'code'=>200,
            'data'=>$data
        ];
        return json($res);
    }

    public function xiang(){
        $type=request()->param()['type'];
        if($type==1){
            $building=Db::name('user')->whereTime('create_time','today')->field("project")->select();
            $ids=Db::name('dai')->whereTime('create_time','today')->field("id")->select();
            $bold=Db::name('user')->whereTime('create_time','yesterday')->field("project")->select();
            $dold=Db::name('dai')->whereTime('create_time','yesterday')->field("id")->select();
            $building=array_unique($building);
            $list=[];
            foreach($building as $v){
                $li=[];
                $li[]=Building::where('id',$v)->column('building_name')[0];
                $li[]=Db::name('user')->where('project',$v)->whereTime('create_time','today')->count("id");
                $dai=Building::where('id',$v)->column('d_id');
                $dai=explode(',',$dai);
                $dai=array_intersect($dai,$ids);
                $li[]=count($dai);
                $li[]=round($li[2]/$li[1],2)*100;
                $bold=Db::name('user')->where('project',$v)->whereTime('create_time','yesterday')->count("id");
                $ss=array_intersect($dai,$dold);
                $li[]=round($ss/$bold,2)*100;
                $li[]=$li[4]-$li[3];
                $list[]=$li;
            }
        }else if($type==2){
            $building=Db::name('user')->whereTime('create_time','week')->field("project")->select();
            $ids=Db::name('dai')->whereTime('create_time','week')->field("id")->select();
            $bold=Db::name('user')->whereTime('create_time','last week')->field("project")->select();
            $dold=Db::name('dai')->whereTime('create_time','last week')->field("id")->select();
            $building=array_unique($building);
            $list=[];
            foreach($building as $v){
                $li=[];
                $li[]=Building::where('id',$v)->column('building_name')[0];
                $li[]=Db::name('user')->where('project',$v)->whereTime('create_time','week')->count("id");
                $dai=Building::where('id',$v)->column('d_id');
                $dai=explode(',',$dai);
                $dai=array_intersect($dai,$ids);
                $li[]=count($dai);
                $li[]=round($li[2]/$li[1],2)*100;
                $bold=Db::name('user')->where('project',$v)->whereTime('create_time','last week')->count("id");
                $ss=array_intersect($dai,$dold);
                $li[]=round($ss/$bold,2)*100;
                $li[]=$li[4]-$li[3];
                $list[]=$li;
            }
        }else if($type==3){
            $building=Db::name('user')->whereTime('create_time','month')->field("proje            ct")->select();
            $ids=Db::name('dai')->whereTime('create_time','month')->field("id")->select();
            $bold=Db::name('user')->whereTime('create_time','last month')->field("project")->select();
            $dold=Db::name('dai')->whereTime('create_time','last month')->field("id")->select();
            $building=array_unique($building);
            $list=[];
            foreach($building as $v){
                $li=[];
                $li[]=Building::where('id',$v)->column('building_name')[0];
                $li[]=Db::name('user')->where('project',$v)->whereTime('create_time','month')->count("id");
                $dai=Building::where('id',$v)->column('d_id');
                $dai=explode(',',$dai);
                $dai=array_intersect($dai,$ids);
                $li[]=count($dai);
                $li[]=round($li[2]/$li[1],2)*100;
                $bold=Db::name('user')->where('project',$v)->whereTime('create_time','last month')->count("id");
                $ss=array_intersect($dai,$dold);
                $li[]=round($ss/$bold,2)*100;
                $li[]=$li[4]-$li[3];
                $list[]=$li;
            }
        }else if($type==4){
            $building=Db::name('user')->whereTime('create_time','year')->field("project")->select();
            $ids=Db::name('dai')->whereTime('create_time','year')->field("id")->select();
            $bold=Db::name('user')->whereTime('create_time','last year')->field("project")->select();
            $dold=Db::name('dai')->whereTime('create_time','last year')->field("id")->select();
            $building=array_unique($building);
            $list=[];
            foreach($building as $v){
                $li=[];
                $li[]=Building::where('id',$v)->column('building_name')[0];
                $li[]=Db::name('user')->where('project',$v)->whereTime('create_time','year')->count("id");
                $dai=Building::where('id',$v)->column('d_id');
                $dai=explode(',',$dai);
                $dai=array_intersect($dai,$ids);
                $li[]=count($dai);
                $li[]=round($li[2]/$li[1],2)*100;
                $bold=Db::name('user')->where('project',$v)->whereTime('create_time','last year')->count("id");
                $ss=array_intersect($dai,$dold);
                $li[]=round($ss/$bold,2)*100;
                $li[]=$li[4]-$li[3];
                $list[]=$li;
            }
        }
        $res=[
            'code'=>200,
            'data'=>$list
        ];
        return json($res);
    }
}
