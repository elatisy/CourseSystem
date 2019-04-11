<?php
/**
 * Created by PhpStorm.
 * User: elati
 * Date: 2018/7/25
 * Time: 17:05
 */

namespace App\Export;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ExportExcel implements FromCollection
{
    use Exportable;
    private $data = [];
    public function collection()
    {
        // TODO: Implement collection() method.
        return (new Collection($this->data));
    }

    public function getDataFromTable(string $table, Array $title, bool $has_header){
        if($has_header){
            $temp = [];
            foreach ($title as $key => $value){
                $temp []= $key;
            }
            $this->data []= $temp;
        }else{
            $this->data []= $title;
        }

        $rows = DB::table($table)->get();
        foreach ($rows as $row){
            $temp = [];
            foreach ($title as $key){
                $temp []= $row->$key;
            }
            $this->data []= $temp;
        }
    }

    /**
     * example,写在控制器里
     */
//    public function getExcel(Excel $excel, ExportExcel $exportExcel)
//    {
//        $exportExcel->getDataFromTable('newacmers', [
//            '姓名'            => 'name',
//            '性别'            => 'gender',
//            '学号'            => 'studentNumber',
//            'QQ号'            => 'qqNumber',
//            '学院|专业'       => 'college|major',
//            '电话号码'         => 'phoneNumber'
//        ], true);
//
//        return $exportExcel->download('报名信息表.xlsx');
//    }
}