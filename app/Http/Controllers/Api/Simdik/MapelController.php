<?php

namespace App\Http\Controllers\Api\Simdik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;
use Crypt;
use Hash;
use Auth;

class MapelController extends Controller
{
    public function __construct(){
      $this->middleware('auth:simdik');
    }

    public function index(request $request){
      $senin   = DB::table('dt_mapel_sekolah')
                  ->leftjoin('simdiks','dt_mapel_sekolah.id_simdik','=','simdiks.id')
                  ->leftjoin('dt_mapel','dt_mapel_sekolah.id_mapel','=','dt_mapel.id')
                  ->leftjoin('dt_hari','dt_mapel_sekolah.hari','=','dt_hari.hari')
                  ->selectRaw('dt_mapel_sekolah.id, 
                               dt_mapel_sekolah.hari, 
                               dt_mapel_sekolah.jam, 
                               dt_mapel_sekolah.awal, 
                               dt_mapel_sekolah.akhir, 
                               dt_mapel.mapel, 
                               simdiks.gelar_depan, 
                               simdiks.nama, 
                               simdiks.gelar_belakang')
                  ->where('dt_mapel_sekolah.id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->where('dt_mapel_sekolah.id_kelas_sekolah',$request->id_kelas)
                  ->where('dt_mapel_sekolah.hapus',0)
                  ->where('dt_mapel_sekolah.hari','SENIN')
                  ->orderby('dt_mapel_sekolah.awal')
                  ->get();

      $selasa = DB::table('dt_mapel_sekolah')
                  ->leftjoin('simdiks','dt_mapel_sekolah.id_simdik','=','simdiks.id')
                  ->leftjoin('dt_mapel','dt_mapel_sekolah.id_mapel','=','dt_mapel.id')
                  ->leftjoin('dt_hari','dt_mapel_sekolah.hari','=','dt_hari.hari')
                  ->selectRaw('dt_mapel_sekolah.id, 
                               dt_mapel_sekolah.hari, 
                               dt_mapel_sekolah.jam, 
                               dt_mapel_sekolah.awal, 
                               dt_mapel_sekolah.akhir, 
                               dt_mapel.mapel, 
                               simdiks.gelar_depan, 
                               simdiks.nama, 
                               simdiks.gelar_belakang')
                  ->where('dt_mapel_sekolah.id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->where('dt_mapel_sekolah.id_kelas_sekolah',$request->id_kelas)
                  ->where('dt_mapel_sekolah.hapus',0)
                  ->where('dt_mapel_sekolah.hari','SELASA')
                  ->orderby('dt_mapel_sekolah.awal')
                  ->get();

      $rabu = DB::table('dt_mapel_sekolah')
                  ->leftjoin('simdiks','dt_mapel_sekolah.id_simdik','=','simdiks.id')
                  ->leftjoin('dt_mapel','dt_mapel_sekolah.id_mapel','=','dt_mapel.id')
                  ->leftjoin('dt_hari','dt_mapel_sekolah.hari','=','dt_hari.hari')
                  ->selectRaw('dt_mapel_sekolah.id, 
                               dt_mapel_sekolah.hari, 
                               dt_mapel_sekolah.jam, 
                               dt_mapel_sekolah.awal, 
                               dt_mapel_sekolah.akhir, 
                               dt_mapel.mapel, 
                               simdiks.gelar_depan, 
                               simdiks.nama, 
                               simdiks.gelar_belakang')
                  ->where('dt_mapel_sekolah.id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->where('dt_mapel_sekolah.id_kelas_sekolah',$request->id_kelas)
                  ->where('dt_mapel_sekolah.hapus',0)
                  ->where('dt_mapel_sekolah.hari','RABU')
                  ->orderby('dt_mapel_sekolah.awal')
                  ->get();

      $kamis = DB::table('dt_mapel_sekolah')
                  ->leftjoin('simdiks','dt_mapel_sekolah.id_simdik','=','simdiks.id')
                  ->leftjoin('dt_mapel','dt_mapel_sekolah.id_mapel','=','dt_mapel.id')
                  ->leftjoin('dt_hari','dt_mapel_sekolah.hari','=','dt_hari.hari')
                  ->selectRaw('dt_mapel_sekolah.id, 
                               dt_mapel_sekolah.hari, 
                               dt_mapel_sekolah.jam, 
                               dt_mapel_sekolah.awal, 
                               dt_mapel_sekolah.akhir, 
                               dt_mapel.mapel, 
                               simdiks.gelar_depan, 
                               simdiks.nama, 
                               simdiks.gelar_belakang')
                  ->where('dt_mapel_sekolah.id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->where('dt_mapel_sekolah.id_kelas_sekolah',$request->id_kelas)
                  ->where('dt_mapel_sekolah.hapus',0)
                  ->where('dt_mapel_sekolah.hari','KAMIS')
                  ->orderby('dt_mapel_sekolah.awal')
                  ->get();

      $jumat = DB::table('dt_mapel_sekolah')
                  ->leftjoin('simdiks','dt_mapel_sekolah.id_simdik','=','simdiks.id')
                  ->leftjoin('dt_mapel','dt_mapel_sekolah.id_mapel','=','dt_mapel.id')
                  ->leftjoin('dt_hari','dt_mapel_sekolah.hari','=','dt_hari.hari')
                  ->selectRaw('dt_mapel_sekolah.id, 
                               dt_mapel_sekolah.hari, 
                               dt_mapel_sekolah.jam, 
                               dt_mapel_sekolah.awal, 
                               dt_mapel_sekolah.akhir, 
                               dt_mapel.mapel, 
                               simdiks.gelar_depan, 
                               simdiks.nama, 
                               simdiks.gelar_belakang')
                  ->where('dt_mapel_sekolah.id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->where('dt_mapel_sekolah.id_kelas_sekolah',$request->id_kelas)
                  ->where('dt_mapel_sekolah.hapus',0)
                  ->where('dt_mapel_sekolah.hari','JUMAT')
                  ->orderby('dt_mapel_sekolah.awal')
                  ->get();

      $sabtu = DB::table('dt_mapel_sekolah')
                  ->leftjoin('simdiks','dt_mapel_sekolah.id_simdik','=','simdiks.id')
                  ->leftjoin('dt_mapel','dt_mapel_sekolah.id_mapel','=','dt_mapel.id')
                  ->leftjoin('dt_hari','dt_mapel_sekolah.hari','=','dt_hari.hari')
                  ->selectRaw('dt_mapel_sekolah.id, 
                               dt_mapel_sekolah.hari, 
                               dt_mapel_sekolah.jam, 
                               dt_mapel_sekolah.awal, 
                               dt_mapel_sekolah.akhir, 
                               dt_mapel.mapel, 
                               simdiks.gelar_depan, 
                               simdiks.nama, 
                               simdiks.gelar_belakang')
                  ->where('dt_mapel_sekolah.id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->where('dt_mapel_sekolah.id_kelas_sekolah',$request->id_kelas)
                  ->where('dt_mapel_sekolah.hapus',0)
                  ->where('dt_mapel_sekolah.hari','SABTU')
                  ->orderby('dt_mapel_sekolah.awal')
                  ->get();

      if($senin) {
        return response()->json([
          'senin' => $senin,
          'selasa' => $selasa,
          'rabu' => $rabu,
          'kamis' => $kamis,
          'jumat' => $jumat,
          'sabtu' => $sabtu,
        ]);
      } else {
        return response()->json([
          'status' => 'error',
        ], 401);
      }
    }

    public function baru(request $request){
      $validator = Validator::make($request->all(), [
        'id_sekolah' => 'required',
        'id_kelas_sekolah' => 'required',
        'id_mapel' => 'required',
        'hari' => 'required',
        'jam' => 'required',
        'awal' => 'required',
        'akhir' => 'required',
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
      }

      DB::table('dt_mapel_sekolah')
        ->insert([
          'id_sekolah' => $request->id_sekolah, 
          'id_kelas_sekolah' => $request->id_kelas_sekolah, 
          'id_simdik' => $request->id_simdik, 
          'id_mapel' => $request->id_mapel, 
          'hari' => $request->hari, 
          'jam' => $request->jam, 
          'awal' => $request->awal, 
          'akhir' => $request->akhir, 
          'simdik_create' => Auth::guard('simdik')->user()->id,
          'simdik_update' => Auth::guard('simdik')->user()->id,
        ]);

      $data   = DB::table('dt_mapel_sekolah')
                  ->where('id_kelas_sekolah',$request->id_kelas_sekolah)
                  ->orderby('id','desc')
                  ->first();

      return response()->json($data);
    }

    public function edit(request $request){
      $cek  = DB::table('dt_mapel_sekolah')->where('id',$request->id)->first();

      if($cek){
        $validator = Validator::make($request->all(), [
          'id_sekolah' => 'required',
          'id_kelas_sekolah' => 'required',
          'id_mapel' => 'required',
          'hari' => 'required',
          'jam' => 'required',
          'awal' => 'required',
          'akhir' => 'required',
        ]);

        if ($validator->fails()) {
          return response()->json($validator->errors(), 422);
        }

        DB::table('dt_mapel_sekolah')
          ->where('id',$request->id)
          ->update([
            'id_sekolah' => $request->id_sekolah, 
            'id_kelas_sekolah' => $request->id_kelas_sekolah, 
            'id_simdik' => $request->id_simdik, 
            'id_mapel' => $request->id_mapel, 
            'hari' => $request->hari, 
            'jam' => $request->jam, 
            'awal' => $request->awal, 
            'akhir' => $request->akhir, 
            'simdik_update' => Auth::guard('simdik')->user()->id,
          ]);

        $data   = DB::table('dt_mapel_sekolah')
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

    public function hapus($id) {
      $cek  = DB::table('dt_mapel_sekolah')->where('id',$id)->first();

      if($cek){
        DB::table('dt_mapel_sekolah')
          ->where('id',$id)
          ->update([
            'hapus' => 1,
            'petugas_update' => Auth::guard('simdik')->user()->id,
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
      $sekolah  = DB::table('sekolah')
                    ->where('id',Auth::guard('simdik')->user()->id_sekolah)
                    ->first();

      $tingkat = $sekolah->bentuk_pendidikan;

      $kelas    = DB::table('dt_kelas_sekolah')
                    ->where('id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                    ->orderby('dt_kelas_sekolah.kelas')
                    ->orderby('dt_kelas_sekolah.kode')
                    ->orderby('dt_kelas_sekolah.sub_kelas')
                    ->where('dt_kelas_sekolah.hapus',0)
                    ->get();

      $simdik   = DB::table('simdiks')
                    ->where('id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                    ->where('hapus',0)
                    ->orderby('simdiks.nama')
                    ->get();

      $mapel    = DB::table('dt_mapel')
                    ->orderby('dt_mapel.mapel')
                    ->where('dt_mapel.hapus',0)
                    ->when($tingkat == 1, function ($query) {
                      return $query->where('dt_mapel.tingkat_1',1);
                    })
                    ->when($tingkat == 2, function ($query) {
                      return $query->where('dt_mapel.tingkat_2',1);
                    })
                    ->when($tingkat == 3, function ($query) {
                      return $query->where('dt_mapel.tingkat_3',1);
                    })
                    ->get();

      if($id == 0){
        return response()->json([
          'kelas' => $kelas,
          'guru' => $simdik,
          'mapel' => $mapel,
        ]);
      } else {
        $data     = DB::table('dt_mapel_sekolah')
                      ->where('id',$id)
                      ->first();
        
        if($data){
          return response()->json([
            'data' => $data,
            'kelas' => $kelas,
            'guru' => $simdik,
            'mapel' => $mapel,
          ]);
        } else {
          return response()->json([
            'status' => 'error',
            'message' => 'Data tidak ditemukan',
          ], 401);
        }
      }      
    }
}
