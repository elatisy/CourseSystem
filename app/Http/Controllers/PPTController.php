<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\PPTUrl;
use App\UserRole;
use App\ClassModel;
use App\Tools\ClassTools;


class PPTController extends Controller
{
    private $PPTUrlTable;
    private $classTable;

    public function __construct(PPTUrl $PPTUrlTable, ClassModel $classTable) {
        $this->PPTUrlTable = $PPTUrlTable;
        $this->classTable = $classTable;
    }

    public function uploadPPT(Request $request) {
        $this->validate($request,[
            'name'          => 'required',
            'PPTUrl'        => 'required',
            'class_name'    => 'required'
        ]);

        $userRoleTable = new UserRole();
        $rolesId = $userRoleTable->where('users_id', $request->users_id)->first()->roles_id;

        if($rolesId != 1) {
            return response([
                'code'      => 2001,
                'message'   => '只有教师才能上传PPT'
            ]);
        }

        $classes_id = ClassTools::setClass($request->class_name, $request->users_id);

        $this->PPTUrlTable->insert([
            'name'          => $request->name,
            'url'           => $request->PPTUrl,
            'classes_id'    => $classes_id,
            'users_id'      => $request->users_id
        ]);

        return response([
            'code'  => 0
        ]);
    }

    public function getAllPPTs(Request $request) {
        $PPTs = $this->PPTUrlTable->where('users_id', $request->users_id)->get();
        $classes = $this->classTable->where('users_id', $request->users_id)->get();

        $temp = [];
        foreach ($classes as $class) {
            $temp[$class->id] = $class->name;
        }
        $classes = $temp;

        $data =[];
        foreach ($PPTs as $PPT) {
            $data []= [
                'id'            => $PPT->id,
                'name'          => $PPT->name,
                'class_name'    => $classes[$PPT->classes_id],
                'url'           => $PPT->url
            ];
        }

        return response([
            'code'  => 0,
            'data'  => $data
        ]);
    }

    public function getClasses(Request $request) {
        $classes = $this->classTable->where('users_id', $request->users_id)->get();

        $data = [];
        foreach ($classes as $class) {
            $data []= $class->name;
        }

        return response([
            'code'  => 0,
            'data'  => $data
        ]);
    }

    public function getPPTsByClassName(string $name) {
        $class = $this->classTable->where('name', $name)->first();
        if($class == null) {
            return response([
                'code'  => 0,
                'data'  => []
            ]);
        }

        $PPTs = $this->PPTUrlTable->where('classes_id', $class->id)->get();
        $data = [];
        foreach ($PPTs as $PPT) {
            $data []= [
                'id'            => $PPT->id,
                'name'          => $PPT->name,
                'class_name'    => $name,
                'url'           => $PPT->url
            ];
        }

        return response([
            'code'  => 0,
            'data'  => $data
        ]);
    }

    public function deletePPT(Request $request) {
        $this->validate($request, [
            'PPT_id'    => 'required'
        ]);

        $PPT = $this->PPTUrlTable->where('id', $request->PPT_id)->first();
        if($PPT == null) {
            return response([
                'code'  => 0
            ]);
        }

        if($PPT->users_id != $request->users_id) {
            return response([
                'code'      => 2002,
                'message'   => '只能删除对应账号上传的PPT'
            ]);
        }

        $this->PPTUrlTable->where('id', $request->PPT_id)->delete();

        return response([
            'code'  => 0
        ]);
    }
}
