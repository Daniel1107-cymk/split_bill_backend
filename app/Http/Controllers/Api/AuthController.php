<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

class AuthController extends ApiController
{
    public function register(Request $request) {
        $validation = Validator::make(
            $request->all(),
            [
                'full_name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed',
                'gender' => 'required',
            ],
        );
        if($validation->fails()) {
            return $this->respond('fail', $validation->errors(), 422);
        }
        $password = Hash::make($request['password']);
        $user = User::create(array_merge($request->all(), ['password' => $password]));
        
        return $this->respond('success', $user, 200);
    }

    public function login(Request $request) {
        $validation = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required',
            ],
        );
        if($validation->fails()) {
            return $this->respond('fail', $validation->errors(), 422);
        }
        if (!$token = auth()->attempt($request->all())) {
            $data = ['email' => 'Email and password not match'];

            return $this->respond('fail', $data, 401);
        }
        $data = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];

        return $this->respond('success', $data, 200);
    }

    protected function respond($status, $data, $code) {
        return response()->json([
            'status' => $status,
            'data' => $data
        ], $code);
    }
}
