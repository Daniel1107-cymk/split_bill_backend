<?php

namespace App\Http\Controllers\Api;

use App\Models\Bill;
use App\Models\BillDetail;
use App\Models\User;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
        $user_id = auth()->user()->id;
        $bills = Bill::where('user_id', $user_id)->get();
        return $this->respond('success', $bills, 200);
    }

    public function showBill($id) {
        $user_id = auth()->user()->id;
        $bill = Bill::where([
            ['user_id', $user_id],
            ['id', $id],
        ])->first();
        $bill->load('billDetails');
        return $this->respond('success', $bill, 200);
    }

    public function storeBill(Request $request) {
        $validation = Validator::make(
            $request->all(), 
            [
                'total_people' => 'required|numeric',
                'grand_total' => 'required|numeric',
                'bill_details' => 'required|array|min:1',
                'bill_details.*.item_name' => 'required',
                'bill_details.*.quantity' => 'required|numeric|min:1',
                'bill_details.*.price' => 'required|numeric',
            ],
        );
        if($validation->fails()) {
            return $this->respond('fail', $validation->errors(), 422);
        }

        // insert into bill
        $user_id = auth()->user()->id;
        $code = 'SP'.date('YmdHis');
        $splitted_value = $request['grand_total'] / $request['total_people'];

        $bill = Bill::create(array_merge(
            $request->except(['bill_details']),
            [
                'user_id' => $user_id,
                'code' => $code,
                'splitted_value' => $splitted_value,
                'date' => Carbon::now(),
            ],
        ));

        // insert into bill detail
        $bill_details = $request['bill_details'];
        $new_bill_details = [];
        for($i = 0; $i < count($bill_details); $i++) {
            $subtotal = $bill_details[$i]['quantity'] * $bill_details[$i]['price'];
            $new_bill_details[] = [
                'bill_id' => $bill->id,
                'item_name' => $bill_details[$i]['item_name'],
                'quantity' => $bill_details[$i]['quantity'],
                'price' => $bill_details[$i]['price'],
                'sub_total' => $subtotal,
            ];
        }
        $bill->billDetails()->createMany($new_bill_details);

        return $this->respond('success', $new_bill_details, 200);
    }

    protected function respond($status, $data, $code) {
        return response()->json([
            'status' => $status,
            'data' => $data
        ], $code);
    }
}
