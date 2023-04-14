<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Siswa;
use DB;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:siswa', ['except' => ['login']]);
    }

    public function login(Request $request){
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');

        $token = Auth::guard('siswa')->attempt($credentials);

        if ($token) {
            DB::table('siswas')
                ->where('id',Auth::guard('siswa')->user()->id)
                ->update([
                    'remember_token' => $token,
                ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::guard('siswa')->user();

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'token' => $token,
        ]);

    }

    public function logout(Request $request){
        DB::table('siswas')
            ->where('id',Auth::guard('siswa')->user()->id)
            ->update([
                'remember_token' => null,
            ]);

        Auth::guard('siswa')->logout();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh(){
        $user = Auth::guard('siswa')->user();

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'token' => Auth::guard('siswa')->refresh(),            
        ]);
    }
}
