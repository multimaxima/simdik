<?php

namespace App\Http\Controllers\Api\Sim;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Imagick;
use DB;
use Crypt;
use Hash;
use File;
use PDF;
use Image;
use Auth;

class SekolahController extends Controller
{
    public function __construct(){
      $this->middleware('auth:sim');
    }

    public function index(request $request){
      $data   = DB::table('sekolah')
                  ->leftjoin('simdiks','sekolah.id_simdiks','=','simdiks.id')
                  ->selectRaw('sekolah.id, 
                               sekolah.nama, 
                               sekolah.alamat, 
                               sekolah.rt, 
                               sekolah.rw, 
                               sekolah.dusun, 
                               sekolah.desa, 
                               sekolah.kecamatan, 
                               sekolah.kota, 
                               sekolah.propinsi, 
                               sekolah.kodepos, 
                               sekolah.email, 
                               sekolah.aktif, 
                               simdiks.gelar_depan, 
                               simdiks.nama as kontak, 
                               simdiks.gelar_belakang,
                               simdiks.hp')
                  ->where('sekolah.hapus',0)
                  ->orderby('sekolah.nama')
                  ->get();

      if($data) {
        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Error',
        ], 401);
      }
    }

    public function baru(request $request){
      if($request->logo){
        $logo   = $request->logo;
      } else {
        $logo   = null;
      }

      DB::table('sekolah')
        ->insert([
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
          'logo' => $logo, 
          'id_simdiks' => $request->id_simdiks, 
          //'panel_user' => $request->panel_user, 
          //'panel_password' => $request->panel_password, 
          //'server' => $request->server, 
          //'paket' => $request->paket, 
          //'id_status' => $request->id_status, 
          //'id_template' => $request->id_template, 
          //'panel_user_create' => $request->panel_user_create, 
          //'panel_web_create' => $request->panel_web_create, 
          //'hapus' => 0, 
          'sim_create' => Auth::guard('sim')->user()->id, 
          'sim_update' => Auth::guard('sim')->user()->id, 
        ]);

      $data   = DB::table('sekolah')
                  ->where('nama',$request->nama)
                  ->orderby('id','desc')
                  ->first();

      DB::table('sanitasi')
        ->insert([
          'id_sekolah' => $data->id,
          'sim_create' => Auth::guard('sim')->user()->id, 
          'sim_update' => Auth::guard('sim')->user()->id, 
        ]);

      $sarpras  = DB::table('dt_sarpras')->get();

      foreach($sarpras as $sar){
        DB::table('sarpras')
          ->insert([
            'id_sekolah' => $data->id,
            'dt_sarpras' => $sar->id,
            'tahun' => date("Y"),
            'sim_create' => Auth::guard('sim')->user()->id, 
            'sim_update' => Auth::guard('sim')->user()->id,  
          ]);
      }

      return response()->json($data->id);
    }

    public function edit(request $request){
      $data   = DB::table('sekolah')
                  ->where('id',$request->id)
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
            //'panel_user' => $request->panel_user, 
            //'panel_password' => $request->panel_password, 
            //'server' => $request->server, 
            //'paket' => $request->paket, 
            //'id_status' => $request->id_status, 
            //'id_template' => $request->id_template, 
            //'panel_user_create' => $request->panel_user_create, 
            //'panel_web_create' => $request->panel_web_create, 
            'sim_update' => Auth::guard('sim')->user()->id, 
          ]);

        if($request->logo){
          DB::table('sekolah')
            ->where('id',$request->id)
            ->update([
              'logo' => $request->logo,
            ]);
        }

        return response()->json([
          'status' => 'success',
          'message' => 'Berhasil',
        ]);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }

    public function hapus($id) {
      $data   = DB::table('sekolah')
                  ->where('id',$id)
                  ->first();

      if($data){
        DB::table('sekolah')
          ->where('id',$id)
          ->update([
            'hapus' => 1,
            'sim_update' => Auth::guard('sim')->user()->id, 
          ]);

        return response()->json([
          'status' => 'success',
          'message' => 'Berhasil',
        ]);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }

    public function detil($id) {
      $data   = DB::table('sekolah')
                  ->leftjoin('dt_bentuk_pendidikan','sekolah.bentuk_pendidikan','=','dt_bentuk_pendidikan.id')
                  ->leftjoin('dt_kepemilikan','sekolah.status_kepemilikan','=','dt_kepemilikan.id')
                  ->leftjoin('dt_bank','sekolah.bank','=','dt_bank.id')
                  ->leftjoin('dt_sertifikasi_iso','sekolah.sertifikasi_iso','=','dt_sertifikasi_iso.id')
                  ->leftjoin('dt_kurikulum','sekolah.kurikulum','=','dt_kurikulum.id')
                  ->leftjoin('simdiks','sekolah.id_simdiks','=','simdiks.id')
                  ->selectRaw('sekolah.id, 
                               sekolah.nama, 
                               sekolah.alamat, 
                               sekolah.rt, 
                               sekolah.rw, 
                               sekolah.dusun, 
                               sekolah.desa, 
                               sekolah.kecamatan, 
                               sekolah.kota, 
                               sekolah.propinsi, 
                               sekolah.kodepos, 
                               sekolah.lat, 
                               sekolah.lng, 
                               sekolah.npsn, 
                               sekolah.status,                                
                               sekolah.sk_pendirian, 
                               DATE_FORMAT(sekolah.tgl_pendirian, "%d %M %Y") as tgl_pendirian,
                               sekolah.sk_izin, 
                               DATE_FORMAT(sekolah.tgl_sk_izin, "%d %M %Y") as tgl_sk_izin,
                               sekolah.kebutuhan_khusus, 
                               sekolah.cabang, 
                               sekolah.rek_atas_nama, 
                               sekolah.no_rek, 
                               sekolah.status_bos, 
                               sekolah.waktu_penyelenggaraan, 
                               sekolah.sumber_listrik, 
                               sekolah.daya_listrik, 
                               sekolah.akses_internet, 
                               sekolah.akreditasi, 
                               sekolah.telp, 
                               sekolah.fax, 
                               sekolah.email, 
                               sekolah.web, 
                               sekolah.facebook, 
                               sekolah.twitter, 
                               sekolah.instagram, 
                               sekolah.google, 
                               sekolah.youtube, 
                               sekolah.whatsapp, 
                               sekolah.logo, 
                               sekolah.motto, 
                               sekolah.aktif, 
                               sekolah.bentuk_pendidikan as id_bentuk_pendidikan,
                               dt_bentuk_pendidikan.bentuk_pendidikan, 
                               dt_bentuk_pendidikan.singkatan, 
                               dt_kepemilikan.kepemilikan, 
                               dt_bank.bank, 
                               dt_sertifikasi_iso.iso, 
                               dt_kurikulum.kurikulum, 
                               simdiks.gelar_depan, 
                               simdiks.nama as nama_kontak, 
                               simdiks.gelar_belakang,
                               simdiks.hp as hp_kontak')
                  ->where('sekolah.id',$id)
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

    public function blokir($id) {
      $cek  = DB::table('sekolah')->where('id',$id)->first();

      if($cek){
        DB::table('sekolah')
          ->where('id',$id)
          ->update([
            'aktif' => 0,
          ]);

        $guru   = DB::table('simdiks')
                    ->where('id_sekolah',$id)
                    ->get();

        foreach($guru as $gur){
          DB::table('simdiks')
            ->where('id',$gur->id)
            ->update([
              'u_back' => $gur->username,
              'username' => $gur->username.'XXXXXXXXXXX',
              'aktif' => 0,
            ]);
        }

        return response()->json([
          'status' => 'success',
          'message' => 'Berhasil',
        ]);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }      
    }

    public function buka($id) {
      $cek  = DB::table('sekolah')->where('id',$id)->first();

      if($cek){
        DB::table('sekolah')
          ->where('id',$id)
          ->update([
            'aktif' => 1,
          ]);

        $guru   = DB::table('simdiks')
                    ->where('id_sekolah',$id)
                    ->get();

        foreach($guru as $gur){
          DB::table('simdiks')
            ->where('id',$gur->id)
            ->update([
              'username' => $gur->u_back,
              'u_back' => null,              
              'aktif' => 1,
            ]);
        }

        return response()->json([
          'status' => 'success',
          'message' => 'Berhasil',
        ]);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }      
    }

    public function edit_show($id) {
      $data   = DB::table('sekolah')
                  ->where('id',$id)
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
                      ->where('id_sekolah',$id)
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
