<?php
/**
 * Created by PhpStorm.
 * User: elatis
 * Date: 2019/4/20
 * Time: 15:04
 */

namespace App\Tools;

use App\ClassModel;


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
}