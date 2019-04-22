<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Tools\RedisTools;
use App\Tools\ClassTools;
use App\Tools\RoleTools;
use App\Homework;
use App\ClassModel;
use App\HomeworkData;
use App\User;


class HomeworkController extends Controller
{
    private $homeworkTable;
    private $classesTable;
    private $homeworkDataTable;

    private $getAllSelfHomeworkExpiredTime = 10;
    private $redisPrefix;

    public function __construct(Homework $homeworkTable, ClassModel $classesTable, HomeworkData $homeworkDataTable) {
        $this->homeworkTable = $homeworkTable;
        $this->classesTable = $classesTable;
        $this->homeworkDataTable = $homeworkDataTable;

        $this->redisPrefix = env('REDIS_PREFIX', 'CourseSystem_') . 'Homework_';
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

    public function getHomeworkByClassId(int $id) {
        $class = $this->classesTable->where('id', $id)->first();
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

    public function handInHomework(Request $request) {
        $this->validate($request, [
            'classes_id'    => 'required',
            'data'          => 'required'
        ]);

        $check = $this->homeworkDataTable
            ->where([
                ['users_id' , $request->users_id],
                ['classes_id', $request->classes_id]
            ])
            ->first();

        if($check != null) {
            $this->homeworkDataTable
                ->where([
                    ['users_id' , $request->users_id],
                    ['classes_id', $request->classes_id]
                ])
                ->update([
                    'data'  => json_encode($request->data)
                ]);
        } else {
            $this->homeworkDataTable->insert([
                'users_id'      => $request->users_id,
                'classes_id'    => $request->classes_id,
                'data'          => json_encode($request->data)
            ]);
        }

        return response([
            'code'  => 0
        ]);
    }

    public function getAllSelfHomeworkRecords(Request $request) {
        $redisKey = $this->redisPrefix . 'record' . strval($request->users_id);

        $cache = RedisTools::getValueByKey($redisKey);
        if($cache != null) {
            return response([
                'code'  => 0,
                'data'  => json_decode($cache)
            ]);
        }

        $classes = ClassTools::getAllClasses();

        $data = [];
        $homeworkDatas = $this->homeworkDataTable->where('users_id', $request->users_id)->orderBy('desc')->get();
        foreach ($homeworkDatas as $homeworkData) {
            $data []= [
                'classes_id'    => $homeworkData->classes_id,
                'classes_name'  => $classes[$homeworkData->classes_id]
            ];
        }

        RedisTools::setKeyWillExpired($redisKey, jeon_encode($data), $this->getAllSelfHomeworkExpiredTime);

        return response([
            'code'  => 0,
            'data'  => $data
        ]);
    }

    public function getHomeworkDataByClassesIdAndUsersId(Request $request) {
        $this->validate($request, [
            'homework_users_id' => 'required',
            'classes_id'        => 'required'
        ]);

        if(RoleTools::getOnesRolesIdByUsersId($request->users_id) != 1) {
            return response([
                'code'      => 3001,
                'message'   => '你不能查看此作业'
            ]);
        }

        $homeworkData = $this->homeworkDataTable
                            ->where([
                                ['users_id' , $request->homework_users_id],
                                ['classes_id', $request->classes_id]
                            ])
                            ->first();

        if($homeworkData == null) {
            return response([
                'code'      => 3002,
                'message'   => '该学生未提交本节课作业'
            ]);
        }

        return response([
            'code'  => 0,
            'data'  => json_decode($homeworkData->data)
        ]);
    }

    public function getHomeworkRecordsByClassesId(int $id) {
        $redisKey = $this->redisPrefix . 'classes_record' . strval($id);

        $cache = RedisTools::getValueByKey($redisKey);
        if($cache != null) {
            return response([
                'code'  => 0,
                'data'  => json_decode($cache)
            ]);
        }

        $userTable = new User();
        $temp = $userTable->get();
        $users = [];
        foreach ($temp as $user) {
            $users[$user->id] = $user->name;
        }

        $data = [];
        $homeworkDatas = $this->homeworkDataTable->where('classes_id', $id)->get();
        foreach ($homeworkDatas as $homeworkData) {
            $data []= [
                'users_id'      => $homeworkData->users_id,
                'users_name'    => $users[$homeworkData->users_id],
            ];
        }

        RedisTools::setKeyWillExpired($redisKey, json_encode($data), $this->getAllSelfHomeworkExpiredTime);

        return response([
            'code'  => 0,
            'data'  => $data
        ]);
    }
}
