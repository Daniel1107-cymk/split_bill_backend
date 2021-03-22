<?php

namespace App\Http\Controllers\Api;

use App\Models\Bill;
use App\Models\BillDetail;
use App\Models\User;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class UserController extends ApiController
{
    public function me() {
        $user = auth()->user();
        if($user->id == null) {
            return $this->respond('fail', null, 401);
        }
        return $this->respond('success', $user, 200);
    }

    public function updateProfile(Request $request) {
        $validation = Validator::make(
            $request->all(),
            [
                'full_name' => 'required',
                'gender' => 'required',
            ],
        );
        if($validation->fails()) {
            return $this->respond('fail', $validation->errors(), 422);
        }
        $user = auth()->user();
        $user->update($request->all());
        return $this->respond('success', $user, 200);
    }

    public function changePassword(Request $request) {
        $validation = Validator::make(
            $request->all(),
            [
                'current_password' => 'required',
                'password' => 'required|confirmed',
            ],
        );
        if($validation->fails()) {
            return $this->respond('fail', $validation->errors(), 422);
        }
        $user = auth()->user();
        if(Hash::check($request->current_password, $user->password)) {
            $password = Hash::make($request->password);
            $user->update(['password' => $password]);
            return $this->respond('success', $user, 200);
        } else {
            $message = [
                'current_password' => "current password not match",
            ];
            return $this->respond('fail', $message, 422);
        }
    }

    public function indexBill() {
        $userId = auth()->user()->id;
        $bills = Bill::where('user_id', $userId)->get();
        return $this->respond('success', $bills, 200);
    }

    public function storeBill(Request $request) {
        $validation = Validator::make(
            $request->all(), 
            [
                'total_people' => 'required|numeric',
                'bill' => 'required|array|min:1',
                'bill.*.item_name' => 'required',
                'bill.*.quantity' => 'required|min:1',
                'bill.*.price' => 'required|numeric',
            ],
        );
        if($validation->fails()) {
            return $this->respond('fail', $validation->errors(), 422);
        }
        // $code = 'SP'.date('YmdHis');
        return $this->respond('succes', $request->all(), 200);
    }

    protected function respond($status, $data, $code) {
        return response()->json([
            'status' => $status,
            'data' => $data
        ], $code);
    }
}
