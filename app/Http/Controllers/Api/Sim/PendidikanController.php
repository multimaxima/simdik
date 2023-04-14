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

class PendidikanController extends Controller
{
    public function __construct(){
      $this->middleware('auth:sim');
    }

    public function index(){
      $data   = DB::table('dt_pendidikan')
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
        'pendidikan' => 'required|String',
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
      }

      DB::table('dt_pendidikan')
        ->insert([
          'pendidikan' => $request->pendidikan,
          'sim_create' => Auth::guard('sim')->user()->id,
          'sim_update' => Auth::guard('sim')->user()->id,
        ]);

      $baru   = DB::table('dt_pendidikan')
                  ->where('pendidikan',$request->pendidikan)
                  ->orderby('id','desc')
                  ->first();

      $data   = DB::table('dt_pendidikan')->where('id',$baru->id)->first();

      return response()->json($data);
    }

    public function edit(request $request){
      $cek  = DB::table('dt_pendidikan')->where('id',$request->id)->first();

      if($cek){
        $validator = Validator::make($request->all(), [
          'pendidikan' => 'required|String',
        ]);

        if ($validator->fails()) {
          return response()->json($validator->errors(), 422);
        }

        DB::table('dt_pendidikan')
          ->where('id',$request->id)
          ->update([
            'pendidikan' => $request->pendidikan,
            'sim_update' => Auth::guard('sim')->user()->id,
          ]);

        $data   = DB::table('dt_pendidikan')->where('id',$request->id)->first();

        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }

    public function hapus($id) {
      $cek  = DB::table('dt_pendidikan')->where('id',$id)->first();

      if($cek){
        DB::table('dt_pendidikan')
          ->where('id',$id)
          ->update([
            'hapus' => 1,
            'sim_update' => Auth::guard('sim')->user()->id,
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
      $data   = DB::table('dt_pendidikan')->where('id',$id)->first();

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
