<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\ClassModel;
use App\Tools\RedisTools;
use App\User;


class ClassController extends Controller
{
    private $expire_time = 10;
    private $classTable;
    private $userTable;

    public function __construct(ClassModel $classTable, User $userTable) {
        $this->classTable = $classTable;
        $this->userTable = $userTable;
    }

    public function getAllClasses() {
        $classesRedisKey = env('REDIS_PREFIX', 'CourseSystem_') . 'Classes';
        $cache = RedisTools::getValueByKey($classesRedisKey);
        if($cache != null) {
            return response([
                'code'  => 0,
                'data'  => json_encode($cache)
            ]);
        }

        $data = [];
        $temp = $this->userTable->get();
        $users = [];
        foreach ($temp as $user) {
            $users[$user->id] = $user->name;
        }
        $classes = $this->classTable->get();
        foreach ($classes as $class) {
            $data []= [
                'id'            => $class->id,
                'name'          => $class->name,
                'users_name'    => $users[$class->users_id]
            ];
        }

        RedisTools::setKeyWillExpired($classesRedisKey, json_encode($data), $this->expire_time);

        return response([
            'code'  => 0,
            'data'  => $data
        ]);
    }

    public function getByUsersId(Request $request) {
        $classes = $this->classTable->where('users_id', $request->users_id)->get();

        $data = [];
        foreach ($classes as $class) {
            $data []= [
                'id'    => $class->id,
                'name'  => $class->name,
            ];
        }

        return response([
            'code'  => 0,
            'data'  => $data
        ]);
    }
}
