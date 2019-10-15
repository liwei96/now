<?php
namespace app\api\controller;
use think\Controller;
use think\Request;
use think\Db;
use app\api\model\Staff;
use app\api\model\User;
use app\api\model\Dai;
use app\api\model\Gen;
use app\api\model\Record;
use app\api\model\Building;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


class Index
{
    public function list(){
        $list=Staff::where('guide','eq','3')->field("id,name")->select();
        $res=[
            'code'=>200,
            'list'=>$list
        ];
        return json($res);
    }
    function ids($data){
        static $dd=[];
        foreach($data as $v){
            $ss=Staff::where('pid',$v['id'])->select();
            if($ss){
                $this->ids($ss);
            }else{
                $dd[]=$v['id'];
            }
        }
        return $dd;
    }
    public function out()
    {
        $id=session('user.id');
        $ids=Staff::where('pid',$id)->select();
        $type=request()->param()['type'];
        $ids=$this->ids($ids);
       
        $shu=[];
        foreach($ids as $v){
            $lin=[];
            if($type==1){
                $jinke=User::where('s_id','eq',$v)->whereTime('create_time','today')->field("count(*) as total")->select();
                $zongdan=Dai::where('s_id','eq',$v)->whereTime('create_time','today')->field("count(*) as total")->select();
                $xin=Dai::where([['s_id','eq',$v],['label','eq','新客']])->whereTime('create_time','today')->field("count(*) as total")->select();
                $old=Dai::where([['s_id','eq',$v],['label','eq','老客']])->whereTime('create_time','today')->field("count(*) as total")->select();
                $gen=Gen::where('s_id','eq',$v)->whereTime('create_time','today')->field("count(*) as total")->select();
                $gong=Gen::where([['s_id','eq',$v],['label','eq','公客']])->whereTime('create_time','today')->field("count(*) as total")->select();
                $si=Gen::where([['s_id','eq',$v],['label','eq','私客']])->whereTime('create_time','today')->field("count(*) as total")->select();
                $data=Db::query("select * from (select * from erp_gen order by update_time desc) as data where s_id=$v group by u_id order by update_time desc");
                $time=time();
                $s1=0;
                $s2=0;
                $s3=0;
                $s4=0;
                foreach($data as $j){
                    if($time-$j['create_time']<(3600*24*3)){
                        $s1=$s1+1;
                    }else if($time-$j['create_time']<(3600*24*6) && $time-$j['create_time']>(3600*24*4)){
                        $s2=$s2+1;
                    }else if($time-$j['create_time']<(3600*24*11) && $time-$j['create_time']>(3600*24*7)){
                        $s3=$s3+1;
                    }else if($time-$j['create_time']>(3600*24*11)){
                        $s4=$s4+1;
                    }
                }
                $tao=Record::where('s_id','eq',$v)->whereTime('create_time','today')->count("*");
                $jin=Record::where('s_id','eq',$v)->whereTime('create_time','today')->field("sum(yeji) as num")->select();
                
            }else if($type==2){
                $jinke=User::where('s_id','eq',$v)->whereTime('create_time','week')->field("count(*) as total")->select();
                $zongdan=Dai::where('s_id','eq',$v)->whereTime('create_time','week')->field("count(*) as total")->select();
                $xin=Dai::where([['s_id','eq',$v],['label','eq','新客']])->whereTime('create_time','week')->field("count(*) as total")->select();
                $old=Dai::where([['s_id','eq',$v],['label','eq','老客']])->whereTime('create_time','week')->field("count(*) as total")->select();
                $gen=Gen::where('s_id','eq',$v)->whereTime('create_time','week')->field("count(*) as total")->select();
                $gong=Gen::where([['s_id','eq',$v],['label','eq','公客']])->whereTime('create_time','week')->field("count(*) as total")->select();
                $si=Gen::where([['s_id','eq',$v],['label','eq','私客']])->whereTime('create_time','week')->field("count(*) as total")->select();
                $data=Db::query("select * from (select * from erp_gen order by update_time desc) as data where s_id=$v group by u_id order by update_time desc");
                $time=time();
                $s1=0;
                $s2=0;
                $s3=0;
                $s4=0;
                foreach($data as $j){
                    if($time-$j['create_time']<(3600*24*3)){
                        $s1=$s1+1;
                    }else if($time-$j['create_time']<(3600*24*6) && $time-$j['create_time']>(3600*24*4)){
                        $s2=$s2+1;
                    }else if($time-$j['create_time']<(3600*24*11) && $time-$j['create_time']>(3600*24*7)){
                        $s3=$s3+1;
                    }else if($time-$j['create_time']>(3600*24*11)){
                        $s4=$s4+1;
                    }
                }
                $tao=Record::where('s_id','eq',$v)->whereTime('create_time','week')->count("*");
                $jin=Record::where('s_id','eq',$v)->whereTime('create_time','week')->field("sum(yeji) as num")->select();
            }else if($type==3){
                $jinke=User::where('s_id','eq',$v)->whereTime('create_time','month')->field("count(*) as total")->select();
                $zongdan=Dai::where('s_id','eq',$v)->whereTime('create_time','month')->field("count(*) as total")->select();
                $xin=Dai::where([['s_id','eq',$v],['label','eq','新客']])->whereTime('create_time','month')->field("count(*) as total")->select();
                $old=Dai::where([['s_id','eq',$v],['label','eq','老客']])->whereTime('create_time','month')->field("count(*) as total")->select();
                $gen=Gen::where('s_id','eq',$v)->whereTime('create_time','month')->field("count(*) as total")->select();
                $gong=Gen::where([['s_id','eq',$v],['label','eq','公客']])->whereTime('create_time','month')->field("count(*) as total")->select();
                $si=Gen::where([['s_id','eq',$v],['label','eq','私客']])->whereTime('create_time','month')->field("count(*) as total")->select();
                $data=Db::query("select * from (select * from erp_gen order by update_time desc) as data where s_id=$v group by u_id order by update_time desc");
                $time=time();
                $s1=0;
                $s2=0;
                $s3=0;
                $s4=0;
                foreach($data as $j){
                    if($time-$j['create_time']<(3600*24*3)){
                        $s1=$s1+1;
                    }else if($time-$j['create_time']<(3600*24*6) && $time-$j['create_time']>(3600*24*4)){
                        $s2=$s2+1;
                    }else if($time-$j['create_time']<(3600*24*11) && $time-$j['create_time']>(3600*24*7)){
                        $s3=$s3+1;
                    }else if($time-$j['create_time']>(3600*24*11)){
                        $s4=$s4+1;
                    }
                }
                $tao=Record::where('s_id','eq',$v)->whereTime('create_time','month')->count("*");
                $jin=Record::where('s_id','eq',$v)->whereTime('create_time','month')->field("sum(yeji) as num")->select();
            }else if($type==4){
                $jinke=User::where('s_id','eq',$v)->whereTime('create_time','-3 month')->field("count(*) as total")->select();
                $zongdan=Dai::where('s_id','eq',$v)->whereTime('create_time','-3 month')->field("count(*) as total")->select();
                $xin=Dai::where([['s_id','eq',$v],['label','eq','新客']])->whereTime('create_time','-3 month')->field("count(*) as total")->select();
                $old=Dai::where([['s_id','eq',$v],['label','eq','老客']])->whereTime('create_time','-3 month')->field("count(*) as total")->select();
                $gen=Gen::where('s_id','eq',$v)->whereTime('create_time','-3 month')->field("count(*) as total")->select();
                $gong=Gen::where([['s_id','eq',$v],['label','eq','公客']])->whereTime('create_time','-3 month')->field("count(*) as total")->select();
                $si=Gen::where([['s_id','eq',$v],['label','eq','私客']])->whereTime('create_time','-3 month')->field("count(*) as total")->select();
                $data=Db::query("select * from (select * from erp_gen order by update_time desc) as data where s_id=$v group by u_id order by update_time desc");
                $time=time();
                $s1=0;
                $s2=0;
                $s3=0;
                $s4=0;
                foreach($data as $j){
                    if($time-$j['create_time']<(3600*24*3)){
                        $s1=$s1+1;
                    }else if($time-$j['create_time']<(3600*24*6) && $time-$j['create_time']>(3600*24*4)){
                        $s2=$s2+1;
                    }else if($time-$j['create_time']<(3600*24*11) && $time-$j['create_time']>(3600*24*7)){
                        $s3=$s3+1;
                    }else if($time-$j['create_time']>(3600*24*11)){
                        $s4=$s4+1;
                    }
                }
                $tao=Record::where('s_id','eq',$v)->whereTime('create_time','-3 month')->count("*");
                $jin=Record::where('s_id','eq',$v)->whereTime('create_time','-3 month')->field("sum(yeji) as num")->select();
            }else if($type==5){
                $jinke=User::where('s_id','eq',$v)->whereTime('create_time','year')->field("count(*) as total")->select();
                $zongdan=Dai::where('s_id','eq',$v)->whereTime('create_time','year')->field("count(*) as total")->select();
                $xin=Dai::where([['s_id','eq',$v],['label','eq','新客']])->whereTime('create_time','year')->field("count(*) as total")->select();
                $old=Dai::where([['s_id','eq',$v],['label','eq','老客']])->whereTime('create_time','year')->field("count(*) as total")->select();
                $gen=Gen::where('s_id','eq',$v)->whereTime('create_time','year')->field("count(*) as total")->select();
                $gong=Gen::where([['s_id','eq',$v],['label','eq','公客']])->whereTime('create_time','year')->field("count(*) as total")->select();
                $si=Gen::where([['s_id','eq',$v],['label','eq','私客']])->whereTime('create_time','year')->field("count(*) as total")->select();
                $data=Db::query("select * from (select * from erp_gen order by update_time desc) as data where s_id=$v group by u_id order by update_time desc");
                $time=time();
                $s1=0;
                $s2=0;
                $s3=0;
                $s4=0;
                foreach($data as $j){
                    if($time-$j['create_time']<(3600*24*3)){
                        $s1=$s1+1;
                    }else if($time-$j['create_time']<(3600*24*6) && $time-$j['create_time']>(3600*24*4)){
                        $s2=$s2+1;
                    }else if($time-$j['create_time']<(3600*24*11) && $time-$j['create_time']>(3600*24*7)){
                        $s3=$s3+1;
                    }else if($time-$j['create_time']>(3600*24*11)){
                        $s4=$s4+1;
                    }
                }
                $tao=Record::where('s_id','eq',$v)->whereTime('create_time','year')->count("*");
                $jin=Record::where('s_id','eq',$v)->whereTime('create_time','year')->field("sum(yeji) as num")->select();
            }
            $zongdan=$zongdan[0]['total'];
            $gen=$gen[0]['total'];
            $gong=$gong[0]['total'];
            $si=$si[0]['total'];
            $xin=$xin[0]['total'];
            $jinke=$jinke[0]['total'];
            $old=$old[0]['total'];
            $jin=$jin[0]['num'];
            if($jinke==0){
                $xinli=0;
                $oldli=0;
                $zongli=0;
            }else{
                $xinli=round($xin/$jinke,2)*100;
                $oldli=round($old/$jinke,2)*100;
                $zongli=round($zongdan/$jinke,2)*100;
            }
            $lin[]=Staff::where('id',$v['id'])->column('name')[0];
            $lin[]=$jinke;
                $lin[]=$zongdan;
                $lin[]=$xin;
                $lin[]=$old;
                $lin[]=$gen;
                $lin[]=$si;
                $lin[]=$gong;
                $lin[]=$s1;
                $lin[]=$s2;
                $lin[]=$s3;
                $lin[]=$s4;
                $lin[]=$tao;
                $lin[]=$jin;
                $lin[]=$zongli;
                $lin[]=$xinli;
                $lin[]=$oldli;
            $shu[]=$lin;
        }
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        //设置sheet的名字  两种方法
        $sheet->setTitle('phpspreadsheet——demo');
        $spreadsheet->getActiveSheet()->setTitle('Hello');
        //设置第一行小标题
        $k = 1;
        $sheet->setCellValue('b'.$k, '进客量');
        $sheet->setCellValue('c'.$k, '带看量');
        $sheet->setCellValue('f'.$k, '跟进量');
        $sheet->setCellValue('i'.$k, '客户跟进周期');
        $sheet->setCellValue('m'.$k, '成交量');
        $sheet->setCellValue('o'.$k, '客户利用率');
        $sheet->setCellValue('c2','总带看量');
        $sheet->setCellValue('d2','新客');
        $sheet->setCellValue('e2','老客');
        $sheet->setCellValue('f2','跟进量');
        $sheet->setCellValue('g2','私客');
        $sheet->setCellValue('h2','公客');
        $sheet->setCellValue('i2','1-3');
        $sheet->setCellValue('j2','4-6');
        $sheet->setCellValue('k2','7-10');
        $sheet->setCellValue('l2','11+');
        $sheet->setCellValue('m2','套数');
        $sheet->setCellValue('n2','金额');
        $sheet->setCellValue('o2','总进客');
        $sheet->setCellValue('p2','新客');
        $sheet->setCellValue('q2','总利用率');
        
        
        //将A3到D4合并成一个单元格
        $spreadsheet->getActiveSheet()->mergeCells('c1:e1');
        $spreadsheet->getActiveSheet()->mergeCells('f1:h1');
        $spreadsheet->getActiveSheet()->mergeCells('i1:l1');
        $spreadsheet->getActiveSheet()->mergeCells('m1:n1');
        $spreadsheet->getActiveSheet()->mergeCells('o1:q1');
        // $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $styleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $sheet->getStyle('a1:t1')->applyFromArray($styleArray);
        $sheet->getStyle('a2:t2')->applyFromArray($styleArray);
        
        //循环赋值
        $row=3;
        foreach($shu as $k=>$v){
            $column=1;
            foreach($v as $l=>$n){
                $sheet->setCellValueByColumnAndRow($column,$row,$n);
                $column++;
            }
            $row++;
        }
        
        $file_name = date('Y-m-d', time()).rand(1000, 9999);
        //第一种保存方式
        $writer = new Xlsx($spreadsheet);
        // 保存的路径可自行设置
        $file_name = '../'.$file_name . ".xlsx";
        $writer->save($file_name);
        //第二种直接页面上显示下载
        $file_name = $file_name . ".xlsx";
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$file_name.'"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        //注意createWriter($spreadsheet, 'Xls') 第二个参数首字母必须大写
        $writer->save('php://output');
    }

