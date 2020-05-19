<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
        $user = $this->create($request->all());
        $this->guard()->login($user);
        return response()->json([
            'user' => $user,
            'message' => 'Register successful'
        ], 200);
    }

    protected  function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            //'password' => ['required', 'string', 'min:4', 'confirmed'],
            // NO PASSWORD CONFIRMATION
            'password' => ['required', 'string', 'min:4'],
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function guard()
    {
        return Auth::guard();
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $authUser = auth()->user();
            return response()->json(['message' => 'Login successful'], 200);
        }

        return response()->json(['message' => 'Invalid email or password'], 401);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Logged out'], 200);
    }
}
