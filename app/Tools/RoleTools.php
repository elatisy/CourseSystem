<?php
/**
 * Created by PhpStorm.
 * User: elatis
 * Date: 2019/4/20
 * Time: 14:51
 */

namespace App\Tools;

use App\Role;
use App\UserRole;

class RoleTools
{
    public static function getRolesIdByName(string $name) {
        $roleTable = new Role();
        return $roleTable->where('name', $name)->first()->id;
    }

    public static function getOnesRolesIdByUsersId(int $users_id) {
        $userRoleTable = new UserRole();
        return $userRoleTable->where('users_id', $users_id)->first()->roles_id;
    }
}