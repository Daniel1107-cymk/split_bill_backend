<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class UserController extends ApiController
{
    public function me() {
        $user = auth()->user();
        if($user->id == null) {
            return $this->respond('fail', null, 401);
        }
        return $this->respond('success', $user, 200);
    }

    protected function respond($status, $data, $code) {
        return response()->json([
            'status' => $status,
            'data' => $data
        ], $code);
    }
}
