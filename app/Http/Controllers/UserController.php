<?php

namespace App\Http\Controllers;

use App\Tools\TokenTools;
use Illuminate\Http\Request;
use App\User;
use App\UserRole;
use App\Tools\Encrypt;
use Carbon\Carbon;
use App\Role;

class UserController extends Controller
{
    private $tokenExpireTime = 259200;

    private $userTable;
    private $roleTable;

    public function __construct(User $userTable, Role $roleTable) {
        $this->userTable = $userTable;
        $this->roleTable = $roleTable;
    }

    public function register(Request $request) {
        $this->validate($request, [
            'name'          => 'required',
            'mobile'        => 'required',
            'email'         => 'required',
            'job'           => 'required',
            'password'      => 'required',
            'role'          => 'required',
            'student_code'  => 'required'
        ]);

        if($this->userTable->where('mobile', $request->mobile)->first() != null) {
            return response([
                'code'      => 1001,
                'message'   => '该手机号已注册'
            ]);
        }

        $role = $this->roleTable->where('name', $request->role)->first();
        if($role == null) {
            return response([
                'code'      => 1004,
                'message'   => '无角色名对应的角色'
            ]);
        }

        $roleId = $role->id;

        $this->userTable->insert([
            'name'          => $request->name,
            'mobile'        => $request->mobile,
            'email'         => $request->email,
            'job'           => $request->job,
            'password'      => Encrypt::encrypt($request->password),
            'student_code'  => $request->student_code

        ]);

        $user = $this->userTable->where('mobile', $request->mobile)->first();
        $userRoleTable = new UserRole();
        $userRoleTable->insert([
            'users_id'  => $user->id,
            'roles_id'  => $roleId
        ]);

        return response([
            'code'  => 0
        ]);
    }

    public function login(Request $request) {
        $this->validate($request, [
            'mobile'    => 'required',
            'password'  => 'required'
        ]);

        $user = $this->userTable->where('mobile', $request->mobile)->first();
        if($user == null) {
            return response([
                'code'      => 1002,
                'message'   => '该手机号未注册'
            ]);
        }

        if(!Encrypt::check($user->password, $request->password)) {
            return response([
                'code'      => 1003,
                'message'   => '密码不正确'
            ]);
        }

        $expiredAt = Carbon::now()->timestamp + $this->tokenExpireTime;
        $token = TokenTools::createAndSet($user->id, $expiredAt);

        $userRoleTable = new UserRole();
        $userRole = $userRoleTable->where('users_id', $user->id)->first();
        $role = $this->roleTable->where('id', $userRole->roles_id)->first()->name;

        return response([
            'code'  => 0,
            'data'  => [
                'token' => $token,
                'role'  => $role
            ]
        ]);
    }
}
