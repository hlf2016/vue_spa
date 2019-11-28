<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class UsersController extends Controller
{
    public function index()
    {
        // UserResource::collection() 方法将用户的集合转换为 JSON 格式。
        return UserResource::collection(User::paginate(10));
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function update(User $user, Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email'
        ]);
        $user->update($data);
        return new UserResource($user);
    }

    public function destory(User $user)
    {
        $user->delete();
        return response(null, 204);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|min:8'
        ]);

        return new UserResource(User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]));
    }
}
