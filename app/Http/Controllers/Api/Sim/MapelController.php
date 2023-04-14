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

class MapelController extends Controller
{
    public function __construct(){
      $this->middleware('auth:sim');
    }

    public function index(){
      $data   = DB::table('dt_mapel')
                  ->orderby('mapel')
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
        'mapel' => 'required|String',
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
      }

      DB::table('dt_mapel')
        ->insert([
          'mapel' => $request->mapel,
          'kode' => $request->kode,
          'tingkat_1' => $request->tingkat_1,
          'tingkat_2' => $request->tingkat_2,
          'tingkat_3' => $request->tingkat_3,
          'sim_create' => Auth::guard('sim')->user()->id,
          'sim_update' => Auth::guard('sim')->user()->id,
        ]);

      $baru   = DB::table('dt_mapel')
                  ->where('mapel',$request->mapel)
                  ->orderby('id','desc')
                  ->first();

      $data   = DB::table('dt_mapel')->where('id',$baru->id)->first();

      return response()->json($data);
    }

    public function edit(request $request){
      $cek  = DB::table('dt_mapel')->where('id',$request->id)->first();

      if($cek){
        $validator = Validator::make($request->all(), [
          'mapel' => 'required|String',
        ]);

        if ($validator->fails()) {
          return response()->json($validator->errors(), 422);
        }

        DB::table('dt_mapel')
          ->where('id',$request->id)
          ->update([
            'mapel' => $request->mapel,
            'kode' => $request->kode,
            'tingkat_1' => $request->tingkat_1,
            'tingkat_2' => $request->tingkat_2,
            'tingkat_3' => $request->tingkat_3,
            'sim_update' => Auth::guard('sim')->user()->id,
          ]);

        $data   = DB::table('dt_mapel')->where('id',$request->id)->first();

        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }

    public function hapus($id) {
      $cek  = DB::table('dt_mapel')->where('id',$id)->first();

      if($cek){
        DB::table('dt_mapel')
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
      $data   = DB::table('dt_mapel')->where('id',$id)->first();

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
