<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Image;
use app\admin\model\Goods;
use app\admin\model\Category;
use app\admin\model\Text;
use app\admin\model\Attribute;
use app\admin\model\Jiaoimgs;
use app\admin\model\Huimgs;
use app\admin\model\Yangimgs;
use app\admin\model\Xiaoimgs;
use app\admin\model\Shiimgs;
use app\admin\model\Peiimgs;
use app\admin\model\Project;
use app\admin\model\Tuan;

class building extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        if(request()->isGet()){
            $list=Goods::order('id','desc')->paginate(15);
        }else if(request()->isPost()){
            $name=request()->param('name');
            $list=Goods::where('building_name','like','%'.$name."%")->order('id','desc')->paginate(15);
        }
        
        return view('index',['list'=>$list]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        // 
        $attr=Attribute::select();
        $type=explode(',',$attr[0]['attr_values']);
        $xiaoshou=explode(',',$attr[1]['attr_values']);
        $jiage=explode(',',$attr[2]['attr_values']);
        $dengji=explode(',',$attr[3]['attr_values']);
        $zongjia=explode(',',$attr[11]['attr_values']);
        $zhuangxiu=explode(',',$attr[4]['attr_values']);
        $ditie=explode(',',$attr[5]['attr_values']);
        $huxing=explode(',',$attr[6]['attr_values']);
        $xingshi=explode(',',$attr[7]['attr_values']);
        $tese=explode(',',$attr[8]['attr_values']);
        $ziliao=explode(',',$attr[9]['attr_values']);
        $tejia=explode(',',$attr[10]['attr_values']);
        $list=Category::where('pid',0)->select();
        $yushou=explode(',',$attr[13]['attr_values']);
        return view('create',['type'=>$type,'xiaoshou'=>$xiaoshou,'jiage'=>$jiage,
        'dengji'=>$dengji,'zhuangxiu'=>$zhuangxiu,'ditie'=>$ditie,'huxing'=>$huxing,'xingshi'=>$xingshi,
        'tese'=>$tese,'ziliao'=>$ziliao,'tejia'=>$tejia,'attr'=>$attr,'list'=>$list,'zongjia'=>$zongjia,'yushou'=>$yushou]);
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
        if(array_key_exists('building_ditie',$data)){
	       $data['building_ditie']=implode(',',$data['building_ditie']);
	}
        if(array_key_exists('building_huxing',$data)){
	       $data['building_huxing']=implode(',',$data['building_huxing']);
	}
	if(array_key_exists('building_xingshi',$data)){
	       $data['building_xingshi']=implode(',',$data['building_xingshi']);
	}
	if(array_key_exists('building_tese',$data)){
	       $data['building_tese']=implode(',',$data['building_tese']);
	}

        
        
        
        $data['building_img']=$this->upload_logo();
        // return var_dump($data['id']);
        $res=Goods::create($data,true);
        
        if($res){
            $this->success('添加成功','admin/building/index');
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

        $list=Goods::where('id',$id)->find();
        $l_ditie=explode(',',$list['building_ditie']);
        $l_huxing=explode(',',$list['building_huxing']);
        $l_xingshi=explode(',',$list['building_xingshi']);
        $l_tese=explode(',',$list['building_tese']);

        $second=Category::where('id',$list['cate_id'])->find();
        $seconds=Category::where('pid',$second['pid'])->select();
        $once=Category::where('id',$second['pid'])->find();
        $lists=Category::where('pid',0)->select();
        // var_dump($second);
        // return;
        $attr=Attribute::select();
        $type=explode(',',$attr[0]['attr_values']);
        $xiaoshou=explode(',',$attr[1]['attr_values']);
        $jiage=explode(',',$attr[2]['attr_values']);
        $dengji=explode(',',$attr[3]['attr_values']);
        $zhuangxiu=explode(',',$attr[4]['attr_values']);
        $ditie=explode(',',$attr[5]['attr_values']);
        $huxing=explode(',',$attr[6]['attr_values']);
        $xingshi=explode(',',$attr[7]['attr_values']);
        $tese=explode(',',$attr[8]['attr_values']);
        $ziliao=explode(',',$attr[9]['attr_values']);
        $tejia=explode(',',$attr[10]['attr_values']);
        $zongjia=explode(',',$attr[11]['attr_values']);
        $yushou=explode(',',$attr[13]['attr_values']);
        return view('edit',['list'=>$list,'type'=>$type,'xiaoshou'=>$xiaoshou,'jiage'=>$jiage,'lists'=>$lists,
        'dengji'=>$dengji,'zhuangxiu'=>$zhuangxiu,'ditie'=>$ditie,'huxing'=>$huxing,'xingshi'=>$xingshi,
        'tese'=>$tese,'ziliao'=>$ziliao,'tejia'=>$tejia,'l_ditie'=>$l_ditie,'l_huxing'=>$l_huxing,'l_xingshi'=>$l_xingshi,
        'l_tese'=>$l_tese,'second'=>$second,'seconds'=>$seconds,'once'=>$once,'zongjia'=>$zongjia,'yushou'=>$yushou]);
    }
    // 
    public function editimgs($id){
        $j_imgs=Jiaoimgs::where('bid',$id)->select();
        $h_imgs=Huimgs::where('bid',$id)->select();
        $x_imgs=Xiaoimgs::where('bid',$id)->select();
        $p_imgs=Peiimgs::where('bid',$id)->select();
        $s_imgs=Shiimgs::where('bid',$id)->select();
        $y_imgs=Yangimgs::where('bid',$id)->select();
            
            // return;
        return view('editimgs',['id'=>$id,'j_imgs'=>$j_imgs,'h_imgs'=>$h_imgs,'x_imgs'=>$x_imgs,'p_imgs'=>$p_imgs,'s_imgs'=>$s_imgs,'y_imgs'=>$y_imgs,'j_imgs'=>$j_imgs]);
    }
    public function saveimgs(){
        $id=request()->param()['bid'];
        // return var_dump($id);
        $this->upload_y($id);
        $this->upload_p($id);
        $this->upload_s($id);
        $this->upload_x($id);
        $this->upload_h($id);
        $this->upload_j($id);
        
        $this->success('操作成功','index');
    }
    public function updatetext($id){
        $bid=request()->param()['bid'];
        $data=request()->param();
        Huimgs::update($data,['id'=>$id]);
        $this->redirect("admin/building/editimgs/id/$bid");
    }
    public function delpics(){
        $data=request()->put();
        $id=$data['id'];
        if (!preg_match('/^\d+$/', $id)) {
            $res = [
                'code' => 10000,
                'msg' => '参数错误'
            ];
            return json($res);
        }
        if($data['type']=='x'){
            $x=Xiaoimgs::where('id',$id)->find();
            Xiaoimgs::destroy($id);
        }else if($data['type']=='y'){
            $x=Yangimgs::where('id',$id)->find();
            Yangimgs::destroy($id);
        }else if($data['type']=='p'){
            $x=Peiimgs::where('id',$id)->find();
            Peiimgs::destroy($id);
        }else if($data['type']=='s'){
            $x=Shiimgs::where('id',$id)->find();
            Shiimgs::destroy($id);
        }else if($data['type']=='j'){
            $x=Jiaoimgs::where('id',$id)->find();
            Jiaoimgs::destroy($id);
        }else if($data['type']=='h'){
            $x=Huimgs::where('id',$id)->find();
            Huimgs::destroy($id);
        }
        $res=[
            'code' => 10001,
            'msg' => 'success'
        ];
        return $res;
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
        if($request->file('building_logo')){
            
            $data['building_img']=$this->upload_logo();
        }
        if(array_key_exists('building_ditie',$data)){
	       $data['building_ditie']=implode(',',$data['building_ditie']);
	}
        if(array_key_exists('building_huxing',$data)){
	       $data['building_huxing']=implode(',',$data['building_huxing']);
	}
	if(array_key_exists('building_xingshi',$data)){
	       $data['building_xingshi']=implode(',',$data['building_xingshi']);
	}
	if(array_key_exists('building_tese',$data)){
	       $data['building_tese']=implode(',',$data['building_tese']);
	}
            // return var_dump($data);
        Goods::update($data,['id'=>$id]);
        $this->redirect('index');
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
        $g=Goods::where('id',$id)->column('building_img')[0];
        unlink(ROOT_PATH.'public'.$g);
        Goods::destroy($id);
        Project::destroy(['bid'=>$id]);
        Tuan::destroy(['bid'=>$id]);
        $this->redirect('index');
    }

    

    private function upload_logo(){
        $file=request()->file('building_logo');
        if(empty($file)){
            $this->error('没有文件上传');
        }
        $info=$file->validate(['size'=>5*1024*1024,'ext'=>'jpg,png,jpeg,gif'])->move(ROOT_PATH.'public'.DS.'uploads');
        if($info){
            $goods_logo=DS.'uploads'.DS.$info->getSaveName();
            $img=\think\Image::open('.'.$goods_logo);
            $img->thumb(800,800)->save('.'.$goods_logo);
            return $goods_logo;
        }else{
            $error=$file->getError();
            $this->error($error);
        }
    }
    public function upload_y($bid)
    {
        $flies = request()->file('y_pics');
        $pics_data = [];
        foreach ($flies as $file) {
            $info = $file->validate(['size' => 3 * 1024 * 1024, 'ext' => 'jpg,gif,jpeg,png'])->move(ROOT_PATH . 'public' . DS . 'uploads');
            if ($info) {
                $pics_origin = DS . 'uploads' . DS . $info->getSaveName();
                $temp = explode(DS, $info->getSaveName());
                $pics_big = DS . 'uploads' . DS . $temp[0] . DS . 'thumb_800_' . $temp[1];
                $pics_small = DS . 'uploads' . DS . $temp[0] . DS . 'thumb_400_' . $temp[1];
                $image = Image::open('.' . $pics_origin);
                $image->thumb(800, 800)->save('.' . $pics_big);
                $image->thumb(400, 400)->save('.' . $pics_small);
                $row = [
                    'bid' => $bid,
                    'y_big' => $pics_big,
                    'y_small' => $pics_small
                ];
                $pics_data[] = $row;
            }
        }
        $goodspic = new Yangimgs();
        $goodspic->saveAll($pics_data);
        
        
    }
    public function upload_p($bid)
    {
        $flies = request()->file('p_pics');
        $pics_data = [];
        foreach ($flies as $file) {
            $info = $file->validate(['size' => 3 * 1024 * 1024, 'ext' => 'jpg,gif,jpeg,png'])->move(ROOT_PATH . 'public' . DS . 'uploads');
            if ($info) {
                $pics_origin = DS . 'uploads' . DS . $info->getSaveName();
                $temp = explode(DS, $info->getSaveName());
                $pics_big = DS . 'uploads' . DS . $temp[0] . DS . 'thumb_800_' . $temp[1];
                $pics_small = DS . 'uploads' . DS . $temp[0] . DS . 'thumb_400_' . $temp[1];
                $image = Image::open('.' . $pics_origin);
                $image->thumb(800, 800)->save('.' . $pics_big);
                $image->thumb(400, 400)->save('.' . $pics_small);
                $row = [
                    'bid' => $bid,
                    'p_big' => $pics_big,
                    'p_small' => $pics_small
                ];
                $pics_data[] = $row;
            }
        }
        $goodspic = new Peiimgs();
        $goodspic->saveAll($pics_data);
    }
    public function upload_x($bid)
    {
        $flies = request()->file('x_pics');
        $pics_data = [];
        foreach ($flies as $file) {
            $info = $file->validate(['size' => 3 * 1024 * 1024, 'ext' => 'jpg,gif,jpeg,png'])->move(ROOT_PATH . 'public' . DS . 'uploads');
            if ($info) {
                $pics_origin = DS . 'uploads' . DS . $info->getSaveName();
                $temp = explode(DS, $info->getSaveName());
                $pics_big = DS . 'uploads' . DS . $temp[0] . DS . 'thumb_800_' . $temp[1];
                $pics_small = DS . 'uploads' . DS . $temp[0] . DS . 'thumb_400_' . $temp[1];
                $image = Image::open('.' . $pics_origin);
                $image->thumb(800, 800)->save('.' . $pics_big);
                $image->thumb(400, 400)->save('.' . $pics_small);
                $row = [
                    'bid' => $bid,
                    'x_big' => $pics_big,
                    'x_small' => $pics_small
                ];
                $pics_data[] = $row;
            }
        }
        $goodspic = new Xiaoimgs();
         $goodspic->saveAll($pics_data);
    }
    public function upload_h($bid)
    {

        $flies = request()->file('h_pics');
        $data=request()->request();
        
        $pics_data = [];
        if(is_array($flies)){
            foreach ($flies as $k=>$file) {
                $info = $file->validate(['size' => 3 * 1024 * 1024, 'ext' => 'jpg,gif,jpeg,png'])->move(ROOT_PATH . 'public' . DS . 'uploads');
                if ($info) {
                    $pics_origin = DS . 'uploads' . DS . $info->getSaveName();
                    $temp = explode(DS, $info->getSaveName());
                    $pics_big = DS . 'uploads' . DS . $temp[0] . DS . 'thumb_800_' . $temp[1];
                    $pics_small = DS . 'uploads' . DS . $temp[0] . DS . 'thumb_400_' . $temp[1];
                    $image = Image::open('.' . $pics_origin);
                    $image->thumb(800, 800)->save('.' . $pics_big);
                    $image->thumb(400, 400)->save('.' . $pics_small);
                    $row = [
                        'bid' => $bid,
                        'h_big' => $pics_big,
                        'h_small' => $pics_small,
                        'content'=>$data['content'][$k],
                        'mian' => $data['mian'][$k],
                        'jia' => $data['jia'][$k]
                    ];
                    foreach($data as $v){
    
                    }
                    $pics_data[] = $row;
                }
            }
        }else{
            $info = $flies->validate(['size' => 3 * 1024 * 1024, 'ext' => 'jpg,gif,jpeg,png'])->move(ROOT_PATH . 'public' . DS . 'uploads');
                if ($info) {
                    $pics_origin = DS . 'uploads' . DS . $info->getSaveName();
                    $temp = explode(DS, $info->getSaveName());
                    $pics_big = DS . 'uploads' . DS . $temp[0] . DS . 'thumb_800_' . $temp[1];
                    $pics_small = DS . 'uploads' . DS . $temp[0] . DS . 'thumb_400_' . $temp[1];
                    $image = Image::open('.' . $pics_origin);
                    $image->thumb(800, 800)->save('.' . $pics_big);
                    $image->thumb(400, 400)->save('.' . $pics_small);
                    $row = [
                        'bid' => $bid,
                        'h_big' => $pics_big,
                        'h_small' => $pics_small,
                        'content'=>$data['content'][$k],
                        'mian' => $data['mian'][$k],
                        'jia' => $data['jia'][$k]
                    ];
                    foreach($data as $v){
    
                    }
                    $pics_data[] = $row;
                }
        }
        
        $goodspic = new Huimgs();
         $goodspic->saveAll($pics_data);
    }
    public function upload_s($bid)
    {
        $flies = request()->file('s_pics');
        $pics_data = [];
        foreach ($flies as $file) {
            $info = $file->validate(['size' => 3 * 1024 * 1024, 'ext' => 'jpg,gif,jpeg,png'])->move(ROOT_PATH . 'public' . DS . 'uploads');
            if ($info) {
                $pics_origin = DS . 'uploads' . DS . $info->getSaveName();
                $temp = explode(DS, $info->getSaveName());
                $pics_big = DS . 'uploads' . DS . $temp[0] . DS . 'thumb_800_' . $temp[1];
                $pics_small = DS . 'uploads' . DS . $temp[0] . DS . 'thumb_400_' . $temp[1];
                $image = Image::open('.' . $pics_origin);
                $image->thumb(800, 800)->save('.' . $pics_big);
                $image->thumb(400, 400)->save('.' . $pics_small);
                $row = [
                    'bid' => $bid,
                    's_big' => $pics_big,
                    's_small' => $pics_small
                ];
                $pics_data[] = $row;
            }
        }
        $goodspic = new Shiimgs();
         $goodspic->saveAll($pics_data);
    }
    public function upload_j($bid)
    {
        $flies = request()->file('j_pics');
        $pics_data = [];
        foreach ($flies as $file) {
            $info = $file->validate(['size' => 3 * 1024 * 1024, 'ext' => 'jpg,gif,jpeg,png'])->move(ROOT_PATH . 'public' . DS . 'uploads');
            if ($info) {
                $pics_origin = DS . 'uploads' . DS . $info->getSaveName();
                $temp = explode(DS, $info->getSaveName());
                $pics_big = DS . 'uploads' . DS . $temp[0] . DS . 'thumb_800_' . $temp[1];
                $pics_small = DS . 'uploads' . DS . $temp[0] . DS . 'thumb_400_' . $temp[1];
                $image = Image::open('.' . $pics_origin);
                $image->thumb(800, 800)->save('.' . $pics_big);
                $image->thumb(400, 400)->save('.' . $pics_small);
                $row = [
                    'bid' => $bid,
                    'j_big' => $pics_big,
                    'j_small' => $pics_small
                ];
                $pics_data[] = $row;
            }
        }
        $goodspic = new Jiaoimgs();
         $goodspic->saveAll($pics_data);
    }
    
}
