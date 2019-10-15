<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Export extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
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

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
        $str=file_get_contents('./1.jpg');
        $ss=base64_encode($str);
        $ss='';
        $ss=explode(',',$ss);
        // $data=base64_decode($ss);
        // file_put_contents('./ss.jpg',$data);
        dump($ss);
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
