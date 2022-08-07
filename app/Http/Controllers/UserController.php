<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' =>  'required|email',
                'password' =>  'required'
            ]
        );
        if ($validator->fails())
            return response()->json(['code' => 400, 'message' => 'Bad Request'], 200);

        $user = User::where('email', $request->email)->first();
        if (!isset($user)) {
            return response()->json(['code' => 400, 'message' => 'User not found'], 200);
        }

        if (!Hash::check($request->password, $user->password))
            return response()->json(['code' => 400,'message' => 'Do not match our records!!'], 200);

        $tokenResult = $user->createToken('authToken')->plainTextToken;
        return response()->json(['code' => 200, 'token' => $tokenResult, 'message' => 'good', 'type' => $user->type], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'code' => 200,
            'msg' => 'token deleted successfully'
        ], 200);
    }
    public function signUp(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' =>  'required',
            'type' =>  'required|in:Owner,Customer',
            'phone' =>  'required|min:10|max:10',
        ]);
        $user = User::where('email', $request->email)->first();
        if (isset($user)) {
            return response()->json(['status_code' => 400, 'message' => 'this email already in use.'], 200);
        }
        if ($valid->fails())
            return response()->json(['status_code' => 400, 'message' => 'Bad Request'], 200);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'type' => $request->type,
            'avatar' => '',
        ]);
        if ($request->hasFile('avatar')) {
            $avatar = $request->avatar;
            $file_extention = $avatar->getClientOriginalExtension();
            $file_name = time() . '.' . $file_extention;  // 546165165.jpg
            $path = '/storage/avatars';
            $avatar->move($path, $file_name);
            $user->avatar = $path . '/' . $file_name;
            $user->save();
        }
        if (isset($user)) {
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return response()->json([
                'code' => 200,
                'message' => "user created successfully",
                'token' => $tokenResult,
                'type' => $user->type,
            ], 200);
        } else {
            return response()->json([
                'code' => 300,
                'message' => "user can't be created now."
            ], 200);
        }
    }
}