    public function index(){
        $id=session('user.id');
        $ids=Staff::where('pid',$id)->select();
        $type=request()->param()['type'];
        $ids=$this->ids($ids);
        // 测试
        $ids[]=session('user.id');
        $shu=[];
        foreach($ids as $v){
            $lin=[];
            if($type==1){
                $jinke=User::where('s_id','eq',$v)->whereTime('create_time','today')->field("count(*) as total")->select();
                $zongdan=Dai::where('s_id','eq',$v)->whereTime('create_time','today')->field("count(*) as total")->select();
                $xin=Dai::where([['s_id','eq',$v],['label','eq','新客']])->whereTime('create_time','today')->field("count(*) as total")->select();
                $old=Dai::where([['s_id','eq',$v],['label','eq','老客']])->whereTime('create_time','today')->field("count(*) as total")->select();
                $gen=Gen::where('s_id','eq',$v)->whereTime('create_time','today')->field("count(*) as total")->select();
                $gong=Gen::where([['s_id','eq',$v],['label','eq','公客']])->whereTime('create_time','today')->field("count(*) as total")->select();
                $si=Gen::where([['s_id','eq',$v],['label','eq','私客']])->whereTime('create_time','today')->field("count(*) as total")->select();
                $data=Db::query("select * from (select * from erp_gen order by update_time desc) as data where s_id=$v group by u_id order by update_time desc");
                $time=time();
                $s1=0;
                $s2=0;
                $s3=0;
                $s4=0;
                foreach($data as $j){
                    if($time-$j['create_time']<(3600*24*3)){
                        $s1=$s1+1;
                    }else if($time-$j['create_time']<(3600*24*6) && $time-$j['create_time']>(3600*24*4)){
                        $s2=$s2+1;
                    }else if($time-$j['create_time']<(3600*24*11) && $time-$j['create_time']>(3600*24*7)){
                        $s3=$s3+1;
                    }else if($time-$j['create_time']>(3600*24*11)){
                        $s4=$s4+1;
                    }
                }
                $tao=Record::where('s_id','eq',$v)->whereTime('create_time','today')->count("*");
                $jin=Record::where('s_id','eq',$v)->whereTime('create_time','today')->field("sum(yeji) as num")->select();
                
            }else if($type==2){
                $jinke=User::where('s_id','eq',$v)->whereTime('create_time','week')->field("count(*) as total")->select();
                $zongdan=Dai::where('s_id','eq',$v)->whereTime('create_time','week')->field("count(*) as total")->select();
                $xin=Dai::where([['s_id','eq',$v],['label','eq','新客']])->whereTime('create_time','week')->field("count(*) as total")->select();
                $old=Dai::where([['s_id','eq',$v],['label','eq','老客']])->whereTime('create_time','week')->field("count(*) as total")->select();
                $gen=Gen::where('s_id','eq',$v)->whereTime('create_time','week')->field("count(*) as total")->select();
                $gong=Gen::where([['s_id','eq',$v],['label','eq','公客']])->whereTime('create_time','week')->field("count(*) as total")->select();
                $si=Gen::where([['s_id','eq',$v],['label','eq','私客']])->whereTime('create_time','week')->field("count(*) as total")->select();
                $data=Db::query("select * from (select * from erp_gen order by update_time desc) as data where s_id=$v group by u_id order by update_time desc");
                $time=time();
                $s1=0;
                $s2=0;
                $s3=0;
                $s4=0;
                foreach($data as $j){
                    if($time-$j['create_time']<(3600*24*3)){
                        $s1=$s1+1;
                    }else if($time-$j['create_time']<(3600*24*6) && $time-$j['create_time']>(3600*24*4)){
                        $s2=$s2+1;
                    }else if($time-$j['create_time']<(3600*24*11) && $time-$j['create_time']>(3600*24*7)){
                        $s3=$s3+1;
                    }else if($time-$j['create_time']>(3600*24*11)){
                        $s4=$s4+1;
                    }
                }
                $tao=Record::where('s_id','eq',$v)->whereTime('create_time','week')->count("*");
                $jin=Record::where('s_id','eq',$v)->whereTime('create_time','week')->field("sum(yeji) as num")->select();
            }else if($type==3){
                $jinke=User::where('s_id','eq',$v)->whereTime('create_time','month')->field("count(*) as total")->select();
                $zongdan=Dai::where('s_id','eq',$v)->whereTime('create_time','month')->field("count(*) as total")->select();
                $xin=Dai::where([['s_id','eq',$v],['label','eq','新客']])->whereTime('create_time','month')->field("count(*) as total")->select();
                $old=Dai::where([['s_id','eq',$v],['label','eq','老客']])->whereTime('create_time','month')->field("count(*) as total")->select();
                $gen=Gen::where('s_id','eq',$v)->whereTime('create_time','month')->field("count(*) as total")->select();
                $gong=Gen::where([['s_id','eq',$v],['label','eq','公客']])->whereTime('create_time','month')->field("count(*) as total")->select();
                $si=Gen::where([['s_id','eq',$v],['label','eq','私客']])->whereTime('create_time','month')->field("count(*) as total")->select();
                $data=Db::query("select * from (select * from erp_gen order by update_time desc) as data where s_id=$v group by u_id order by update_time desc");
                $time=time();
                $s1=0;
                $s2=0;
                $s3=0;
                $s4=0;
                foreach($data as $j){
                    if($time-$j['create_time']<(3600*24*3)){
                        $s1=$s1+1;
                    }else if($time-$j['create_time']<(3600*24*6) && $time-$j['create_time']>(3600*24*4)){
                        $s2=$s2+1;
                    }else if($time-$j['create_time']<(3600*24*11) && $time-$j['create_time']>(3600*24*7)){
                        $s3=$s3+1;
                    }else if($time-$j['create_time']>(3600*24*11)){
                        $s4=$s4+1;
                    }
                }
                $tao=Record::where('s_id','eq',$v)->whereTime('create_time','month')->count("*");
                $jin=Record::where('s_id','eq',$v)->whereTime('create_time','month')->field("sum(yeji) as num")->select();
            }else if($type==4){
                $jinke=User::where('s_id','eq',$v)->whereTime('create_time','-3 month')->field("count(*) as total")->select();
                $zongdan=Dai::where('s_id','eq',$v)->whereTime('create_time','-3 month')->field("count(*) as total")->select();
                $xin=Dai::where([['s_id','eq',$v],['label','eq','新客']])->whereTime('create_time','-3 month')->field("count(*) as total")->select();
                $old=Dai::where([['s_id','eq',$v],['label','eq','老客']])->whereTime('create_time','-3 month')->field("count(*) as total")->select();
                $gen=Gen::where('s_id','eq',$v)->whereTime('create_time','-3 month')->field("count(*) as total")->select();
                $gong=Gen::where([['s_id','eq',$v],['label','eq','公客']])->whereTime('create_time','-3 month')->field("count(*) as total")->select();
                $si=Gen::where([['s_id','eq',$v],['label','eq','私客']])->whereTime('create_time','-3 month')->field("count(*) as total")->select();
                $data=Db::query("select * from (select * from erp_gen order by update_time desc) as data where s_id=$v group by u_id order by update_time desc");
                $time=time();
                $s1=0;
                $s2=0;
                $s3=0;
                $s4=0;
                foreach($data as $j){
                    if($time-$j['create_time']<(3600*24*3)){
                        $s1=$s1+1;
                    }else if($time-$j['create_time']<(3600*24*6) && $time-$j['create_time']>(3600*24*4)){
                        $s2=$s2+1;
                    }else if($time-$j['create_time']<(3600*24*11) && $time-$j['create_time']>(3600*24*7)){
                        $s3=$s3+1;
                    }else if($time-$j['create_time']>(3600*24*11)){
                        $s4=$s4+1;
                    }
                }
                $tao=Record::where('s_id','eq',$v)->whereTime('create_time','-3 month')->count("*");
                $jin=Record::where('s_id','eq',$v)->whereTime('create_time','-3 month')->field("sum(yeji) as num")->select();
            }else if($type==5){
                $jinke=User::where('s_id','eq',$v)->whereTime('create_time','year')->field("count(*) as total")->select();
                $zongdan=Dai::where('s_id','eq',$v)->whereTime('create_time','year')->field("count(*) as total")->select();
                $xin=Dai::where([['s_id','eq',$v],['label','eq','新客']])->whereTime('create_time','year')->field("count(*) as total")->select();
                $old=Dai::where([['s_id','eq',$v],['label','eq','老客']])->whereTime('create_time','year')->field("count(*) as total")->select();
                $gen=Gen::where('s_id','eq',$v)->whereTime('create_time','year')->field("count(*) as total")->select();
                $gong=Gen::where([['s_id','eq',$v],['label','eq','公客']])->whereTime('create_time','year')->field("count(*) as total")->select();
                $si=Gen::where([['s_id','eq',$v],['label','eq','私客']])->whereTime('create_time','year')->field("count(*) as total")->select();
                $data=Db::query("select * from (select * from erp_gen order by update_time desc) as data where s_id=$v group by u_id order by update_time desc");
                $time=time();
                $s1=0;
                $s2=0;
                $s3=0;
                $s4=0;
                foreach($data as $j){
                    if($time-$j['create_time']<(3600*24*3)){
                        $s1=$s1+1;
                    }else if($time-$j['create_time']<(3600*24*6) && $time-$j['create_time']>(3600*24*4)){
                        $s2=$s2+1;
                    }else if($time-$j['create_time']<(3600*24*11) && $time-$j['create_time']>(3600*24*7)){
                        $s3=$s3+1;
                    }else if($time-$j['create_time']>(3600*24*11)){
                        $s4=$s4+1;
                    }
                }
                $tao=Record::where('s_id','eq',$v)->whereTime('create_time','year')->count("*");
                $jin=Record::where('s_id','eq',$v)->whereTime('create_time','year')->field("sum(yeji) as num")->select();
            }
            $zongdan=$zongdan[0]['total'];
            $gen=$gen[0]['total'];
            $gong=$gong[0]['total'];
            $si=$si[0]['total'];
            $xin=$xin[0]['total'];
            $jinke=$jinke[0]['total'];
            $old=$old[0]['total'];
            $jin=$jin[0]['num'];
            if($jinke==0){
                $xinli=0;
                $oldli=0;
                $zongli=0;
            }else{
                $xinli=round($xin/$jinke,2)*100;
                $oldli=round($old/$jinke,2)*100;
                $zongli=round($zongdan/$jinke,2)*100;
            }
            $lin[]=Staff::where('id',$v)->column('name')[0];
            $lin[]=$jinke;
                $lin[]=$zongdan;
                $lin[]=$xin;
                $lin[]=$old;
                $lin[]=$gen;
                $lin[]=$si;
                $lin[]=$gong;
                $lin[]=$s1;
                $lin[]=$s2;
                $lin[]=$s3;
                $lin[]=$s4;
                $lin[]=$tao;
                $lin[]=$jin;
                $lin[]=$zongli;
                $lin[]=$xinli;
                $lin[]=$oldli;
            $shu[]=$lin;
        }
        return json(['code'=>200,'data'=>$shu]);
    }
    
