<?php

namespace App\Http\Controllers\Api\Simdik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;
use Auth;

class JurusanController extends Controller
{
    public function __construct(){
      $this->middleware('auth:simdik');
    }

    public function index(){
      $data   = DB::table('dt_jurusan')
                  ->orderby('jurusan')
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
        'jurusan' => 'required|String',
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
      }

      DB::table('dt_jurusan')
        ->insert([
          'jurusan' => $request->jurusan,
          'kode' => $request->kode,
          'simdik_create' => Auth::guard('simdik')->user()->id,
          'simdik_update' => Auth::guard('simdik')->user()->id,
        ]);

      $baru   = DB::table('dt_jurusan')
                  ->where('jurusan',$request->jurusan)
                  ->orderby('id','desc')
                  ->first();

      $data   = DB::table('dt_jurusan')->where('id',$baru->id)->first();

      return response()->json($data);
    }

    public function edit(request $request){
      $cek  = DB::table('dt_jurusan')->where('id',$request->id)->first();

      if($cek){
        $validator = Validator::make($request->all(), [
          'jurusan' => 'required|String',
        ]);

        if ($validator->fails()) {
          return response()->json($validator->errors(), 422);
        }

        DB::table('dt_jurusan')
          ->where('id',$request->id)
          ->update([
            'jurusan' => $request->jurusan,
            'kode' => $request->kode,
            'simdik_update' => Auth::guard('simdik')->user()->id,
          ]);

        $data   = DB::table('dt_jurusan')->where('id',$request->id)->first();

        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }

    public function hapus($id) {
      $cek  = DB::table('dt_jurusan')->where('id',$id)->first();

      if($cek){
        DB::table('dt_jurusan')
          ->where('id',$id)
          ->update([
            'hapus' => 1,
            'simdik_update' => Auth::guard('simdik')->user()->id,
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
      $data   = DB::table('dt_jurusan')->where('id',$id)->first();

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
