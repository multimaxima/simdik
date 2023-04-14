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

class GuruController extends Controller
{
    public function __construct(){
      $this->middleware('auth:simdik');
    }

    public function index(){
      $data     = DB::table('simdiks')
                    ->orderby('simdiks.nama')
                    ->where('simdiks.hapus',0)
                    ->where('simdiks.id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
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
        'nama' => 'required|String',
        'id_sekolah' => 'required',
        'username' => 'required|String|min:6|unique:simdiks',
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
      }

      DB::table('simdiks')
        ->insert([
          'id_sekolah' => $request->id_sekolah,
          'gelar_depan' => $request->gelar_depan,
          'nama' => $request->nama,
          'gelar_belakang' => $request->gelar_belakang,
          'alamat' => $request->alamat,
          'rt' => $request->rt,
          'rw' => $request->rw,
          'dusun' => $request->dusun,
          'no_prop' => $request->no_prop,
          'no_kab' => $request->no_kab,
          'no_kec' => $request->no_kec,
          'no_kel' => $request->no_kel,
          'propinsi' => $this->nama_propinsi($request->no_prop),
          'kota' => $this->nama_kota($request->no_prop, $request->no_kab),
          'kecamatan' => $this->nama_kecamatan($request->no_prop, $request->no_kab, $request->no_kec),
          'desa' => $this->nama_kelurahan($request->no_prop, $request->no_kab, $request->no_kec, $request->no_kel),
          'kodepos' => $request->kodepos,
          'nip' => $request->nip,
          'jabatan' => $request->jabatan,
          'pangkat' => $request->pangkat,
          'golongan' => $request->golongan,
          'hp' => $request->hp,
          'temp_lahir' => $request->temp_lahir,
          'tgl_lahir' => date('Y-m-d', strtotime($request->tgl_lahir)),
          'kelamin' => $request->kelamin,
          'foto' => $request->foto,
          'facebook' => $request->facebook,
          'instagram' => $request->instagram,
          'twitter' => $request->twitter,
          'google' => $request->google,
          'whatsapp' => $request->whatsapp,
          'youtube' => $request->youtube,
          'id_agama' => $request->id_agama,
          'id_pendidikan' => $request->id_pendidikan,
          'sekolah' => $request->sekolah,
          'lulusan' => $request->lulusan,
          'angkatan' => $request->angkatan,
          'tentang' => $request->tentang,
          'prestasi' => $request->prestasi,
          'visimisi' => $request->visimisi,
          'lat' => $request->lat,
          'lng' => $request->lng,
          'administrator' => $request->administrator,
          'kepsek' => $request->kepsek,
          'waka' => $request->waka,
          'tu' => $request->tu,
          'guru' => $request->guru,
          'guru_bp' => $request->guru_bp,
          'perpus' => $request->perpus,
          'web_operator' => $request->web_operator,
          'ppdb' => $request->ppdb,
          'bel_sekolah' => $request->bel_sekolah,
          'email' => $request->email,
          'username' => $request->username,
          'password' => bcrypt('123456'),
          'simdiks_create' => Auth::guard('simdik')->user()->id,
          'simdiks_update' => Auth::guard('simdik')->user()->id,
        ]);

      $data   = DB::table('simdiks')
                  ->where('nama',$request->nama)
                  ->orderby('id','desc')
                  ->first();

      return response()->json($data);
    }