    public function sou($id){
        $ids=Staff::where('pid','eq',$id)->select();
        $type=request()->param()['type'];
        $ids=$this->ids($ids);
        // 测试
        $ids[]=session('user.id');
        $shu=[];
        foreach($ids as $v){
            $lin=[];
            if($type==1){
                $jinke=User::where('s_id','eq',$v)->whereTime('create_time','today')->field("count(*) as total")->select();
                $zongdan=Dai::where('s_id','eq',$v)->whereTime('create_time','today')->field("count(*) as total")->select();
                $xin=Dai::where([['s_id','eq',$v],['label','eq','新客']])->whereTime('create_time','today')->field("count(*) as total")->select();
                $old=Dai::where([['s_id','eq',$v],['label','eq','老客']])->whereTime('create_time','today')->field("count(*) as total")->select();
                $gen=Gen::where('s_id','eq',$v)->whereTime('create_time','today')->field("count(*) as total")->select();
                $gong=Gen::where([['s_id','eq',$v],['label','eq','公客']])->whereTime('create_time','today')->field("count(*) as total")->select();
                $si=Gen::where([['s_id','eq',$v],['label','eq','私客']])->whereTime('create_time','today')->field("count(*) as total")->select();
                $data=Db::query("select * from (select * from erp_gen order by update_time desc) as data where s_id=$v group by u_id order by update_time desc");
                $time=time();
                $s1=0;
                $s2=0;
                $s3=0;
                $s4=0;
                foreach($data as $j){
                    if($time-$j['create_time']<(3600*24*3)){
                        $s1=$s1+1;
                    }else if($time-$j['create_time']<(3600*24*6) && $time-$j['create_time']>(3600*24*4)){
                        $s2=$s2+1;
                    }else if($time-$j['create_time']<(3600*24*11) && $time-$j['create_time']>(3600*24*7)){
                        $s3=$s3+1;
                    }else if($time-$j['create_time']>(3600*24*11)){
                        $s4=$s4+1;
                    }
                }
                $tao=Record::where('s_id','eq',$v)->whereTime('create_time','today')->count("*");
                $jin=Record::where('s_id','eq',$v)->whereTime('create_time','today')->field("sum(yeji) as num")->select();
                
            }else if($type==2){
                $jinke=User::where('s_id','eq',$v)->whereTime('create_time','week')->field("count(*) as total")->select();
                $zongdan=Dai::where('s_id','eq',$v)->whereTime('create_time','week')->field("count(*) as total")->select();
                $xin=Dai::where([['s_id','eq',$v],['label','eq','新客']])->whereTime('create_time','week')->field("count(*) as total")->select();
                $old=Dai::where([['s_id','eq',$v],['label','eq','老客']])->whereTime('create_time','week')->field("count(*) as total")->select();
                $gen=Gen::where('s_id','eq',$v)->whereTime('create_time','week')->field("count(*) as total")->select();
                $gong=Gen::where([['s_id','eq',$v],['label','eq','公客']])->whereTime('create_time','week')->field("count(*) as total")->select();
                $si=Gen::where([['s_id','eq',$v],['label','eq','私客']])->whereTime('create_time','week')->field("count(*) as total")->select();
                $data=Db::query("select * from (select * from erp_gen order by update_time desc) as data where s_id=$v group by u_id order by update_time desc");
                $time=time();
                $s1=0;
                $s2=0;
                $s3=0;
                $s4=0;
                foreach($data as $j){
                    if($time-$j['create_time']<(3600*24*3)){
                        $s1=$s1+1;
                    }else if($time-$j['create_time']<(3600*24*6) && $time-$j['create_time']>(3600*24*4)){
                        $s2=$s2+1;
                    }else if($time-$j['create_time']<(3600*24*11) && $time-$j['create_time']>(3600*24*7)){
                        $s3=$s3+1;
                    }else if($time-$j['create_time']>(3600*24*11)){
                        $s4=$s4+1;
                    }
                }
                $tao=Record::where('s_id','eq',$v)->whereTime('create_time','week')->count("*");
                $jin=Record::where('s_id','eq',$v)->whereTime('create_time','week')->field("sum(yeji) as num")->select();
            }else if($type==3){
                $jinke=User::where('s_id','eq',$v)->whereTime('create_time','month')->field("count(*) as total")->select();
                $zongdan=Dai::where('s_id','eq',$v)->whereTime('create_time','month')->field("count(*) as total")->select();
                $xin=Dai::where([['s_id','eq',$v],['label','eq','新客']])->whereTime('create_time','month')->field("count(*) as total")->select();
                $old=Dai::where([['s_id','eq',$v],['label','eq','老客']])->whereTime('create_time','month')->field("count(*) as total")->select();
                $gen=Gen::where('s_id','eq',$v)->whereTime('create_time','month')->field("count(*) as total")->select();
                $gong=Gen::where([['s_id','eq',$v],['label','eq','公客']])->whereTime('create_time','month')->field("count(*) as total")->select();
                $si=Gen::where([['s_id','eq',$v],['label','eq','私客']])->whereTime('create_time','month')->field("count(*) as total")->select();
                $data=Db::query("select * from (select * from erp_gen order by update_time desc) as data where s_id=$v group by u_id order by update_time desc");
                $time=time();
                $s1=0;
                $s2=0;
                $s3=0;
                $s4=0;
                foreach($data as $j){
                    if($time-$j['create_time']<(3600*24*3)){
                        $s1=$s1+1;
                    }else if($time-$j['create_time']<(3600*24*6) && $time-$j['create_time']>(3600*24*4)){
                        $s2=$s2+1;
                    }else if($time-$j['create_time']<(3600*24*11) && $time-$j['create_time']>(3600*24*7)){
                        $s3=$s3+1;
                    }else if($time-$j['create_time']>(3600*24*11)){
                        $s4=$s4+1;
                    }
                }
                $tao=Record::where('s_id','eq',$v)->whereTime('create_time','month')->count("*");
                $jin=Record::where('s_id','eq',$v)->whereTime('create_time','month')->field("sum(yeji) as num")->select();
            }else if($type==4){
                $jinke=User::where('s_id','eq',$v)->whereTime('create_time','-3 month')->field("count(*) as total")->select();
                $zongdan=Dai::where('s_id','eq',$v)->whereTime('create_time','-3 month')->field("count(*) as total")->select();
                $xin=Dai::where([['s_id','eq',$v],['label','eq','新客']])->whereTime('create_time','-3 month')->field("count(*) as total")->select();
                $old=Dai::where([['s_id','eq',$v],['label','eq','老客']])->whereTime('create_time','-3 month')->field("count(*) as total")->select();
                $gen=Gen::where('s_id','eq',$v)->whereTime('create_time','-3 month')->field("count(*) as total")->select();
                $gong=Gen::where([['s_id','eq',$v],['label','eq','公客']])->whereTime('create_time','-3 month')->field("count(*) as total")->select();
                $si=Gen::where([['s_id','eq',$v],['label','eq','私客']])->whereTime('create_time','-3 month')->field("count(*) as total")->select();
                $data=Db::query("select * from (select * from erp_gen order by update_time desc) as data where s_id=$v group by u_id order by update_time desc");
                $time=time();
                $s1=0;
                $s2=0;
                $s3=0;
                $s4=0;
                foreach($data as $j){
                    if($time-$j['create_time']<(3600*24*3)){
                        $s1=$s1+1;
                    }else if($time-$j['create_time']<(3600*24*6) && $time-$j['create_time']>(3600*24*4)){
                        $s2=$s2+1;
                    }else if($time-$j['create_time']<(3600*24*11) && $time-$j['create_time']>(3600*24*7)){
                        $s3=$s3+1;
                    }else if($time-$j['create_time']>(3600*24*11)){
                        $s4=$s4+1;
                    }
                }
                $tao=Record::where('s_id','eq',$v)->whereTime('create_time','-3 month')->count("*");
                $jin=Record::where('s_id','eq',$v)->whereTime('create_time','-3 month')->field("sum(yeji) as num")->select();
            }else if($type==5){
                $jinke=User::where('s_id','eq',$v)->whereTime('create_time','year')->field("count(*) as total")->select();
                $zongdan=Dai::where('s_id','eq',$v)->whereTime('create_time','year')->field("count(*) as total")->select();
                $xin=Dai::where([['s_id','eq',$v],['label','eq','新客']])->whereTime('create_time','year')->field("count(*) as total")->select();
                $old=Dai::where([['s_id','eq',$v],['label','eq','老客']])->whereTime('create_time','year')->field("count(*) as total")->select();
                $gen=Gen::where('s_id','eq',$v)->whereTime('create_time','year')->field("count(*) as total")->select();
                $gong=Gen::where([['s_id','eq',$v],['label','eq','公客']])->whereTime('create_time','year')->field("count(*) as total")->select();
                $si=Gen::where([['s_id','eq',$v],['label','eq','私客']])->whereTime('create_time','year')->field("count(*) as total")->select();
                $data=Db::query("select * from (select * from erp_gen order by update_time desc) as data where s_id=$v group by u_id order by update_time desc");
                $time=time();
                $s1=0;
                $s2=0;
                $s3=0;
                $s4=0;
                foreach($data as $j){
                    if($time-$j['create_time']<(3600*24*3)){
                        $s1=$s1+1;
                    }else if($time-$j['create_time']<(3600*24*6) && $time-$j['create_time']>(3600*24*4)){
                        $s2=$s2+1;
                    }else if($time-$j['create_time']<(3600*24*11) && $time-$j['create_time']>(3600*24*7)){
                        $s3=$s3+1;
                    }else if($time-$j['create_time']>(3600*24*11)){
                        $s4=$s4+1;
                    }
                }
                $tao=Record::where('s_id','eq',$v)->whereTime('create_time','year')->count("*");
                $jin=Record::where('s_id','eq',$v)->whereTime('create_time','year')->field("sum(yeji) as num")->select();
            }
            $zongdan=$zongdan[0]['total'];
            $gen=$gen[0]['total'];
            $gong=$gong[0]['total'];
            $si=$si[0]['total'];
            $xin=$xin[0]['total'];
            $jinke=$jinke[0]['total'];
            $old=$old[0]['total'];
            $jin=$jin[0]['num'];
            if($jinke==0){
                $xinli=0;
                $oldli=0;
                $zongli=0;
            }else{
                $xinli=round($xin/$jinke,2)*100;
                $oldli=round($old/$jinke,2)*100;
                $zongli=round($zongdan/$jinke,2)*100;
            }
            $lin[]=Staff::where('id',$v)->column('name')[0];
            $lin[]=$jinke;
                $lin[]=$zongdan;
                $lin[]=$xin;
                $lin[]=$old;
                $lin[]=$gen;
                $lin[]=$si;
                $lin[]=$gong;
                $lin[]=$s1;
                $lin[]=$s2;
                $lin[]=$s3;
                $lin[]=$s4;
                $lin[]=$tao;
                $lin[]=$jin;
                $lin[]=$zongli;
                $lin[]=$xinli;
                $lin[]=$oldli;
            $shu[]=$lin;
        }
        return json(['code'=>200,'data'=>$shu]);
    }
}
