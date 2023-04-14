<?php

namespace App\Http\Controllers\Api\Simdik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Simdik;
use Validator;
use Imagick;
use DB;
use Crypt;
use File;
use PDF;
use Image;

class LoginController extends Controller
{
    public function __construct(){
      $this->middleware('auth:simdik', ['except' => ['login','status','getSekolah']]);
    }

    public function login(Request $request){
      $validator = Validator::make($request->all(), [
        'username' => 'required|string',
        'password' => 'required|string',
      ]);
      
      if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
      }

      if (! $token = Auth::guard('simdik')->attempt($validator->validated())) {
        return response()->json(['error' => 'Tidak dapat melakukan validasi.'], 401);
      }

      return $this->createNewToken($token);
    }

    public function status(){
      if(Auth::guard('simdik')->user()){
        return response()->json(true);
      } else {
        return response()->json(false);
      }
    }

    public function getSekolah(request $request){
      $sekolah  = DB::table('sekolah')
                    ->where('id',$request->id_sekolah)
                    ->first();

      return response()->json($sekolah);
    }

    public function logout(Request $request){
      Auth::guard('simdik')->logout();
      return response()->json(['status' => 'success'], 200);
    }

    public function refresh() {
      return $this->createNewToken(Auth::guard('simdik')->refresh());
    }
    
    public function profil(){
      $user   = DB::table('simdiks')
                  ->selectRaw('simdiks.id, 
                               simdiks.id_sekolah, 
                               CONCAT(IF(simdiks.gelar_depan IS NOT NULL,CONCAT(simdiks.gelar_depan," "),""),
                               IF(simdiks.gelar_belakang IS NOT NULL AND simdiks.gelar_belakang <> "",CONCAT(UPPER(simdiks.nama),", ",simdiks.gelar_belakang),UPPER(simdiks.nama))) as nama,
                               simdiks.foto, 
                               simdiks.administrator, 
                               simdiks.kepsek, 
                               simdiks.waka, 
                               simdiks.tu, 
                               simdiks.guru, 
                               simdiks.guru_bp, 
                               simdiks.perpus, 
                               simdiks.web_operator, 
                               simdiks.ppdb, 
                               simdiks.bel_sekolah, 
                               simdiks.email, 
                               simdiks.username,
                               IF((SELECT COUNT(dt_kelas_sekolah.id)
                                FROM dt_kelas_sekolah
                                WHERE dt_kelas_sekolah.id_sekolah = simdiks.id_sekolah
                                AND dt_kelas_sekolah.id_simdik = simdiks.id) > 0,1,0) as walikelas')
                  ->where('simdiks.id',Auth::guard('simdik')->user()->id)
                  ->first();

      return response()->json($user);
    }

    public function profil_edit(){
      $agama        = DB::table('dt_agama')
                        ->where('hapus',0)
                        ->orderby('agama')
                        ->get();

      $pendidikan   = DB::table('dt_pendidikan')
                        ->where('hapus',0)
                        ->orderby('id')
                        ->get();

      $propinsi     = DB::table('dt_wilayah')
                        ->whereNotNull('no_prop')
                        ->whereNull('no_kab')
                        ->whereNull('no_kec')
                        ->whereNull('no_kel')
                        ->orderby('nama')
                        ->get();

      $kota         = DB::table('dt_wilayah')
                        ->where('no_prop',Auth::guard('simdik')->user()->no_prop)
                        ->whereNotNull('no_kab')
                        ->whereNull('no_kec')
                        ->whereNull('no_kel')
                        ->orderby('nama')
                        ->get();

      $kecamatan    = DB::table('dt_wilayah')
                        ->where('no_prop',Auth::guard('simdik')->user()->no_prop)
                        ->where('no_kab',Auth::guard('simdik')->user()->no_kab)
                        ->whereNotNull('no_kec')
                        ->whereNull('no_kel')
                        ->orderby('nama')
                        ->get();

      $desa         = DB::table('dt_wilayah')
                        ->where('no_prop',Auth::guard('simdik')->user()->no_prop)
                        ->where('no_kab',Auth::guard('simdik')->user()->no_kab)
                        ->where('no_kec',Auth::guard('simdik')->user()->no_kec)
                        ->whereNotNull('no_kel')
                        ->orderby('nama')
                        ->get();

      return response()->json([
        'data' => Auth::guard('simdik')->user(),
        'prop' => $propinsi,
        'kab' => $kota,
        'kec' => $kecamatan,
        'kel' => $desa,
        'agama' => $agama,
        'pendidikan' => $pendidikan,
      ]);
    }

    public function profil_simpan(request $request){
      $validator = Validator::make($request->all(), [
        'username' => 'required|String|min:6|unique:simdiks,username,'.Auth::guard('simdik')->user()->id,
        'hp' => 'required|string|unique:simdiks,hp,'.Auth::guard('simdik')->user()->id,
        'email' => 'required|email|unique:simdiks,email,'.Auth::guard('simdik')->user()->id,
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
      }

      DB::table('simdiks')
        ->where('id',Auth::guard('simdik')->user()->id)
        ->update([
          'id' => $request->id, 
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
          'desa' => $this->nama_kelurahan($request->no_prop, $request->no_kab, $request->no_kec, $request->no_kel),
          'kecamatan' => $this->nama_kecamatan($request->no_prop, $request->no_kab, $request->no_kec),
          'kota' => $this->nama_kota($request->no_prop, $request->no_kab),
          'propinsi' => $this->nama_propinsi($request->no_prop),
          'kodepos' => $request->kodepos, 
          'nip' => $request->nip, 
          'jabatan' => $request->jabatan, 
          'pangkat' => $request->pangkat, 
          'golongan' => $request->golongan, 
          'telp' => $request->telp, 
          'hp' => $request->hp, 
          'temp_lahir' => $request->temp_lahir, 
          'tgl_lahir' => $request->tgl_lahir ? date('Y-m-d', strtotime($request->tgl_lahir)) : null,
          'kelamin' => $request->kelamin, 
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
          'template' => $request->template, 
          'lat' => $request->lat, 
          'lng' => $request->lng, 
          'email' => $request->email, 
          'username' => $request->username, 
          'simdiks_update' => Auth::guard('simdik')->user()->id, 
        ]);

      if($request->foto){
        DB::table('simdiks')
          ->where('id',Auth::guard('simdik')->user()->id)
          ->update([
            'foto' => $request->foto,
          ]);
      }

      $data   = DB::table('simdiks')->where('id',Auth::guard('simdik')->user()->id)->first();

      return response()->json($data, 200);
    }

    public function profil_password(request $request){
      $validator = Validator::make($request->all(), [
        'password' => 'required|string|min:6',
        'konfirmasi' => 'required|string|min:6',
      ]);
      
      if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
      }

      if($request->password === $request->konfirmasi){
        DB::table('simdiks')
          ->where('id',Auth::guard('simdik')->user()->id)
          ->update([
            'password' => bcrypt($request->password),
          ]);
      
        return response()->json(['status' => 'success'], 200);
      } else {
        return response()->json([
          'status' => 'success',
          'message' => 'Konfirmasi password tidak sama.'
        ], 201);
      }      
    }

    protected function createNewToken($token){
      return response()->json([
        'access_token' => $token,
        'expires_in' => Auth::guard('simdik')->factory()->getTTL() * 60,
      ]);
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