    public function edit(request $request){
      $cek  = DB::table('simdiks')->where('id',$request->id)->first();

      if($cek){
        $validator = Validator::make($request->all(), [
          'nama' => 'required|String',
          'id_sekolah' => 'required',
          'username' => 'required|String|min:5|unique:simdiks,username,'.$request->id,
        ]);

        if ($validator->fails()) {
          return response()->json($validator->errors(), 422);
        }

        if($request->foto){
          $foto = $request->foto;
        } else {
          $foto = $cek->foto;
        }

        DB::table('simdiks')
          ->where('id',$request->id)
          ->update([
            'id_sekolah' => $request->id_sekolah,
            'gelar_depan' => $request->gelar_depan,
            'nama' => $request->nama,
            'gelar_belakang' => $request->gelar_belakang,
            'alamat' => $request->alamat,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'dusun' => $request->dusun,
            'no_prop' => $request->no_prop,
            'no_kab' => $request->no_kab,
            'no_kec' => $request->no_kec,
            'no_kel' => $request->no_kel,
            'propinsi' => $this->nama_propinsi($request->no_prop),
            'kota' => $this->nama_kota($request->no_prop, $request->no_kab),
            'kecamatan' => $this->nama_kecamatan($request->no_prop, $request->no_kab, $request->no_kec),
            'desa' => $this->nama_kelurahan($request->no_prop, $request->no_kab, $request->no_kec, $request->no_kel),
            'kodepos' => $request->kodepos,
            'nip' => $request->nip,
            'jabatan' => $request->jabatan,
            'pangkat' => $request->pangkat,
            'golongan' => $request->golongan,
            'hp' => $request->hp,
            'temp_lahir' => $request->temp_lahir,
            'tgl_lahir' => date('Y-m-d', strtotime($request->tgl_lahir)),
            'kelamin' => $request->kelamin,
            'foto' => $foto,
            'facebook' => $request->facebook,
            'instagram' => $request->instagram,
            'twitter' => $request->twitter,
            'google' => $request->google,
            'whatsapp' => $request->whatsapp,
            'youtube' => $request->youtube,
            'id_agama' => $request->id_agama,
            'id_pendidikan' => $request->id_pendidikan,
            'sekolah' => $request->sekolah,
            'lulusan' => $request->lulusan,
            'angkatan' => $request->angkatan,
            'tentang' => $request->tentang,
            'prestasi' => $request->prestasi,
            'visimisi' => $request->visimisi,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'administrator' => $request->administrator,
            'kepsek' => $request->kepsek,
            'waka' => $request->waka,
            'tu' => $request->tu,
            'guru' => $request->guru,
            'guru_bp' => $request->guru_bp,
            'perpus' => $request->perpus,
            'web_operator' => $request->web_operator,
            'ppdb' => $request->ppdb,
            'bel_sekolah' => $request->bel_sekolah,
            'email' => $request->email,
            'username' => $request->username,
            'sims_update' => Auth::guard('simdik')->user()->id,
          ]);

        $data   = DB::table('simdiks')->where('id',$request->id)->first();

        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }

    public function hapus($id) {
      $cek  = DB::table('simdiks')->where('id',$id)->first();

      if($cek){
        DB::table('simdiks')
          ->where('id',$id)
          ->update([
            'hapus' => 1,
            'simdiks_update' => Auth::guard('simdik')->user()->id,
          ]);

        return response()->json(['status' => 'success'], 200);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }

    public function detil(request $request) {
      $data         = DB::table('simdiks')->where('id',$request->id)->first();

      $agama        = DB::table('dt_agama')
                        ->where('hapus',0)
                        ->orderby('id')
                        ->get();

      $pendidikan   = DB::table('dt_pendidikan')
                        ->where('hapus',0)
                        ->orderby('id')
                        ->get();

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

      if($data){
        return response()->json([
          'data' => $data,
          'agama' => $agama,
          'pendidikan' => $pendidikan,
          'propinsi' => $propinsi,
          'kota' => $kota,
          'kecamatan' => $kecamatan,
          'desa' => $desa,
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

    public function reset(request $request){
      $cek  = DB::table('simdiks')->where('id',$request->id)->first();

      if($cek){
        DB::table('simdiks')
          ->where('id',$request->id)
          ->update([
            'password' => bcrypt('123456'),
          ]);

        return response()->json(['status' => 'success'],200);  
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
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

      $data   = DB::table('simdiks')
                  ->when($id, function ($query) use ($id) {
                      return $query->whereNot('simdiks.id',$id);
                    })
                  ->when($username, function ($query) use ($username) {
                      return $query->where('simdiks.username',$username);
                    })
                  ->when($email, function ($query) use ($email) {
                      return $query->where('simdiks.email',$email);
                    })
                  ->when($hp, function ($query) use ($hp) {
                      return $query->where('simdiks.hp',$hp);
                    })                  
                  ->count();

      if($data) {
        return response()->json(false);
      } else {
        return response()->json(true);
      }
    }

    public function detil_perpus(request $request){
      $data     = DB::table('simdiks')
                    ->orderby('simdiks.nama')
                    ->where('simdiks.hapus',0)
                    ->where('simdiks.id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                    ->where('simdiks.id',$request->id)
                    ->first();

      if($data) {
        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
        ], 401);
      }
    }
}
