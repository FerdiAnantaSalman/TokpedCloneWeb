<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    // API for login
    public function login(Request $request)
    {
        $validasi = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($validasi->fails()) {
            return $this->error($validasi->errors());
        }

        $user = User::where('email', $request->email)->first();

        if ($user) {

            // Check Password
            if (password_verify($request->password, $user->password)) {
                return $this->success($user);
            } else {
                return $this->error("Password Salah");
            }
        }

        return $this->error("User not found");
    }


    // API for register 
    public function register(Request $request)
    {
        $validasi = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|min:6'
        ]);

        if ($validasi->fails()) {
            return $this->error($validasi->errors()->first());
        }

        $user = User::create(array_merge($request->all(), [
            'password' => bcrypt($request->password)
        ]));

        if ($user) {
            return $this->success($user, "Selamat Datang" . $user->name);
        } else {
            return $this->error("Terjadi Kesalahan");
        }
    }


    // Function if success
    public function success($data, $message = "success")
    {
        return response()->json([
            'code' => 200,
            'message' => $message,
            'data' => $data
        ], 200);
    }


    // Function if error
    public function error($message)
    {
        return response()->json([
            'code' => 400,
            'message' => $message
        ], 400);
    }
}