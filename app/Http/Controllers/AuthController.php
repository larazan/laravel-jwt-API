<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(Request $request) {
        // $credentials = request(['name', 'email', 'password']);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'city' => 'required|string',
            'role' => 'required|string' 
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request) {
        // $credentials = request(['email', 'password']);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token=auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }
    
    public function createNewToken($token)
    {
        return response()->json([
            'status' => 'success',
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => env('JWT_TTL', 60*60*7),
            'user' => auth()->user()
        ]);
    }

    public function user($id) {
        $user = User::find($id);

        return response()->json([
            'message' => 'User successfully get',
            'user' => $user,
        ], 201);
    }

    public function profile() {
        return response()->json(auth()->user());
    }

    public function updateProfile(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            // 'password' => 'required|string|confirmed|min:6',
            'city' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::find($id);

        $user->update(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'message' => 'User successfully updated',
            'user' => $user,
        ], 201);
    }

    public function logout() {
        auth()->logout();

        return response()->json([
            'message' => 'User logged out'
        ], 201);
    
    }
}
