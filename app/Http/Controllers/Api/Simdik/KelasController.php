<?php

namespace App\Http\Controllers\Api\Simdik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;
use Crypt;
use Hash;
use PDF;
use Auth;

class KelasController extends Controller
{
    public function __construct(){
      $this->middleware('auth:simdik');
    }

    public function index(){
      $data     = DB::table('dt_kelas_sekolah')
                    ->leftjoin('simdiks','dt_kelas_sekolah.id_simdik','=','simdiks.id')
                    ->leftjoin('dt_kurikulum','dt_kelas_sekolah.id_kurikulum','=','dt_kurikulum.id')
                    ->selectRaw('dt_kelas_sekolah.id, 
                                 dt_kelas_sekolah.id_sekolah, 
                                 dt_kelas_sekolah.id_simdik, 
                                 dt_kelas_sekolah.id_kelas, 
                                 dt_kelas_sekolah.kelas,
                                 dt_kelas_sekolah.sub_kelas, 
                                 dt_kelas_sekolah.id_jurusan,
                                 dt_kelas_sekolah.jurusan,
                                 dt_kelas_sekolah.kode,
                                 dt_kurikulum.kurikulum,
                                 simdiks.gelar_depan,
                                 simdiks.nama,
                                 simdiks.gelar_belakang,
                                 (SELECT COUNT(siswas.id)
                                  FROM siswas
                                  WHERE siswas.id_kelas_sekolah = dt_kelas_sekolah.id
                                  AND siswas.hapus = 0) as siswa')
                    ->orderby('dt_kelas_sekolah.kelas')
                    ->orderby('dt_kelas_sekolah.kode')
                    ->orderby('dt_kelas_sekolah.sub_kelas')
                    ->where('dt_kelas_sekolah.hapus',0)
                    ->where('dt_kelas_sekolah.id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
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
        'id_kelas' =>  'required',
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
      }

      $kelas    = DB::table('dt_kelas')
                    ->where('id',$request->id_kelas)
                    ->first();

      $jurusan  = DB::table('dt_jurusan')
                    ->where('id',$request->id_jurusan)
                    ->first();

      DB::table('dt_kelas_sekolah')
        ->insert([
          'id_sekolah' => $request->id_sekolah, 
          'id_simdik' => $request->id_simdik, 
          'id_kelas' => $request->id_kelas, 
          'kelas' => $kelas->tingkat,
          'jurusan' => !$jurusan ? null : $jurusan->jurusan,
          'kode' => !$jurusan ? null : $jurusan->kode,
          'sub_kelas' => $request->sub_kelas, 
          'id_jurusan' => $request->id_jurusan, 
          'id_kurikulum' => $request->id_kurikulum, 
          'simdik_create' => Auth::guard('simdik')->user()->id, 
          'simdik_update' => Auth::guard('simdik')->user()->id,
        ]);

      $data   = DB::table('dt_kelas_sekolah')
                  ->where('id_sekolah',$request->id_sekolah)
                  ->orderby('id','desc')
                  ->first();

      return response()->json($data);
    }

    public function edit(request $request){
      $cek  = DB::table('dt_kelas_sekolah')->where('id',$request->id)->first();

      if($cek){
        $validator = Validator::make($request->all(), [
          'id_sekolah' => 'required',
          'id_kelas' =>  'required',
        ]);

        if ($validator->fails()) {
          return response()->json($validator->errors(), 422);
        }

        $kelas    = DB::table('dt_kelas')
                      ->where('id',$request->id_kelas)
                      ->first();

        $jurusan  = DB::table('dt_jurusan')
                      ->where('id',$request->id_jurusan)
                      ->first();

        DB::table('dt_kelas_sekolah')
          ->where('id',$request->id)
          ->update([
            'id_sekolah' => $request->id_sekolah, 
            'id_simdik' => $request->id_simdik, 
            'id_kelas' => $request->id_kelas, 
            'kelas' => $kelas->tingkat,
            'jurusan' => !$jurusan ? null : $jurusan->jurusan,
            'kode' => !$jurusan ? null : $jurusan->kode,
            'sub_kelas' => $request->sub_kelas, 
            'id_jurusan' => $request->id_jurusan, 
            'id_kurikulum' => $request->id_kurikulum, 
            'simdik_update' => Auth::guard('simdik')->user()->id
          ]);

      $data   = DB::table('dt_kelas_sekolah')
                  ->where('id',$request->id)
                  ->first();

      return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }

    public function hapus($id){
      $cek  = DB::table('dt_kelas_sekolah')->where('id',$id)->first();

      if($cek){
        DB::table('dt_kelas_sekolah')
          ->where('id',$id)
          ->update([
            'hapus' => 1,
            'simdik_update' => Auth::guard('simdik')->user()->id,
          ]);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }

    public function detil(request $request){
      $data     = DB::table('dt_kelas_sekolah')
                    ->where('id',$request->id)
                    ->first();

      $kelas    = DB::table('dt_kelas')
                    ->where('strata',$request->tingkat)
                    ->where('hapus',0)
                    ->get();

      $jurusan  = DB::table('dt_jurusan')
                    ->orderby('jurusan')
                    ->where('hapus',0)
                    ->get();

      $simdik   = DB::table('simdiks')
                    ->where('id_sekolah',$request->id_sekolah)
                    ->where('hapus',0)
                    ->orderby('simdiks.nama')
                    ->get();

      if($data){
        return response()->json([
          'data' => $data,
          'kelas' => $kelas,
          'jurusan' => $jurusan,
          'guru' => $simdik,
        ]);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }

    public function tambah(request $request){
      $kelas    = DB::table('dt_kelas')
                    ->where('strata',$request->tingkat)
                    ->where('hapus',0)
                    ->get();

      $jurusan  = DB::table('dt_jurusan')
                    ->orderby('jurusan')
                    ->where('hapus',0)
                    ->get();

      $simdik   = DB::table('simdiks')
                    ->where('id_sekolah',$request->id_sekolah)
                    ->where('hapus',0)
                    ->orderby('simdiks.nama')
                    ->get();

      return response()->json([
        'kelas' => $kelas,
        'jurusan' => $jurusan,
        'guru' => $simdik,
      ]);
    }
}
