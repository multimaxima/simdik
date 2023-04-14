<?php

namespace App\Http\Controllers\Api\Simdik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;
use Crypt;
use Hash;
use Auth;

class SekolahController extends Controller
{
    public function __construct(){
      $this->middleware('auth:simdik');
    }

    public function index(){
      $data   = DB::table('sekolah')
                  ->where('id',Auth::guard('simdik')->user()->id_sekolah)
                  ->first();

      $propinsi   = DB::table('dt_wilayah')
                      ->whereNotNull('no_prop')
                      ->whereNull('no_kab')
                      ->whereNull('no_kec')
                      ->whereNull('no_kel')
                      ->orderby('nama')
                      ->get();

      $kota       = DB::table('dt_wilayah')
                      ->where('no_prop',$data->no_prop)
                      ->whereNotNull('no_kab')
                      ->whereNull('no_kec')
                      ->whereNull('no_kel')
                      ->orderby('nama')
                      ->get();

      $kecamatan  = DB::table('dt_wilayah')
                      ->where('no_prop',$data->no_prop)
                      ->where('no_kab',$data->no_kab)
                      ->whereNotNull('no_kec')
                      ->whereNull('no_kel')
                      ->orderby('nama')
                      ->get();

      $desa       = DB::table('dt_wilayah')
                      ->where('no_prop',$data->no_prop)
                      ->where('no_kab',$data->no_kab)
                      ->where('no_kec',$data->no_kec)
                      ->whereNotNull('no_kel')
                      ->orderby('nama')
                      ->get();

      $bentuk     = DB::table('dt_bentuk_pendidikan')
                      ->where('hapus',0)
                      ->orderby('id')
                      ->get();

      $kepemilikan  = DB::table('dt_kepemilikan')
                        ->where('hapus',0)
                        ->orderby('id')
                        ->get();

      $bank       = DB::table('dt_bank')
                      ->where('hapus',0)
                      ->orderby('bank')
                      ->get();

      $iso        = DB::table('dt_sertifikasi_iso')
                      ->where('hapus',0)
                      ->orderby('id')
                      ->get();

      $kurikulum  = DB::table('dt_kurikulum')
                      ->where('hapus',0)
                      ->orderby('id')
                      ->get();

      $kontak     = DB::table('simdiks')
                      ->where('id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                      ->orderby('nama')
                      ->get();

      if($data){
        return response()->json([
          'data' => $data,
          'propinsi' => $propinsi,
          'kota' => $kota,
          'kecamatan' => $kecamatan,
          'desa' => $desa,
          'bentuk' => $bentuk,
          'bank' => $bank,
          'kepemilikan' => $kepemilikan,
          'iso' => $iso,
          'kurikulum' => $kurikulum,
          'kontak' => $kontak,
        ]);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Error',
        ], 401);
      }
    }

    public function edit(request $request){
      $data   = DB::table('sekolah')
                  ->where('id',$request->id)
                  ->first();

      $bentuk   = DB::table('dt_bentuk_pendidikan')
                    ->where('id',$request->bentuk_pendidikan)
                    ->first();

      if($data){
        DB::table('sekolah')
          ->where('id',$request->id)
          ->update([
            'nama' => $request->nama, 
            'alamat' => $request->alamat, 
            'rt' => $request->rt, 
            'rw' => $request->rw, 
            'dusun' => $request->dusun, 
            'no_kel' => $request->no_kel, 
            'no_kec' => $request->no_kec, 
            'no_kab' => $request->no_kab, 
            'no_prop' => $request->no_prop, 
            'desa' => $this->nama_kelurahan($request->no_prop, $request->no_kab, $request->no_kec, $request->no_kel),
            'kecamatan' => $this->nama_kecamatan($request->no_prop, $request->no_kab, $request->no_kec),
            'kota' => $this->nama_kota($request->no_prop, $request->no_kab),
            'propinsi' => $this->nama_propinsi($request->no_prop),
            'kodepos' => $request->kodepos, 
            'lat' => $request->lat, 
            'lng' => $request->lng, 
            'npsn' => $request->npsn, 
            'status' => $request->status, 
            'bentuk_pendidikan' => $request->bentuk_pendidikan, 
            'status_kepemilikan' => $request->status_kepemilikan, 
            'sk_pendirian' => $request->sk_pendirian, 
            'tgl_pendirian' => $request->tgl_pendirian ? date('Y-m-d', strtotime($request->tgl_pendirian)) : null, 
            'sk_izin' => $request->sk_izin, 
            'tgl_sk_izin' => $request->tgl_sk_izin ? date('Y-m-d', strtotime($request->tgl_sk_izin)) : null, 
            'kebutuhan_khusus' => $request->kebutuhan_khusus, 
            'bank' => $request->bank, 
            'cabang' => $request->cabang, 
            'rek_atas_nama' => $request->rek_atas_nama, 
            'status_bos' => $request->status_bos, 
            'waktu_penyelenggaraan' => $request->waktu_penyelenggaraan, 
            'sertifikasi_iso' => $request->sertifikasi_iso, 
            'sumber_listrik' => $request->sumber_listrik, 
            'daya_listrik' => $request->daya_listrik, 
            'akses_internet' => $request->akses_internet, 
            'akreditasi' => $request->akreditasi, 
            'kurikulum' => $request->kurikulum, 
            'telp' => $request->telp, 
            'fax' => $request->fax, 
            'email' => $request->email, 
            'web' => $request->web, 
            'facebook' => $request->facebook, 
            'twitter' => $request->twitter, 
            'instagram' => $request->instagram, 
            'google' => $request->google, 
            'youtube' => $request->youtube, 
            'whatsapp' => $request->whatsapp, 
            'motto' => $request->motto, 
            'id_simdiks' => $request->id_simdiks, 
            'strata' => $bentuk->tingkatan, 
            'simdik_update' => Auth::guard('simdik')->user()->id, 
          ]);

        if($request->logo){
          DB::table('sekolah')
            ->where('id',$request->id)
            ->update([
              'logo' => $request->logo,
            ]);
        }

        if($request->logo_web){
          DB::table('sekolah')
            ->where('id',$request->id)
            ->update([
              'logo_web' => $request->logo_web,
            ]);
        }

        return;
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }    

    public function nama_propinsi($noprop) {
      $data   = DB::table('dt_wilayah')
                  ->selectRaw('nama')
                  ->where('no_prop',$noprop)
                  ->whereNull('no_kab')
                  ->whereNull('no_kec')
                  ->whereNull('no_kel')
                  ->first();

      if($data){
        return $data->nama;
      } else {
        return NULL;
      }      
    }

    public function nama_kota($noprop, $nokab) {
      $data   = DB::table('dt_wilayah')
                  ->selectRaw('nama')
                  ->where('no_prop',$noprop)
                  ->where('no_kab',$nokab)
                  ->whereNull('no_kec')
                  ->whereNull('no_kel')
                  ->first();

      if($data){
        return $data->nama;
      } else {
        return NULL;
      }
    }

    public function nama_kecamatan($noprop, $nokab, $nokec) {
      $data   = DB::table('dt_wilayah')
                  ->selectRaw('nama')
                  ->where('no_prop',$noprop)
                  ->where('no_kab',$nokab)
                  ->where('no_kec',$nokec)
                  ->whereNull('no_kel')
                  ->first();

      if($data){
        return $data->nama;
      } else {
        return NULL;
      }      
    }

    public function nama_kelurahan($noprop, $nokab, $nokec, $nokel) {
      $data   = DB::table('dt_wilayah')
                  ->selectRaw('nama')
                  ->where('no_prop',$noprop)
                  ->where('no_kab',$nokab)
                  ->where('no_kec',$nokec)
                  ->where('no_kel',$nokel)
                  ->first();

      if($data){
        return $data->nama;
      } else {
        return NULL;
      }      
    }
}
