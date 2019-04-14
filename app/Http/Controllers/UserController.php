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

    public function __construct(User $userTable) {
        $this->userTable = $userTable;
    }

    public function register(Request $request) {
        $this->validate($request, [
            'name'      => 'required',
            'mobile'    => 'required',
            'email'     => 'required',
            'job'       => 'required',
            'password'  => 'required'
        ]);

        if($this->userTable->where('mobile', $request->mobile)->first() != null) {
            return response([
                'code'      => 1001,
                'message'   => '该手机号已注册'
            ]);
        }

        $this->userTable->insert([
            'name'      => $request->name,
            'mobile'    => $request->mobile,
            'email'     => $request->email,
            'job'       => $request->job,
            'password'  => Encrypt::encrypt($request->password)
        ]);

        $user = $this->userTable->where('mobile', $request->mobile)->first();
        $userRoleTable = new UserRole();
        $userRoleTable->insert([
            'users_id'  => $user->id,
            'roles_id'  => 2
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
        $roleTable = new Role();
        $userRole = $userRoleTable->where('users_id', $user->id)->first();
        $role = $roleTable->where('id', $userRole->roles_id)->first()->name;

        return response([
            'code'  => 0,
            'data'  => [
                'token' => $token,
                'role'  => $role
            ]
        ]);
    }
}
