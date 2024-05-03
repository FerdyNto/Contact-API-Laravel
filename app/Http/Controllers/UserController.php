<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

    public function login(UserLoginRequest $request): UserResource
    {
        // Validasi data
        $data = $request->validated();

        // get user dari database sesuai dengan username yg diinput
        $user = User::where('username', $data['username'])->first();

        // cek username atau password yg diinputkan tidak sesuai
        if (!$user || !Hash::check($data['password'], $user->password)) {
            // ada di database?
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "username or password wrong"
                    ]
                ]
            ], 401));
        }

        // buat token untuk login user
        $user->token = Str::uuid()->toString();
        $user->save();

        // karena response 200 tidak perlu return JsonResponse cukup UserResource saja
        return new UserResource($user);
    }
}
