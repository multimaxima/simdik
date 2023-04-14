<?php

namespace App\Http\Controllers\Api\Sim;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Sim;
use Validator;
use Imagick;
use DB;
use Crypt;
use File;
use PDF;
use Image;

class LoginController extends Controller
{
    public function __construct(){
      $this->middleware('auth:sim', ['except' => ['login','status']]);
    }

    public function login(Request $request){
      $validator = Validator::make($request->all(), [
        'username' => 'required|string',
        'password' => 'required|string',
      ]);
      
      if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
      }
      
      if (! $token = Auth::guard('sim')->attempt($validator->validated())) {
        return response()->json(['error' => 'Unauthorized'], 401);
      }
      
      return $this->createNewToken($token);
    }

    public function status(){
      if(Auth::guard('sim')->user()){
        return response()->json(true);
      } else {
        return response()->json(false);
      }
    }

    public function logout(Request $request){
      Auth::guard('sim')->logout();
      return response()->json(['status' => 'success'], 200);
    }

    public function refresh() {
      return $this->createNewToken(Auth::guard('sim')->refresh());
    }
    
    public function profil(){
      return response()->json(Auth::guard('sim')->user());
    }

    public function profil_simpan(request $request){
      $validator = Validator::make($request->all(), [
        'username' => 'required|String|min:5|unique:sims,username,'.Auth::guard('sim')->user()->id,
        'hp' => 'required|string|min:5|unique:sims,hp,'.Auth::guard('sim')->user()->id,
        'email' => 'required|email|min:5|unique:sims,email,'.Auth::guard('sim')->user()->id,
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
      }

      DB::table('sims')
        ->where('id',Auth::guard('sim')->user()->id)
        ->update([
          'nama' => $request->nama, 
          'alamat' => $request->alamat, 
          'hp' => $request->hp, 
          'whatsapp' => $request->whatsapp, 
          'email' => $request->email, 
          'username' => $request->username, 
          'petugas_update' => Auth::guard('sim')->user()->id, 
        ]);

      if($request->foto){
        DB::table('sims')
          ->where('id',Auth::guard('sim')->user()->id)
          ->update([
            'foto' => $request->foto,
          ]);
      }

      $data   = DB::table('sims')->where('id',Auth::guard('sim')->user()->id)->first();

      return response()->json($data, 200);
    }

    public function profil_password(request $request){
      $validator = Validator::make($request->all(), [
        'password' => 'required|string|confirmed|min:5',
      ]);
      
      if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
      }

      DB::table('sims')
        ->where('id',Auth::guard('sim')->user()->id)
        ->update([
          'password' => bcrypt($request->password),
        ]);
      
      return response()->json(['status' => 'success'], 200);
    }

    protected function createNewToken($token){
      return response()->json([
        'access_token' => $token,
        'expires_in' => Auth::guard('sim')->factory()->getTTL() * 60,
      ]);
    }
}
