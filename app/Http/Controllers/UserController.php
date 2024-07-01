<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserCreateRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Traits\HttpResponses;

class UserController extends Controller
{
    use HttpResponses;

    public function index(){
        $users = User::all();
        return $this->success($users);
    }

    public function store(UserCreateRequest $request){

        $created = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'bvn' => $request->bvn,
            'password' => Hash::make($request->password)
        ]);

        return $this->success($created, 'User Created Successfully');
    }

    public function edit(UpdateUserRequest $request, User $user){
        $updated = $user->update([
            'name' => $request->name ?? $user->name,
            'phone' => $request->phone ?? $user->phone,
            'bvn' => $request->bvn ?? $user->bvn,
        ]);
        if(!$updated){
            return $this->error(400, 'Something went wrong');
        }
        return $this->success($user);
    }

    public function remove(User $user){
        $user->forceDelete();
        return new JsonResponse([
            'status' => 'Success',
            'message' => 'User Removed'
        ]);
    }
}
