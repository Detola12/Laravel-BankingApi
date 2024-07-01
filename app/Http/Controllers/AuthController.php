<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use HttpResponses;
    public function login(LoginUserRequest $request){
        $request->validated($request->all());

        if(Auth::attempt($request->only(['email','password']))){
            $user = User::where('email', $request->email)->first();
            return $this->success([
                'user' => $user,
                'token' => $user->createToken('Api Token For '. $user->name)->plainTextToken
            ]);
        }

        return $this->error(404, "Credential do not match");
    }

    public function logout(){

        Auth::user()->tokens()->delete();
        return $this->success([
            'message' => 'Logged Out'
        ]);
    }
}
