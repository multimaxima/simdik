<?php

namespace App\Http\Controllers\Api\Sim;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Imagick;
use DB;
use Crypt;
use Hash;
use File;
use PDF;
use Image;
use Auth;

class PetugasController extends Controller
{
    public function __construct(){
      $this->middleware('auth:sim');
    }

    public function index(){
      $data   = DB::table('sims')
                  ->orderby('nama')
                  ->where('hapus',0)
                  ->where('id','>',1)
                  ->get();

      if($data) {
        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
        ], 401);
      }
    }

    public function validasi(request $request){
      if($request->id){
        $id   = $request->id;
      } else {
        $id   = '';
      }

      if($request->username){
        $username   = $request->username;
      } else {
        $username   = '';
      }

      if($request->email){
        $email   = $request->email;
      } else {
        $email   = '';
      }

      if($request->hp){
        $hp   = $request->hp;
      } else {
        $hp   = '';
      }

      $data   = DB::table('sims')
                  ->when($id, function ($query) use ($id) {
                      return $query->whereNot('sims.id',$id);
                    })
                  ->when($username, function ($query) use ($username) {
                      return $query->where('sims.username',$username);
                    })
                  ->when($email, function ($query) use ($email) {
                      return $query->where('sims.email',$email);
                    })
                  ->when($hp, function ($query) use ($hp) {
                      return $query->where('sims.hp',$hp);
                    })                  
                  ->count();

      if($data) {
        return response()->json(false);
      } else {
        return response()->json(true);
      }
    }

    public function baru(request $request){
      $validator = Validator::make($request->all(), [
        'username' => 'required|String|unique:sims|min:5',
        'hp' => 'required|string|unique:sims|min:5',
        'email' => 'required|email|unique:sims|min:5',
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
      }

      DB::table('sims')
        ->insert([
          'nama' => $request->nama,
          'alamat' => $request->alamat,
          'hp' => $request->hp,
          'whatsapp' => $request->whatsapp,
          'email' => $request->email,
          'username' => $request->username,
          'password' => bcrypt('123456'),
          'petugas_create' => Auth::guard('sim')->user()->id,
          'petugas_update' => Auth::guard('sim')->user()->id,
        ]);

      $baru   = DB::table('sims')
                  ->where('username',$request->username)
                  ->orderby('id','desc')
                  ->first();

      if($request->foto){
        DB::table('sims')
          ->where('id',$baru->id)
          ->update([
            'foto' => $request->foto,
          ]);
      }

      $data   = DB::table('sims')->where('id',$baru->id)->first();

      return response()->json($data);
    }

    public function edit(request $request){
      $cek  = DB::table('sims')->where('id',$request->id)->first();

      if($cek){
        $validator = Validator::make($request->all(), [
          'username' => 'required|String|unique:sims,username,'.$request->id,
          'hp' => 'required|string|unique:sims,hp,'.$request->id,
          'email' => 'required|email|unique:sims,email,'.$request->id,
        ]);

        if ($validator->fails()) {
          return response()->json($validator->errors(), 422);
        }

        DB::table('sims')
          ->where('id',$request->id)
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
            ->where('id',$request->id)
            ->update([
              'foto' => $request->foto,
            ]);
        }

        $data   = DB::table('sims')->where('id',$request->id)->first();

        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }

    public function hapus($id) {
      $cek  = DB::table('sims')->where('id',$id)->first();

      if($cek){
        DB::table('sims')
          ->where('id',$id)
          ->update([
            'hapus' => 1,
            'petugas_update' => Auth::guard('sim')->user()->id,
          ]);

        return response()->json(['status' => 'success'], 200);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }

    public function detil($id) {
      $data   = DB::table('sims')->where('id',$id)->first();

      if($data){
        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }

    public function reset($id){
      DB::table('sims')
        ->where('id',$id)
        ->update([
          'password' => bcrypt('123456'),
        ]);

      return response()->json([
        'status' => 'success',
        'message' => 'Password berhasil di reset.',
      ]);
    }
}
