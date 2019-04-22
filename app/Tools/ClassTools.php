<?php
/**
 * Created by PhpStorm.
 * User: elatis
 * Date: 2019/4/20
 * Time: 15:04
 */

namespace App\Tools;

use App\ClassModel;
use App\User;


class ClassTools
{
    public static function setClass(string $className, int $users_id) {
        $classesTable = new ClassModel();
        $check = $classesTable->where('name', $className)->first();
        if($check != null) {
            return $check->id;
        }

        $classesTable->insert([
            'name'      => $className,
            'users_id'  => $users_id
        ]);

        return self::setClass($className, $users_id);
    }

    public static function getAllClasses() {
        $data = [];
        $userTable = new User();
        $temp = $userTable->get();
        $users = [];
        foreach ($temp as $user) {
            $users[$user->id] = $user->name;
        }

        $classTable = new ClassModel();
        $classes = $classTable->orderBy('id', 'desc')->get();
        foreach ($classes as $class) {
            $data []= [
                'id'            => $class->id,
                'name'          => $class->name,
                'users_name'    => $users[$class->users_id]
            ];
        }

        return $data;
    }
}