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

class KelasSekolahController extends Controller
{
    public function __construct(){
      $this->middleware('auth:sim');
    }

    public function index(request $request){
      $data   = DB::table('dt_kelas_sekolah')
                  ->leftjoin('dt_kelas','dt_kelas_sekolah.id_kelas','=','dt_kelas.id')
                  ->leftjoin('dt_jurusan','dt_kelas_sekolah.id_jurusan','=','dt_jurusan.id')
                  ->selectRaw('dt_kelas_sekolah.id,
                               dt_kelas_sekolah.id_sekolah,
                               dt_kelas.tingkat,
                               dt_jurusan.kode,
                               dt_kelas_sekolah.id_kelas,
                               dt_kelas_sekolah.id_jurusan,
                               dt_kelas_sekolah.sub_kelas')
                  ->orderby('dt_kelas.tingkat')
                  ->orderby('dt_jurusan.kode')
                  ->orderby('dt_kelas_sekolah.sub_kelas')
                  ->where('dt_kelas_sekolah.hapus',0)
                  ->where('dt_kelas_sekolah.id_sekolah',$request->id_sekolah)
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
        'id_sekolah' => 'required',
        'id_kelas' => 'required',
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
      }

      DB::table('dt_kelas_sekolah')
        ->insert([
          'id_sekolah' => $request->id_sekolah,
          'id_kelas' => $request->id_kelas,
          'sub_kelas' => $request->sub_kelas,
          'id_jurusan' => $request->id_jurusan,
          'sim_create' => Auth::guard('sim')->user()->id,
          'sim_update' => Auth::guard('sim')->user()->id,
        ]);

      $data   = DB::table('dt_kelas_sekolah')
                  ->where('id_sekolah',$request->id_sekolah)
                  ->where('id_kelas',$request->id_kelas)
                  ->orderby('id','desc')
                  ->first();

      return response()->json($data);
    }

    public function edit(request $request){
      $cek  = DB::table('dt_kelas_sekolah')->where('id',$request->id)->first();

      if($cek){
        $validator = Validator::make($request->all(), [
          'id_sekolah' => 'required',
          'id_kelas' => 'required',
        ]);

        if ($validator->fails()) {
          return response()->json($validator->errors(), 422);
        }

        DB::table('dt_kelas_sekolah')
          ->where('id',$request->id)
          ->update([
            'id_sekolah' => $request->id_sekolah,
            'id_kelas' => $request->id_kelas,
            'sub_kelas' => $request->sub_kelas,
            'id_jurusan' => $request->id_jurusan,
            'sim_update' => Auth::guard('sim')->user()->id,
          ]);

        $data   = DB::table('dt_kelas_sekolah')->where('id',$request->id)->first();

        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }

    public function hapus($id) {
      $cek  = DB::table('dt_kelas_sekolah')->where('id',$id)->first();

      if($cek){
        DB::table('dt_kelas_sekolah')
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
      $data   = DB::table('dt_kelas_sekolah')
                  ->where('dt_kelas_sekolah.id',$id)
                  ->where('dt_kelas_sekolah.hapus',0)
                  ->first();

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
