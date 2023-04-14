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

class KepemilikanController extends Controller
{
    public function __construct(){
      $this->middleware('auth:sim');
    }

    public function index(){
      $data   = DB::table('dt_kepemilikan')
                  ->orderby('id')
                  ->where('hapus',0)
                  ->get();

      if($data) {
        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
        ], 401);
      }
    }

    public function baru(request $request){
      $validator = Validator::make($request->all(), [
        'kepemilikan' => 'required|String',
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
      }

      DB::table('dt_kepemilikan')
        ->insert([
          'kepemilikan' => $request->kepemilikan,
          'petugas_create' => Auth::guard('sim')->user()->id,
          'petugas_update' => Auth::guard('sim')->user()->id,
        ]);

      $baru   = DB::table('dt_kepemilikan')
                  ->where('kepemilikan',$request->kepemilikan)
                  ->orderby('id','desc')
                  ->first();

      $data   = DB::table('dt_kepemilikan')->where('id',$baru->id)->first();

      return response()->json($data);
    }

    public function edit(request $request){
      $cek  = DB::table('dt_kepemilikan')->where('id',$request->id)->first();

      if($cek){
        $validator = Validator::make($request->all(), [
          'kepemilikan' => 'required|String',
        ]);

        if ($validator->fails()) {
          return response()->json($validator->errors(), 422);
        }

        DB::table('dt_kepemilikan')
          ->where('id',$request->id)
          ->update([
            'kepemilikan' => $request->kepemilikan,
            'petugas_update' => Auth::guard('sim')->user()->id,
          ]);

        $data   = DB::table('dt_kepemilikan')->where('id',$request->id)->first();

        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }

    public function hapus($id) {
      $cek  = DB::table('dt_kepemilikan')->where('id',$id)->first();

      if($cek){
        DB::table('dt_kepemilikan')
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
      $data   = DB::table('dt_kepemilikan')->where('id',$id)->first();

      if($data){
        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }
}
