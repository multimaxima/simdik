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

class SiswaController extends Controller
{
    public function __construct(){
      $this->middleware('auth:sim');
    }

    public function index(request $request){
      $data   = DB::table('siswas')                  
                  ->leftjoin('sekolah','siswas.id_sekolah','=','sekolah.id')
                  ->leftjoin('dt_kelas_sekolah','siswas.id_kelas_sekolah','=','dt_kelas_sekolah.id')
                  ->leftjoin('dt_kelas','dt_kelas_sekolah.id_kelas','=','dt_kelas.id')
                  ->leftjoin('dt_jurusan','dt_kelas_sekolah.id_jurusan','=','dt_jurusan.id')
                  ->selectRaw('siswas.id,
                               siswas.nama,
                               siswas.alamat_tinggal,
                               siswas.kota_tinggal,
                               siswas.propinsi_tinggal,
                               siswas.nis,
                               siswas.hp,
                               sekolah.nama as sekolah_nama,
                               sekolah.kota as sekolah_kota,
                               dt_kelas.tingkat,
                               dt_jurusan.kode,
                               dt_kelas_sekolah.sub_kelas')
                  ->where('siswas.id_sekolah',$request->id_sekolah)
                  ->orderby('siswas.nama')
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
      $validator = Validator::make($request->all(), [
        'nama' => 'required|String',
        'id_sekolah' => 'required',
        'email' => 'required|email|unique:siswas',
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
      }

      $username   = $request->nis.str_pad($request->id_sekolah,3,'0',STR_PAD_LEFT);

      DB::table('siswas')
        ->insert([
          'id_sekolah' => $request->id_sekolah, 
          'nama' => $request->nama, 
          'panggilan' => $request->panggilan, 
          'alamat_tinggal' => $request->alamat_tinggal, 
          'dusun_tinggal' => $request->dusun_tinggal, 
          'rt_tinggal' => $request->rt_tinggal, 
          'rw_tinggal' => $request->rw_tinggal, 
          'no_prop_tinggal' => $request->no_prop_tinggal, 
          'no_kab_tinggal' => $request->no_kab_tinggal, 
          'no_kec_tinggal' => $request->no_kec_tinggal, 
          'no_kel_tinggal' => $request->no_kel_tinggal, 
          'propinsi_tinggal' => $this->nama_propinsi($request->no_prop_tinggal),
          'kota_tinggal' => $this->nama_kota($request->no_prop_tinggal, $request->no_kab_tinggal),
          'kecamatan_tinggal' => $this->nama_kecamatan($request->no_prop_tinggal, $request->no_kab_tinggal, $request->no_kec_tinggal),
          'desa_tinggal' => $this->nama_kelurahan($request->no_prop_tinggal, $request->no_kab_tinggal, $request->no_kec_tinggal, $request->no_kel_tinggal),
          'kodepos_tinggal' => $request->kodepos_tinggal, 
          'lat_tinggal' => $request->lat_tinggal, 
          'lng_tinggal' => $request->lng_tinggal, 
          'status_tinggal' => $request->status_tinggal, 
          'jarak_sekolah' => $request->jarak_sekolah, 
          'jarak_rumah' => $request->jarak_rumah, 
          'alamat_rumah' => $request->alamat_rumah, 
          'dusun_rumah' => $request->dusun_rumah, 
          'rt_rumah' => $request->rt_rumah, 
          'rw_rumah' => $request->rw_rumah, 
          'no_prop_rumah' => $request->no_prop_rumah, 
          'no_kab_rumah' => $request->no_kab_rumah, 
          'no_kec_rumah' => $request->no_kec_rumah, 
          'no_kel_rumah' => $request->no_kel_rumah, 
          'propinsi_rumah' => $this->nama_propinsi($request->no_prop_rumah),
          'kota_rumah' => $this->nama_kota($request->no_prop_rumah, $request->no_kab_rumah),
          'kecamatan_rumah' => $this->nama_kecamatan($request->no_prop_rumah, $request->no_kab_rumah, $request->no_kec_rumah),
          'desa_rumah' => $this->nama_kelurahan($request->no_prop_rumah, $request->no_kab_rumah, $request->no_kec_rumah, $request->no_kel_rumah),
          'kodepos_rumah' => $request->kodepos_rumah, 
          'lat_rumah' => $request->lat_rumah, 
          'lng_rumah' => $request->lng_rumah, 
          'nis' => $request->nis, 
          'nisn' => $request->nisn, 
          'telp' => $request->telp, 
          'hp' => $request->hp, 
          'whatsapp' => $request->whatsapp, 
          'temp_lahir' => $request->temp_lahir, 
          'tgl_lahir' => date('Y-m-d', strtotime($request->tgl_lahir)),
          'kelamin' => $request->kelamin, 
          'foto' => $request->foto, 
          'id_kelas' => $request->id_kelas, 
          'tgl_masuk' => date('Y-m-d', strtotime($request->tgl_masuk)), 
          'asal_sekolah' => $request->asal_sekolah, 
          'tgl_sttb' => date('Y-m-d', strtotime($request->tgl_sttb)), 
          'no_sttb' => $request->no_sttb, 
          'tgl_stl' => date('Y-m-d', strtotime($request->tgl_stl)), 
          'no_stl' => $request->no_stl, 
          'lama_belajar' => $request->lama_belajar, 
          'id_kelas_terima' => $request->id_kelas_terima, 
          'asal_pindahan' => $request->asal_pindahan, 
          'alasan_pindahan' => $request->alasan_pindahan, 
          'id_agama' => $request->id_agama, 
          'wna' => $request->wna, 
          'negara' => $request->negara, 
          'anak_ke' => $request->anak_ke, 
          'sdr_kandung' => $request->sdr_kandung, 
          'sdr_tiri' => $request->sdr_tiri, 
          'sdr_angkat' => $request->sdr_angkat, 
          'yatim' => $request->yatim, 
          'id_bahasa' => $request->id_bahasa, 
          'goldar' => $request->goldar, 
          'sakit' => $request->sakit, 
          'kelainan' => $request->kelainan, 
          'tinggi' => $request->tinggi, 
          'berat' => $request->berat, 
          'kesenian' => $request->kesenian, 
          'olahraga' => $request->olahraga, 
          'organisasi' => $request->organisasi, 
          'lain' => $request->lain, 
          'beasiswa' => $request->beasiswa, 
          'tgl_meninggalkan' => date('Y-m-d', strtotime($request->tgl_meninggalkan)), 
          'pindah_ke' => $request->pindah_ke, 
          'alasan_pindah_ke' => $request->alasan_pindah_ke, 
          'no_ijasah_lulus' => $request->no_ijasah_lulus, 
          'no_tanda_lulus' => $request->no_tanda_lulus, 
          'nilai_rata' => $request->nilai_rata, 
          'melanjutkan_ke' => $request->melanjutkan_ke, 
          'bekerja_di' => $request->bekerja_di, 
          'nik' => $request->nik, 
          'no_kk' => $request->no_kk, 
          'fc_akte' => $request->fc_akte, 
          'fc_kk' => $request->fc_kk, 
          'fc_ktp' => $request->fc_ktp, 
          'fc_nisn' => $request->fc_nisn, 
          'fc_skl' => $request->fc_skl, 
          'email' => $request->email, 
          'username' => $username, 
          'password' => bcrypt('123456'), 
          'sim_create' => Auth::guard('sim')->user()->id, 
          'sim_update' => Auth::guard('sim')->user()->id, 
        ]);

      $data   = DB::table('siswas')
                  ->where('id_sekolah',$request->id_sekolah)
                  ->where('nis',$request->nis)
                  ->orderby('id','desc')
                  ->first();

      return response()->json($data);
    }

    public function edit(request $request){
      $data   = DB::table('siswas')->where('id',$request->id)->first();

      if($data){
        $validator = Validator::make($request->all(), [
          'nama' => 'required|String',
          'id_sekolah' => 'required',
          'email' => 'required|email|unique:siswas,email,'.$request->id,
        ]);

        if ($validator->fails()) {
          return response()->json($validator->errors(), 422);
        }

        if($request->foto){
          $foto   = $request->foto;
        } else {
          $foto   = null;
        }

        DB::table('siswas')
          ->where('id',$request->id)
          ->update([
            'id_sekolah' => $request->id_sekolah, 
            'nama' => $request->nama, 
            'panggilan' => $request->panggilan, 
            'alamat_tinggal' => $request->alamat_tinggal, 
            'dusun_tinggal' => $request->dusun_tinggal, 
            'rt_tinggal' => $request->rt_tinggal, 
            'rw_tinggal' => $request->rw_tinggal, 
            'no_prop_tinggal' => $request->no_prop_tinggal, 
            'no_kab_tinggal' => $request->no_kab_tinggal, 
            'no_kec_tinggal' => $request->no_kec_tinggal, 
            'no_kel_tinggal' => $request->no_kel_tinggal, 
            'propinsi_tinggal' => $this->nama_propinsi($request->no_prop_tinggal),
            'kota_tinggal' => $this->nama_kota($request->no_prop_tinggal, $request->no_kab_tinggal),
            'kecamatan_tinggal' => $this->nama_kecamatan($request->no_prop_tinggal, $request->no_kab_tinggal, $request->no_kec_tinggal),
            'desa_tinggal' => $this->nama_kelurahan($request->no_prop_tinggal, $request->no_kab_tinggal, $request->no_kec_tinggal, $request->no_kel_tinggal),
            'kodepos_tinggal' => $request->kodepos_tinggal, 
            'lat_tinggal' => $request->lat_tinggal, 
            'lng_tinggal' => $request->lng_tinggal, 
            'status_tinggal' => $request->status_tinggal, 
            'jarak_sekolah' => $request->jarak_sekolah, 
            'jarak_rumah' => $request->jarak_rumah, 
            'alamat_rumah' => $request->alamat_rumah, 
            'dusun_rumah' => $request->dusun_rumah, 
            'rt_rumah' => $request->rt_rumah, 
            'rw_rumah' => $request->rw_rumah, 
            'no_prop_rumah' => $request->no_prop_rumah, 
            'no_kab_rumah' => $request->no_kab_rumah, 
            'no_kec_rumah' => $request->no_kec_rumah, 
            'no_kel_rumah' => $request->no_kel_rumah, 
            'propinsi_rumah' => $this->nama_propinsi($request->no_prop_rumah),
            'kota_rumah' => $this->nama_kota($request->no_prop_rumah, $request->no_kab_rumah),
            'kecamatan_rumah' => $this->nama_kecamatan($request->no_prop_rumah, $request->no_kab_rumah, $request->no_kec_rumah),
            'desa_rumah' => $this->nama_kelurahan($request->no_prop_rumah, $request->no_kab_rumah, $request->no_kec_rumah, $request->no_kel_rumah),
            'kodepos_rumah' => $request->kodepos_rumah, 
            'lat_rumah' => $request->lat_rumah, 
            'lng_rumah' => $request->lng_rumah, 
            'nis' => $request->nis, 
            'nisn' => $request->nisn, 
            'telp' => $request->telp, 
            'hp' => $request->hp, 
            'whatsapp' => $request->whatsapp, 
            'temp_lahir' => $request->temp_lahir, 
            'tgl_lahir' => date('Y-m-d', strtotime($request->tgl_lahir)), 
            'kelamin' => $request->kelamin, 
            'foto' => $foto, 
            'id_kelas' => $request->id_kelas, 
            'tgl_masuk' => date('Y-m-d', strtotime($request->tgl_masuk)), 
            'asal_sekolah' => $request->asal_sekolah, 
            'tgl_sttb' => date('Y-m-d', strtotime($request->tgl_sttb)), 
            'no_sttb' => $request->no_sttb, 
            'tgl_stl' => date('Y-m-d', strtotime($request->tgl_stl)), 
            'no_stl' => $request->no_stl, 
            'lama_belajar' => $request->lama_belajar, 
            'id_kelas_terima' => $request->id_kelas_terima, 
            'asal_pindahan' => $request->asal_pindahan, 
            'alasan_pindahan' => $request->alasan_pindahan, 
            'id_agama' => $request->id_agama, 
            'wna' => $request->wna, 
            'negara' => $request->negara, 
            'anak_ke' => $request->anak_ke, 
            'sdr_kandung' => $request->sdr_kandung, 
            'sdr_tiri' => $request->sdr_tiri, 
            'sdr_angkat' => $request->sdr_angkat, 
            'yatim' => $request->yatim, 
            'id_bahasa' => $request->id_bahasa, 
            'goldar' => $request->goldar, 
            'sakit' => $request->sakit, 
            'kelainan' => $request->kelainan, 
            'tinggi' => $request->tinggi, 
            'berat' => $request->berat, 
            'kesenian' => $request->kesenian, 
            'olahraga' => $request->olahraga, 
            'organisasi' => $request->organisasi, 
            'lain' => $request->lain, 
            'beasiswa' => $request->beasiswa, 
            'tgl_meninggalkan' => date('Y-m-d', strtotime($request->tgl_meninggalkan)), 
            'pindah_ke' => $request->pindah_ke, 
            'alasan_pindah_ke' => $request->alasan_pindah_ke, 
            'no_ijasah_lulus' => $request->no_ijasah_lulus, 
            'no_tanda_lulus' => $request->no_tanda_lulus, 
            'nilai_rata' => $request->nilai_rata, 
            'melanjutkan_ke' => $request->melanjutkan_ke, 
            'bekerja_di' => $request->bekerja_di, 
            'nik' => $request->nik, 
            'no_kk' => $request->no_kk, 
            'fc_akte' => $request->fc_akte, 
            'fc_kk' => $request->fc_kk, 
            'fc_ktp' => $request->fc_ktp, 
            'fc_nisn' => $request->fc_nisn, 
            'fc_skl' => $request->fc_skl, 
            'email' => $request->email, 
            'sim_update' => Auth::guard('sim')->user()->id, 
          ]);

        $data   = DB::table('siswas')->where('id',$request->id)->first();

        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }

    public function hapus($id) {
      $data   = DB::table('siswas')
                  ->where('id',$id)
                  ->first();

      if($data){
        DB::table('siswas')
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
      $data   = DB::table('siswas')
                  ->where('id',$request->id)
                  ->first();

      $sekolah      = DB::table('sekolah')
                        ->orderby('nama')
                        ->where('hapus',0)
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

      $agama        = DB::table('dt_agama')
                        ->where('hapus',0)
                        ->orderby('id')
                        ->get();

      $bahasa       = DB::table('dt_bahasa')
                        ->where('hapus',0)
                        ->orderby('id')
                        ->get();

      if($data){
        return response()->json([
          'data' => $data,
          'sekolah' => $sekolah,
          'propinsi' => $propinsi,
          'kota' => $kota,
          'kecamatan' => $kecamatan,
          'desa' => $desa,
          'agama' => $agama,
          'bahasa' => $bahasa,
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

    public function reset($id){
      $cek  = DB::table('siswas')->where('id',$id)->first();

      if($cek){
        DB::table('siswas')
          ->where('id',$id)
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

      $data   = DB::table('siswas')
                  ->when($id, function ($query) use ($id) {
                      return $query->whereNot('siswas.id',$id);
                    })
                  ->when($email, function ($query) use ($email) {
                      return $query->where('siswas.email',$email);
                    })
                  ->count();

      if($data) {
        return response()->json(false);
      } else {
        return response()->json(true);
      }
    }
}
