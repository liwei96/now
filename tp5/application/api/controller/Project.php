<?php

namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Validate;
use think\Image;
use think\facade\Env;
use app\api\model\Building;
use app\api\model\Area;
use app\api\model\Attribute;
use app\api\model\Jiaoimgs;
use app\api\model\Huimgs;
use app\api\model\Yangimgs;
use app\api\model\Xiaoimgs;
use app\api\model\Shiimgs;
use app\api\model\Peiimgs;
use app\api\model\Tuan;
use app\api\model\Zong;
use app\api\model\Ditie;
use app\api\model\Huxing;
use app\api\model\Tese;
use app\api\model\Dai;
use app\api\model\Record;




class Project extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //

        $list = Building::order('id', 'desc')->select();
        foreach ($list as $v) {
            $n = Area::where('id', $v['cate_id'])->column('pid')[0];
            $s = Area::where('id', $n)->column('pid')[0];
            $v['city'] = Area::where('id', $n)->column('area_name')[0];
            $v['provice'] = Area::where('id', $s)->column('area_name')[0];
        }

        return json($list);
    }
    public function dlist($id)
    {
        $ids = Building::where('id', $id)->column('d_id')[0];
        $ids = explode(',', $ids);
        $data = Dai::where('id', 'in', $ids)->select();
        foreach($data as $v){
            $v['time']=date('Y-m-d H:i',$v['time']);
        }
        $res = [
            'code' => 200,
            'data' => $data
        ];
        return json($res);
    }
    public function clist($id)
    {
        $data = Record::where('project', $id)->select();
        $res = [
            'code' => 200,
            'data' => $data
        ];
        return json($res);
    }
    // 推广等级搜索
    public function gtype()
    {
        $type = request()->param()['type'];
        $data = Building::where('project_extend_dengji', 'eq', $type)->select();
        $res = [
            'code' => 200,
            'data' => $data
        ];
        return json($res);
    }
    // 推广项目名字搜索
    public function tsou()
    {
        $name = request()->param()['building_name'];
        $data = Building::where('building_name', 'like', '%' . $name . '%')->column('building_name');
        $res = [
            'code' => 200,
            'data' => $data
        ];
        return json($res);
    }
    public function list()
    {
        $data = Building::field("id,building_name")->select();
        $res = ['code' => 200, 'name' => $data];
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
        $data = Attribute::select();
        $xiao = explode(',', $data[0]['content']);
        $zhuang = explode(',', $data[1]['content']);
        $ditie = Ditie::column('ditie');
        $huxing = Huxing::column('huxing');
        $xing = explode(',', $data[4]['content']);
        $tese = Tese::column('tese');
        $zong = Zong::column('zong');
        $dan = explode(',', $data[7]['content']);
        $yang = explode(',', $data[8]['content']);
        $min = explode(',', $data[9]['content']);
        $ran = explode(',', $data[10]['content']);
        $tui = explode(',', $data[11]['content']);
        $res = [
            'code' => 200,
            'xiao' => $xiao,
            'zhuang' => $zhuang,
            'ditie' => $ditie,
            'huxing' => $huxing,
            'xing' => $xing,
            'tese' => $tese,
            'zong' => $zong,
            'dan' => $dan,
            'yang' => $yang,
            'min' => $min,
            'ran' => $ran,
            'tui' => $tui
        ];
        return json($res);
    }

    public function getSubCate()
    {
        $list = Area::where('pid', 0)->select();

        $data = [];
        foreach ($list as $v) {
            $data[$v['area_name']] = Area::where('pid', $v['id'])->select();
        }
        $ss = [];
        foreach ($data as $k => $v) {
            foreach ($v as $l) {
                $ss[$k][$l['area_name']] = Area::where('pid', $l['id'])->column('area_name');
            }
        }

        $res = [
            'code' => 200,
            'msg' => '数据获取成功',
            'data' => $ss
        ];
        return json($res);
    }

    // 单个照片上传
    public function test()
    {
        $data = request()->param()['data'];
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < 8; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        $lujing = './uploads/' . date('Ymd');
        if (!is_dir($lujing)) {
            mkdir(iconv("UTF-8", "GBK", $lujing), 0777, true);
        }
        $type = explode(',', $data)[0];
        $type = explode(';', $type)[0];
        $type = explode(':', $type)[1];
        $type = explode('/', $type)[1];
        $newFilePath = '/uploads/' . date('Ymd') . '/' . $str . '.' . $type;

        $dd = explode(',', $data)[1]; //得到post过来的二进制原始数据
        if (empty($dd)) {
            $data = file_get_contents("php://input");
        }
        $r = file_put_contents('.' . $newFilePath, base64_decode($dd));
        return json([
            'code' => 200,
            'url' => 'api.jy1980.com'.$newFilePath
        ]);
    }
    public function ones()
    {
        $list = Area::where('pid', 0)->select();
        $res = [
            'code' => 200,
            'list' => $list
        ];
        return json($res);
    }

    public function getareas($id)
    {
        if (empty($id)) {
            $res = [
                'code' => 10000,
                'msg' => '参数错误'
            ];
            return json($res);
        }
        $list = Area::where('pid', $id)->select();
        $res = [
            'code' => 200,
            'msg' => '数据获取成功',
            'data' => $list
        ];
        return json($res);
    }
    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     * 
     */
    public function save(Request $request)
    {
        //
        $hetong = $request->param()['hetong'];
        $hetong = implode(',', $hetong);
        $img = $request->param()['img'];
        $data = $request->param();
        $data = $data['value'];
        $data['hetong'] = $hetong;
        $data['building_img'] = $img;
        $rule = [
            'building_name' => 'require',
            'building_address' => 'require',

            'building_xiaoshou' => 'require',
            'cate_id' => 'require',
            'building_jiage' => 'require',
            'zongjia' => 'require',
            'building_zhuangxiu' => 'require',
            'building_ditie' => 'require',
            'building_huxing' => 'require',
            'building_tese' => 'require',
            'building_xingshi' => 'require',
            'introduce' => 'require',
            'traffic' => 'require',
            'hushu' => 'require',
            'guiji' => 'require',
            'rongji' => 'require',
            'jianji' => 'require',
            'zong' => 'require',

            'channian' => 'require',
            'jiaotime' => 'require',
            'mapx' => 'require',
            'mapy' => 'require',
            'cenggao' => 'require',
            'humianji' => 'require',
            'wufei' => 'require',
            'chewei' => 'require',
            'shoulou' => 'require',
            'wuye' => 'require',
            'kaifa' => 'require',
            'lvhua' => 'require',
        ];
        $msg = [
            'building_name.require' => '项目名不能为空',
            'building_address.require' => '项目地址不能为空',

            'building_xiaoshou.require' => '销售状态不能为空',
            'cate_id.require' => '项目所在城市不能为空',
            'building_jiage.require' => '项目的单价等级没选',
            'zongjia.require' => '项目的总价等级没选',
            'building_zhuangxiu.require' => '项目的装修情况没选',
            'building_ditie.require' => '临近地铁没选',
            'building_huxing.require' => '项目有哪些户型没选',
            'building_tese.require' => '项目特色没选',
            'building_xingshi.require' => '项目形式没选',
            'introduce.require' => '项目介绍没写',
            'traffic.require' => '到楼盘的交通没写',
            'hushu.require' => '项目有多少户没写',
            'guiji.require' => '规划面积没写',
            'rongji.require' => '容积率没写',
            'jianji.require' => '建筑面积没写',
            'zong.require' => '最低总价没写',

            'channian.require' => '产权年限没写',
            'jiaotime.require' => '交房时间没写',
            'mapx.require' => '楼盘经度没写',
            'mapy.require' => '楼盘维度没写',
            'cenggao.require' => '层高没写',
            'humianji.require' => '户面积没写',
            'wufei.require' => '物业费没写',
            'chewei.require' => '车位数没写',
            'shoulou.require' => '售楼处地址没写',
            'wuye.require' => '物业公司没写',
            'kaifa.require' => '开发商没写',
            'lvhua.require' => '绿化率没写',
        ];
        $validate = new Validate($rule, $msg);
        // if(!$validate->check($data)){
        //     $error=$validate->getError();
        //     $res=['code'=>300,'msg'=>$error];
        //     return json($error);
        // } 

        $data['building_ditie'] = implode(',', $data['building_ditie']);
        $data['building_huxing'] = implode(',', $data['building_huxing']);
        $data['building_tese'] = implode(',', $data['building_tese']);
        $cate_id = Area::where('area_name', 'eq', $data['cate_id'][2])->column('id')[0];
        $data['cate_id'] = $cate_id;
        $data['humianji'] = implode(',', $data['humianji']);
        $data['zongjia'] = implode(',', $data['zongjia']);
        $data['hetong_time'] = substr($data['hetong_time'], 0, 10);
        $data['t_kaitime'] = substr($data['t_kaitime'], 0, 10);
        $data['jiaotime'] = substr($data['jiaotime'], 0, 10);
        $data['o_kaitime'] = substr($data['o_kaitime'], 0, 10);
        $re = Building::create($data);
        if (!$re) {
            $res = ['code' => 301, 'msg' => '新增失败'];
            return json($res);
        } else {
            $res = ['code' => 200];
            return json($res);
        }
    }
    // 审核
    public function shen()
    {
        $data = request()->param()['value'];
        $type = $data['type'];
        if ($type == 1) {
            Building::update(['id' => $data['id'], 'shen' => 1]);
        } else if ($type == 2) {
            Building::update(['id' => $data['id'], 'shen' => 3, 'reason' => $data['reason']]);
        }
        return json(['code' => 200]);
    }


    public function saveimgs()
    {
        $id = request()->param()['bid'];


        // return var_dump($id);
        $this->upload_y($id);
        $this->upload_p($id);
        $this->upload_s($id);
        $this->upload_x($id);
        $this->upload_h($id);
        $this->upload_j($id);
        $res = ['code' => 200];
        return json($res);
    }


    public function tui()
    {
            $data = Building::where('project_extend_dengji', '一级')->column('building_name');
            $res = [
                'cdoe' => 200,
                'data' => $data
            ];
            return json($res);
       
    }
    public function tuisou(){
        $type = request()->param()['type'];
            $data = Building::where('project_extend_dengji', $type)->column('building_name');
            $res = [
                'code' => 200,
                'data' => $data
            ];
            return json($res);
    }

    
    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function sou()
    {
        //
        $ss = request()->param()['value'];
        $where = [];
        if (array_key_exists('building_xingshi',$ss)) {
            if($ss['building_xingshi']){
                $where[] = ['building_xingshi', 'eq', $ss['building_xingshi']];
            }
        }
        if (array_key_exists('building_name',$ss)) {
            if($ss['building_name']){
                $where[] = ['building_name', 'like', '%' . $ss['building_name'] . '%'];
            }
        }
        if (array_key_exists('cate_id',$ss)) {
            if($ss['cate_id']){
                $l=$ss['cate_id'][2];
                $id=Area::where('area_name','eq',$l)->column('id')[0];
                $where[] = ['cate_id', 'eq', $id];
            }
        }
        if (array_key_exists('type',$ss)) {
            if($ss['type']){
                if(array_key_exists('num',$ss)){
                    if($ss['num']){
                        $where[]=[$ss['type'],'in',$ss['num']];
                    }
                }
            }
        }
        if (array_key_exists('huxing',$ss)) {
            if($ss['huxing']){
                $where[] = ['building_huxing', 'in', $ss['huxing']];
            }
        }
        $data = Building::where($where)->select();
        $res = [
            'code' => 200,
            'data' => $data
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
        $dd = Building::where('id', $id)->find();
        $dd['building_ditie'] = explode(',', $dd['building_ditie']);
        $dd['building_huxing'] = explode(',', $dd['building_huxing']);
        $dd['building_xingshi'] = explode(',', $dd['building_xingshi']);
        $dd['building_tese'] = explode(',', $dd['building_tese']);
        $dd['hetong'] = explode(',', $dd['hetong']);
        $one = Area::where('id', $dd['cate_id'])->find();
        $two = Area::where('id', $one['pid'])->find();
        $thr = Area::where('id', $two['pid'])->column('area_name');
        $dd['cate_id'] = [$thr[0], $two['area_name'], $one['area_name']];
        $res = [
            'code' => 200,
            'dd' => $dd
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
        $hetong = $request->param()['hetong'];
        $hetong = implode(',', $hetong);
        $img = $request->param()['img'];
        $data = $request->param();
        $data = $data['value'];
        $data['hetong'] = $hetong;
        $data['building_img'] = $img;
        $rule = [
            'building_name' => 'require',
            'building_address' => 'require',

            'building_xiaoshou' => 'require',
            'city_id' => 'require',
            'building_jiage' => 'require',
            'zongjia' => 'require',
            'building_zhuangxiu' => 'require',
            'building_ditie' => 'require',
            'building_huxing' => 'require',
            'building_tese' => 'require',
            'building_xingshi' => 'require',
            'introduce' => 'require',
            'traffic' => 'require',
            'hushu' => 'require',
            'guiji' => 'require',
            'rongji' => 'require',
            'jianji' => 'require',
            'zong' => 'require',

            'channian' => 'require',
            'jiaotime' => 'require',
            'mapx' => 'require',
            'mapy' => 'require',
            'cenggao' => 'require',
            'humianji' => 'require',
            'wufei' => 'require',
            'chewei' => 'require',
            'shoulou' => 'require',
            'wuye' => 'require',
            'kaifa' => 'require',
            'lvhua' => 'require',
        ];
        $msg = [
            'building_name.require' => '项目名不能为空',
            'building_address.require' => '项目地址不能为空',

            'building_xiaoshou.require' => '销售状态不能为空',
            'city_id.require' => '项目所在城市不能为空',
            'building_jiage.require' => '项目的单价等级没选',
            'zongjia.require' => '项目的总价等级没选',
            'building_zhuangxiu.require' => '项目的装修情况没选',
            'building_ditie.require' => '临近地铁没选',
            'building_huxing.require' => '项目有哪些户型没选',
            'building_tese.require' => '项目特色没选',
            'building_xingshi.require' => '项目形式没选',
            'introduce.require' => '项目介绍没写',
            'traffic.require' => '到楼盘的交通没写',
            'hushu.require' => '项目有多少户没写',
            'guiji.require' => '规划面积没写',
            'rongji.require' => '容积率没写',
            'jianji.require' => '建筑面积没写',
            'zong.require' => '最低总价没写',

            'channian.require' => '产权年限没写',
            'jiaotime.require' => '交房时间没写',
            'mapx.require' => '楼盘经度没写',
            'mapy.require' => '楼盘维度没写',
            'cenggao.require' => '层高没写',
            'humianji.require' => '户面积没写',
            'wufei.require' => '物业费没写',
            'chewei.require' => '车位数没写',
            'shoulou.require' => '售楼处地址没写',
            'wuye.require' => '物业公司没写',
            'kaifa.require' => '开发商没写',
            'lvhua.require' => '绿化率没写',
        ];
        $validate = new Validate($rule, $msg);
        if (!$validate->check($data)) {
            $error = $validate->getError();
            $res = ['code' => 300, 'msg' => $error];
            return json($error);
        }

        $data['building_ditie'] = implode(',', $data['building_ditie']);
        $data['building_huxing'] = implode(',', $data['building_huxing']);
        $data['building_tese'] = implode(',', $data['building_tese']);
        $data['humianji'] = implode(',', $data['humianji']);
        $data['zongjia'] = implode(',', $data['zongjia']);
        $data['hetong_time'] = substr($data['hetong_time'], 0, 10);
        $data['t_kaitime'] = substr($data['t_kaitime'], 0, 10);
        $data['jiaotime'] = substr($data['jiaotime'], 0, 10);
        $data['o_kaitime'] = substr($data['o_kaitime'], 0, 10);
        $re = Building::update($data, ['id' => $id]);
        if (!$re) {
            $res = ['code' => 301, 'msg' => '更新失败'];
            return json($res);
        } else {
            $res = ['code' => 200];
            return json($res);
        }
    }
    public function updatetext($id)
    {
        $bid = request()->param()['bid'];
        $data = request()->param();
        Huimgs::update($data, ['id' => $bid]);
        $res = ['code' => 200];
        return json($res);
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
        $g = Building::where('id', $id)->column('building_img')[0];
        $s = Building::where('id', $id)->column('hetong')[0];
        $s = explode(',', $s);
        $g=explode('/',$g);
        unset($g[0]);
        $g=implode('/',$g);
        unlink(Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR . $g);
        foreach ($s as $v) {
            $l=explode('/',$v);
            unset($l[0]);
            $v=implode('/',$l);
            unlink(Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR . $v);
        }
        Building::destroy($id);
        Tuan::destroy(['bid' => $id]);
        ProjectModel::destroy(['bid' => $id]);
        $res = [
            'code' => 200
        ];
        return json($res);
    }
    public function delpics()
    {
        $data = request()->put();
        $id = $data['id'];
        if (!preg_match('/^\d+$/', $id)) {

            $res = [
                'code' => 10000,
                'msg' => '参数错误'
            ];
            return json($res);
        }
        if ($data['type'] == 'x') {
            $x = Xiaoimgs::where('id', $id)->find();
            $s=explode('/',$x['x_small']);
            $b=explode('/',$x['x_big']);
            unset($s[0]);
            unset($b[0]);
            $s=implode('/',$s);
            $b=implode('/',$b);
            unlink(Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR . $b);
            unlink(Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR . $s);
            Xiaoimgs::destroy($id);
        } else if ($data['type'] == 'y') {
            $x = Yangimgs::where('id', $id)->find();
            $s=explode('/',$x['y_small']);
            $b=explode('/',$x['y_big']);
            unset($s[0]);
            unset($b[0]);
            $s=implode('/',$s);
            $b=implode('/',$b);
            unlink(Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR . $b);
            unlink(Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR . $s);
            Yangimgs::destroy($id);
        } else if ($data['type'] == 'p') {
            $x = Peiimgs::where('id', $id)->find();
            $s=explode('/',$x['p_small']);
            $b=explode('/',$x['p_big']);
            unset($s[0]);
            unset($b[0]);
            $s=implode('/',$s);
            $b=implode('/',$b);
            unlink(Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR . $b);
            unlink(Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR . $s);
            Peiimgs::destroy($id);
        } else if ($data['type'] == 's') {
            $x = Shiimgs::where('id', $id)->find();
            $s=explode('/',$x['s_small']);
            $b=explode('/',$x['s_big']);
            unset($s[0]);
            unset($b[0]);
            $s=implode('/',$s);
            $b=implode('/',$b);
            unlink(Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR . $b);
            unlink(Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR . $s);
            Shiimgs::destroy($id);
        } else if ($data['type'] == 'j') {
            $x = Jiaoimgs::where('id', $id)->find();
            $s=explode('/',$x['j_small']);
            $b=explode('/',$x['j_big']);
            unset($s[0]);
            unset($b[0]);
            $s=implode('/',$s);
            $b=implode('/',$b);
            unlink(Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR . $b);
            unlink(Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR . $s);
            Jiaoimgs::destroy($id);
        } else if ($data['type'] == 'h') {
            $x = Huimgs::where('id', $id)->find();
            $s=explode('/',$x['h_small']);
            $b=explode('/',$x['h_big']);
            unset($s[0]);
            unset($b[0]);
            $s=implode('/',$s);
            $b=implode('/',$b);
            unlink(Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR . $b);
            unlink(Env::get('root_path') . 'public' . DIRECTORY_SEPARATOR . $s);
            Huimgs::destroy($id);
        }
        $res = [
            'code' => 200,
            'msg' => 'success'
        ];
        return $res;
    }

    public function img()
    {
        $data = request()->param()['data'];
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < 8; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        $lujing = './uploads/' . date('Ymd');
        if (!is_dir($lujing)) {
            mkdir(iconv("UTF-8", "GBK", $lujing), 0777, true);
        }
        $type = explode(',', $data)[0];
        $type = explode(';', $type)[0];
        $type = explode(':', $type)[1];
        $type = explode('/', $type)[1];
        $newFilePath = '/uploads/' . date('Ymd') . '/' . $str . '.' . $type;

        $dd = explode(',', $data)[1]; //得到post过来的二进制原始数据
        if (empty($dd)) {
            $data = file_get_contents("php://input");
        }
        $r = file_put_contents('.' . $newFilePath, base64_decode($dd));
        $temp = explode('/', $newFilePath);
        $pics_big =  '/uploads/' . $temp[2] . '/thumb_800_' . $temp[3];
        $pics_small = '/uploads/' .  $temp[2] .  '/thumb_400_' . $temp[3];
        $image = Image::open('.' . $newFilePath);
        $image->thumb(800, 800)->save('.' . $pics_big);
        $image->thumb(400, 400)->save('.' . $pics_small);
        $row = [
            'code' => 200,
            'big' => 'api.jy1980.com'.$pics_big,
            'small' => 'api.jy1980.com'.$pics_small
        ];
        return json($row);
    }

    public function upload_y($bid)
    {
        $flies = request()->param()['ybimglist'];
        $pics_data = [];
        foreach ($flies as $v) {
            $big=explode('-',$v)[0];
            $sma=explode('-',$v)[1];
            $row = [
                'bid' => $bid,
                'y_big' => $big,
                'y_small' => $sma
            ];
            $pics_data[] = $row;
        }
        $goodspic = new Yangimgs();
        $goodspic->saveAll($pics_data);
    }
    public function upload_p($bid)
    {
        $flies = request()->param()['ptimglist'];
        $pics_data = [];
        foreach ($flies as $v) {
            $big=explode('-',$v)[0];
            $sma=explode('-',$v)[1];
            $row = [
                'bid' => $bid,
                'p_big' => $big,
                'p_small' => $sma
            ];
            $pics_data[] = $row;
        }
        $goodspic = new Peiimgs();
        $goodspic->saveAll($pics_data);
    }
    public function upload_x($bid)
    {
        $flies = request()->param()['xgimglist'];
        $pics_data = [];
        foreach ($flies as $v) {
            $big=explode('-',$v)[0];
            $sma=explode('-',$v)[1];
            $row = [
                'bid' => $bid,
                'x_big' => $big,
                'x_small' => $sma
            ];
            $pics_data[] = $row;
        }
        $goodspic = new Xiaoimgs();
        $goodspic->saveAll($pics_data);
    }
    public function upload_h($bid)
    {
        $flies = request()->param()['hximglist'];
        $data = request()->param();
        $pics_data = [];
        if (is_array($flies)) {
            foreach ($flies as $k => $v) {
                $big=explode('-',$v)[0];
                $sma=explode('-',$v)[1];
                $row = [
                    'bid' => $bid,
                    'h_big' => $big,
                    'h_small' => $sma,
                    'content' => $data['ting'][$k],
                    'mian' => $data['area'][$k],
                    'jia' => $data['price'][$k]
                ];
                $pics_data[] = $row;
            }
        }

        $goodspic = new Huimgs();
        $goodspic->saveAll($pics_data);
    }
    public function upload_s($bid)
    {
        $flies = request()->param()['sjimglist'];
        $pics_data = [];
        foreach ($flies as $v) {
            $big=explode('-',$v)[0];
            $sma=explode('-',$v)[1];
            $row = [
                'bid' => $bid,
                's_big' => $big,
                's_small' => $sma
            ];
            $pics_data[] = $row;
        }
        $goodspic = new Shiimgs();
        $goodspic->saveAll($pics_data);
    }
    public function upload_j($bid)
    {
        $flies = request()->param()['jtimglist'];
        $pics_data = [];
        foreach ($flies as $v) {
            $big=explode('-',$v)[0];
            $sma=explode('-',$v)[1];
            $row = [
                'bid' => $bid,
                'j_big' => $big,
                'j_small' => $sma
            ];
            $pics_data[] = $row;
        }
        $goodspic = new Jiaoimgs();
        $goodspic->saveAll($pics_data);
    }


    public function tuitong()
    {
        $id = request()->param()['city'];
        $ids = Area::where('pid', $id)->column('area_name');
        $type = request()->param()['type'];
        $where = [
            ['cate_id', 'in', $ids],
            ['isdeng', 'eq', '是']
        ];
        if ($type == 1) {
            $t = strtotime('-12days');
            $data = Building::where($where)->whereTime('update_time', [date('Y-m-d', $t), date('Y-m-d', time())])->field("DATE_FORMAT(FROM_UNIXTIME(update_time),'%Y-%m-%d') as day,count(*) as total")
                ->group("DATE_FORMAT(FROM_UNIXTIME(update_time),'%Y-%m-%d')")->select();
        } else if ($type == 2) {
            $s = strtotime('-12week');
            $data = Db::name('building')->where($where)->whereTime('time', [date('Y-m-d', $s), date('Y-m-d', time())])->field("WEEK(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d')) as week,count(*) as total")
                ->group("WEEK(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d'))")->select();
        } else if ($type == 3) {
            $s = strtotime('-12month');
            $data = Db::name('building')->where($where)->whereTime('time', [date('Y-m-d', $s), date('Y-m-d', time())])->field("MONTH(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d')) as month,count(*) as total")
                ->group("MONTH(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d'))")->select();
        } else if ($type == 4) {
            $s = strtotime('-12quarter');
            $data = Db::name('building')->where($where)->whereTime('time', [date('Y-m-d', $s), date('Y-m-d', time())])->field("QUARTER(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d')) as quarter,count(*) as total")
                ->group("QUARTER(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d'))")->select();
        } else if ($type == 5) {
            $s = strtotime('-12year');
            $data = Db::name('building')->where($where)->whereTime('time', [date('Y-m-d', $s), date('Y-m-d', time())])->field("YEAR(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d')) as year,count(*) as total")
                ->group("YEAR(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d'))")->select();
        }
        $res = [
            'code' => 200,
            'data' => $data
        ];
        return json($res);
    }
    public function xiatong()
    {
        $id = request()->param()['city'];
        $ids = Area::where('pid', $id)->column('area_name');
        $type = request()->param()['type'];
        $where = [
            ['cate_id', 'in', $ids],
            ['isdeng', 'eq', '否']
        ];
        if ($type == 1) {
            $t = strtotime('-12days');
            $data = Building::where($where)->whereTime('update_time', [date('Y-m-d', $t), date('Y-m-d', time())])->field("DATE_FORMAT(FROM_UNIXTIME(update_time),'%Y-%m-%d') as day,count(*) as total")
                ->group("DATE_FORMAT(FROM_UNIXTIME(update_time),'%Y-%m-%d')")->select();
        } else if ($type == 2) {
            $s = strtotime('-12week');
            $data = Db::name('building')->where($where)->whereTime('time', [date('Y-m-d', $s), date('Y-m-d', time())])->field("WEEK(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d')) as week,count(*) as total")
                ->group("WEEK(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d'))")->select();
        } else if ($type == 3) {
            $s = strtotime('-12month');
            $data = Db::name('building')->where($where)->whereTime('time', [date('Y-m-d', $s), date('Y-m-d', time())])->field("MONTH(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d')) as month,count(*) as total")
                ->group("MONTH(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d'))")->select();
        } else if ($type == 4) {
            $s = strtotime('-12quarter');
            $data = Db::name('building')->where($where)->whereTime('time', [date('Y-m-d', $s), date('Y-m-d', time())])->field("QUARTER(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d')) as quarter,count(*) as total")
                ->group("QUARTER(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d'))")->select();
        } else if ($type == 5) {
            $s = strtotime('-12year');
            $data = Db::name('building')->where($where)->whereTime('time', [date('Y-m-d', $s), date('Y-m-d', time())])->field("YEAR(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d')) as year,count(*) as total")
                ->group("YEAR(DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d'))")->select();
        }
        $res = [
            'code' => 200,
            'data' => $data
        ];
        return json($res);
    }
    public function tong()
    { }
}
