<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Tools\ClassTools;
use App\Tools\RoleTools;
use App\Homework;
use App\ClassModel;


class HomeworkController extends Controller
{
    private $homeworkTable;
    private $classesTable;

    public function __construct(Homework $homeworkTable, ClassModel $classesTable) {
        $this->homeworkTable = $homeworkTable;
        $this->classesTable = $classesTable;
    }

    public function createHomework(Request $request) {
        $this->validate($request, [
            'class_name'    => 'required',
            'data'          => 'required'
        ]);

        if(RoleTools::getOnesRolesIdByUsersId($request->users_id) != 1) {
            return response([
                'code'      => 2002,
                'message'   => '只有教师才能上传作业'
            ]);
        }

        $classes_id = ClassTools::setClass($request->class_name, $request->users_id);

        $check = $this->homeworkTable->where('classes_id', $classes_id)->first();
        if($check != null) {
            $this->homeworkTable->where('classes_id', $classes_id)->update([
                'users_id'  => $request->users_id,
                'data'      => json_encode($request->data)
            ]);
        } else {
            $this->homeworkTable->insert([
                'classes_id'    => $classes_id,
                'users_id'      => $request->users_id,
                'data'          => json_encode($request->data)
            ]);
        }

        return response([
            'code'  => 0
        ]);
    }

    public function getHomeworkByClassName(string $name) {
        $class = $this->classesTable->where('name', $name)->first();
        if($class == null) {
            return response([
                'code'  => 0,
                'data'  => []
            ]);
        }

        $homework = $this->homeworkTable->where('classes_id', $class->id)->first();
        return response([
            'code'  => 0,
            'data'  => json_decode($homework->data)
        ]);
    }

    public function updateHomework(Request $request) {
        $this->validate($request, [
            'class_name'    => 'required',
            'data'          => 'required'
        ]);

        if(RoleTools::getOnesRolesIdByUsersId($request->users_id) != 1) {
            return response([
                'code'      => 2002,
                'message'   => '只有教师才能上传作业'
            ]);
        }

        $classes_id = ClassTools::setClass($request->class_name, $request->users_id);
        $this->homeworkTable->where('classes_id', $classes_id)->update([
            'data'          => json_encode($request->data)
        ]);

        return response([
            'code'  => 0
        ]);
    }
}
