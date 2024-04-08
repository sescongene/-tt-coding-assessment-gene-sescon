<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    
    public function register(RegisterRequest $request) {
        $data = $request->validated();

        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        $user->image = $request->file('image');
        $user->save();

        $token = $user->createToken($request->header('user-agent'));

        return $this->respondWithToken($token->plainTextToken, UserResource::make($user));
    }


    public function authenticate(Request $request) {
        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
            
            $user = auth()->user();
            $token = $user->createToken($request->header('user-agent'));
            return $this->respondWithToken($token->plainTextToken, UserResource::make($user));
        }

        return response(['error' => 'Unauthenticated'], 401);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response(['message' => 'Logged out']);
    }
}
