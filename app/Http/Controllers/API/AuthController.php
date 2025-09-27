<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $r) {
        $data = $r->validate([
            'name'=>'required', 'email'=>'required|email|unique:users',
            'password'=>'required|min:6', 'role'=>'in:admin,organizer,customer'
        ]);
        $user = User::create(['name'=>$data['name'],'email'=>$data['email'],'password'=>Hash::make($data['password']),'role'=>$data['role'] ?? 'customer']);
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json(['user'=>$user,'token'=>$token],201);
    }

    public function login(Request $r) {
        $data = $r->validate(['email'=>'required|email','password'=>'required']);
        $user = User::where('email',$data['email'])->first();
        if (!$user || !Hash::check($data['password'],$user->password)) return response()->json(['message'=>'Invalid credentials'],401);
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json(['user'=>$user,'token'=>$token]);
    }

    public function logout(Request $r) {
        $r->user()->currentAccessToken()->delete();
        return response()->json(['message'=>'Logged out']);
    }

    public function me(Request $r) { return $r->user(); }
}
