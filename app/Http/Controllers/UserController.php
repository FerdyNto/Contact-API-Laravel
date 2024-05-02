<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // UserRegisterRequest adalah request untuk create user
    // UserResource adalah respon nya
    public function register(UserRegisterRequest $request): JsonResponse
    {
        // validasi data yg di request
        $data = $request->validated();

        // cek apakah ada username yang sama
        if (User::where('username', $data['username'])->count() >= 1) {
            // ada di database?
            throw new HttpResponseException(response([
                "errors" => [
                    "username" => [
                        "username already registered"
                    ]
                ]
            ], 400));
        }

        // simpan data dalam variabel $user
        $user = new User($data);

        // enkripsi password
        $user->password = Hash::make($data['password']);

        // save data user
        $user->save();

        // kembalikan data user ke respon
        return (new UserResource($user))->response()->setStatusCode(201);
    }
}
