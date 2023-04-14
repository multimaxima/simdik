<?php

namespace App\Http\Controllers\Api\Simdik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Imports\SiswaImport;
use Validator;
use DB;
use Crypt;
use Hash;
use PDF;
use Auth;
use Excel;

class SiswaController extends Controller
{
    public function __construct(){
      $this->middleware('auth:simdik');
    }

    public function index(request $request){
      if($request->id_kelas){        
        $id_kelas = $request->id_kelas;
      } else {
        $id_kelas = '';
      }

      $data     = DB::table('siswas')
                    ->leftjoin('dt_kelas_sekolah','siswas.id_kelas_sekolah','=','dt_kelas_sekolah.id')
                    ->leftjoin('dt_agama','siswas.id_agama','=','dt_agama.id')
                    ->leftjoin('dt_bahasa','siswas.id_bahasa','=','dt_bahasa.id')
                    ->selectRaw('siswas.id, 
                                 siswas.id_sekolah, 
                                 siswas.nama, 
                                 siswas.panggilan, 
                                 siswas.alamat_tinggal, 
                                 siswas.dusun_tinggal, 
                                 siswas.rt_tinggal, 
                                 siswas.rw_tinggal, 
                                 siswas.propinsi_tinggal, 
                                 siswas.kota_tinggal, 
                                 siswas.kecamatan_tinggal, 
                                 siswas.desa_tinggal, 
                                 siswas.kodepos_tinggal, 
                                 siswas.status_tinggal, 
                                 siswas.jarak_sekolah, 
                                 siswas.tinggalrumah, 
                                 siswas.jarak_rumah, 
                                 siswas.alamat_rumah, 
                                 siswas.dusun_rumah, 
                                 siswas.rt_rumah, 
                                 siswas.rw_rumah, 
                                 siswas.propinsi_rumah, 
                                 siswas.kota_rumah, 
                                 siswas.kecamatan_rumah, 
                                 siswas.desa_rumah, 
                                 siswas.kodepos_rumah, 
                                 siswas.nis, 
                                 siswas.nisn, 
                                 siswas.telp, 
                                 siswas.hp, 
                                 siswas.whatsapp, 
                                 siswas.temp_lahir, 
                                 DATE_FORMAT(siswas.tgl_lahir,"%d %M %Y") as tgl_lahir, 
                                 siswas.kelamin, 
                                 siswas.foto, 
                                 siswas.id_kelas_sekolah, 
                                 dt_kelas_sekolah.kelas,
                                 dt_kelas_sekolah.kode,
                                 dt_kelas_sekolah.sub_kelas,
                                 DATE_FORMAT(siswas.tgl_masuk,"%d %M %Y") as tgl_masuk, 
                                 siswas.asal_sekolah, 
                                 DATE_FORMAT(siswas.tgl_sttb,"%d %M %Y") as tgl_sttb, 
                                 siswas.no_sttb, 
                                 DATE_FORMAT(siswas.tgl_stl,"%d %M %Y") as tgl_stl, 
                                 siswas.no_stl, 
                                 siswas.lama_belajar, 
                                 siswas.id_kelas_terima, 
                                 siswas.asal_pindahan, 
                                 siswas.alasan_pindahan, 
                                 siswas.id_agama, 
                                 dt_agama.agama,
                                 siswas.wna, 
                                 siswas.negara, 
                                 siswas.anak_ke, 
                                 siswas.sdr_kandung, 
                                 siswas.sdr_tiri, 
                                 siswas.sdr_angkat, 
                                 siswas.yatim, 
                                 siswas.id_bahasa, 
                                 dt_bahasa.bahasa,
                                 siswas.goldar, 
                                 siswas.sakit, 
                                 siswas.kelainan, 
                                 siswas.tinggi, 
                                 siswas.berat, 
                                 siswas.kesenian, 
                                 siswas.olahraga, 
                                 siswas.organisasi, 
                                 siswas.lain, 
                                 siswas.beasiswa, 
                                 DATE_FORMAT(siswas.tgl_meninggalkan,"%d %M %Y") as tgl_meninggalkan, 
                                 siswas.pindah_ke, 
                                 siswas.alasan_pindah_ke, 
                                 siswas.no_ijasah_lulus, 
                                 siswas.no_tanda_lulus, 
                                 siswas.nilai_rata, 
                                 siswas.melanjutkan_ke, 
                                 siswas.bekerja_di, 
                                 siswas.nik, 
                                 siswas.no_kk, 
                                 siswas.email, 
                                 siswas.username')
                    ->orderby('siswas.nama')
                    ->where('siswas.hapus',0)
                    ->where('siswas.id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                    ->when($id_kelas, function ($query) use($id_kelas) {
                        return $query->where('siswas.id_kelas_sekolah',$id_kelas);
                      })
                    ->get();

      $kelas  = DB::table('dt_kelas_sekolah')
                  ->where('id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->where('hapus',0)
                  ->orderby('kelas')
                  ->orderby('kode')
                  ->orderby('sub_kelas')
                  ->get();

      if($data) {
        return response()->json([
          'data' => $data,
          'kelas' => $kelas,
        ]);
      } else {
        return response()->json([
          'status' => 'error',
        ], 401);
      }
    }

    public function baru(request $request){
      $validator = Validator::make($request->all(), [
        'siswa.nama' => 'required|String',
        'siswa.id_sekolah' => 'required',
        'siswa.email' => 'required|email|unique:siswas,email',
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
      }

      $cek  = DB::table('siswas')
                ->where('nis',$request->siswa['nis'])
                ->where('id_sekolah',$request->siswa['id_sekolah'])
                ->first();

      if($cek){
        return response()->json([
          'error' => 'errors',
          'message' => 'NIS sudah terdaftar',
        ], 422);
      } else {
        $username   = $request->siswa['nis'].str_pad($request->siswa['id_sekolah'],3,'0',STR_PAD_LEFT);

        DB::table('siswas')
          ->insert([
            'id_sekolah' => $request->siswa['id_sekolah'],
            'nama' => $request->siswa['nama'],
            'panggilan' => $request->siswa['panggilan'],
            'alamat_tinggal' => $request->siswa['alamat_tinggal'],
            'dusun_tinggal' => $request->siswa['dusun_tinggal'],
            'rt_tinggal' => $request->siswa['rt_tinggal'],
            'rw_tinggal' => $request->siswa['rw_tinggal'],
            'no_prop_tinggal' => $request->siswa['no_prop_tinggal'],
            'no_kab_tinggal' => $request->siswa['no_kab_tinggal'],
            'no_kec_tinggal' => $request->siswa['no_kec_tinggal'],
            'no_kel_tinggal' => $request->siswa['no_kel_tinggal'],
            'propinsi_tinggal' => $this->nama_propinsi($request->siswa['no_prop_tinggal']),
            'kota_tinggal' => $this->nama_kota($request->siswa['no_prop_tinggal'], $request->siswa['no_kab_tinggal']),
            'kecamatan_tinggal' => $this->nama_kecamatan($request->siswa['no_prop_tinggal'], $request->siswa['no_kab_tinggal'], $request->siswa['no_kec_tinggal']),
            'desa_tinggal' => $this->nama_kelurahan($request->siswa['no_prop_tinggal'], $request->siswa['no_kab_tinggal'], $request->siswa['no_kec_tinggal'], $request->siswa['no_kel_tinggal']),
            'kodepos_tinggal' => $request->siswa['kodepos_tinggal'],
            'lat_tinggal' => $request->siswa['lat_tinggal'],
            'lng_tinggal' => $request->siswa['lng_tinggal'],
            'status_tinggal' => $request->siswa['status_tinggal'],
            'jarak_sekolah' => $request->siswa['jarak_sekolah'],
            'tinggalrumah' => $request->siswa['tinggalrumah'],
            'jarak_rumah' => $request->siswa['jarak_rumah'],
            'alamat_rumah' => $request->siswa['alamat_rumah'],
            'dusun_rumah' => $request->siswa['dusun_rumah'],
            'rt_rumah' => $request->siswa['rt_rumah'],
            'rw_rumah' => $request->siswa['rw_rumah'],
            'no_prop_rumah' => $request->siswa['no_prop_rumah'],
            'no_kab_rumah' => $request->siswa['no_kab_rumah'],
            'no_kec_rumah' => $request->siswa['no_kec_rumah'],
            'no_kel_rumah' => $request->siswa['no_kel_rumah'],
            'propinsi_rumah' => $this->nama_propinsi($request->siswa['no_prop_rumah']),
            'kota_rumah' => $this->nama_kota($request->siswa['no_prop_rumah'], $request->siswa['no_kab_rumah']),
            'kecamatan_rumah' => $this->nama_kecamatan($request->siswa['no_prop_rumah'], $request->siswa['no_kab_rumah'], $request->siswa['no_kec_rumah']),
            'desa_rumah' => $this->nama_kelurahan($request->siswa['no_prop_rumah'], $request->siswa['no_kab_rumah'], $request->siswa['no_kec_rumah'], $request->siswa['no_kel_rumah']),
            'kodepos_rumah' => $request->siswa['kodepos_rumah'],
            'lat_rumah' => $request->siswa['lat_rumah'],
            'lng_rumah' => $request->siswa['lng_rumah'],
            'nis' => $request->siswa['nis'],
            'nisn' => $request->siswa['nisn'],
            'telp' => $request->siswa['telp'],
            'hp' => $request->siswa['hp'],
            'whatsapp' => $request->siswa['whatsapp'],
            'temp_lahir' => $request->siswa['temp_lahir'],
            'tgl_lahir' => $request->siswa['tgl_lahir'] ? date('Y-m-d', strtotime($request->siswa['tgl_lahir'])) : null,
            'kelamin' => $request->siswa['kelamin'],            
            'id_kelas_sekolah' => $request->siswa['id_kelas_sekolah'],
            'tgl_masuk' => $request->siswa['tgl_masuk'] ? date('Y-m-d', strtotime($request->siswa['tgl_masuk'])) : null,
            'asal_sekolah' => $request->siswa['asal_sekolah'],
            'tgl_sttb' => $request->siswa['tgl_sttb'] ? date('Y-m-d', strtotime($request->siswa['tgl_sttb'])) : null,
            'no_sttb' => $request->siswa['no_sttb'],
            'tgl_stl' => $request->siswa['tgl_stl'] ? date('Y-m-d', strtotime($request->siswa['tgl_stl'])) : null,
            'no_stl' => $request->siswa['no_stl'],
            'id_kelas_terima' => $request->siswa['id_kelas_terima'],
            'asal_pindahan' => $request->siswa['asal_pindahan'],
            'alasan_pindahan' => $request->siswa['alasan_pindahan'],
            'id_agama' => $request->siswa['id_agama'],
            'wna' => $request->siswa['wna'],
            'negara' => $request->siswa['negara'],
            'anak_ke' => $request->siswa['anak_ke'],
            'sdr_kandung' => $request->siswa['sdr_kandung'],
            'sdr_tiri' => $request->siswa['sdr_tiri'],
            'sdr_angkat' => $request->siswa['sdr_angkat'],
            'yatim' => $request->siswa['yatim'],
            'id_bahasa' => $request->siswa['id_bahasa'],
            'goldar' => $request->siswa['goldar'],
            'sakit' => $request->siswa['sakit'],
            'kelainan' => $request->siswa['kelainan'],
            'tinggi' => $request->siswa['tinggi'],
            'berat' => $request->siswa['berat'],
            'kesenian' => $request->siswa['kesenian'],
            'olahraga' => $request->siswa['olahraga'],
            'organisasi' => $request->siswa['organisasi'],
            'lain' => $request->siswa['lain'],
            'beasiswa' => $request->siswa['beasiswa'],
            'tgl_meninggalkan' => $request->siswa['tgl_meninggalkan'] ? date('Y-m-d', strtotime($request->siswa['tgl_meninggalkan'])) : null,
            'pindah_ke' => $request->siswa['pindah_ke'],
            'alasan_pindah_ke' => $request->siswa['alasan_pindah_ke'],
            'no_ijasah_lulus' => $request->siswa['no_ijasah_lulus'],
            'no_tanda_lulus' => $request->siswa['no_tanda_lulus'],
            'nilai_rata' => $request->siswa['nilai_rata'],
            'melanjutkan_ke' => $request->siswa['melanjutkan_ke'],
            'bekerja_di' => $request->siswa['bekerja_di'],
            'nik' => $request->siswa['nik'],
            'no_kk' => $request->siswa['no_kk'],            
            'email' => $request->siswa['email'],
            'username' => $username,
            'password' => bcrypt('123456'),
            'simdik_create' => Auth::guard('simdik')->user()->id,
            'simdik_update' => Auth::guard('simdik')->user()->id,
          ]);

        $baru   = DB::table('siswas')
                    ->where('username',$username)
                    ->where('email',$request->siswa['email'])
                    ->orderby('id','desc')
                    ->first();        

        if($request->siswa['foto']){
          DB::table('siswas')
            ->where('id',$baru->id)
            ->update([
              'foto' => $request->siswa['foto'],
            ]);
        }

        if($request->siswa['fc_akte']){
          DB::table('siswas')
            ->where('id',$baru->id)
            ->update([
              'fc_akte' => $request->siswa['fc_akte'],
            ]);
        }

        if($request->siswa['fc_kk']){
          DB::table('siswas')
            ->where('id',$baru->id)
            ->update([
              'fc_kk' => $request->siswa['fc_kk'],
            ]);
        }

        if($request->siswa['fc_ktp']){
          DB::table('siswas')
            ->where('id',$baru->id)
            ->update([
              'fc_ktp' => $request->siswa['fc_ktp'],
            ]);
        }

        if($request->siswa['fc_nisn']){
          DB::table('siswas')
            ->where('id',$baru->id)
            ->update([
              'fc_nisn' => $request->siswa['fc_nisn'],
            ]);
        }

        if($request->siswa['fc_skl']){
          DB::table('siswas')
            ->where('id',$baru->id)
            ->update([
              'fc_skl' => $request->siswa['fc_skl'],
            ]);
        }

        DB::table('walis')
          ->insert([
            'id_sekolah' => $request->wali['id_sekolah'],
            'nama_ayah' => $request->wali['nama_ayah'],
            'temp_lahir_ayah' => $request->wali['temp_lahir_ayah'],
            'tgl_lahir_ayah' => $request->wali['tgl_lahir_ayah'] ? date('Y-m-d', strtotime($request->wali['tgl_lahir_ayah'])) : null,
            'id_agama_ayah' => $request->wali['id_agama_ayah'],
            'warganegara_ayah' => $request->wali['warganegara_ayah'],
            'negara_ayah' => $request->wali['negara_ayah'],
            'id_pendidikan_ayah' => $request->wali['id_pendidikan_ayah'],
            'id_pekerjaan_ayah' => $request->wali['id_pekerjaan_ayah'],
            'penghasilan_ayah' => $request->wali['penghasilan_ayah'],
            'alamat_ayah' => $request->wali['alamat_ayah'],
            'dusun_ayah' => $request->wali['dusun_ayah'],
            'rt_ayah' => $request->wali['rt_ayah'],
            'rw_ayah' => $request->wali['rw_ayah'],
            'no_prop_ayah' => $request->wali['no_prop_ayah'],
            'no_kab_ayah' => $request->wali['no_kab_ayah'],
            'no_kec_ayah' => $request->wali['no_kec_ayah'],
            'no_kel_ayah' => $request->wali['no_kel_ayah'],
            'propinsi_ayah' => $this->nama_propinsi($request->wali['no_prop_ayah']),
            'kota_ayah' => $this->nama_kota($request->wali['no_prop_ayah'],$request->wali['no_kab_ayah']),
            'kecamatan_ayah' => $this->nama_kecamatan($request->wali['no_prop_ayah'],$request->wali['no_kab_ayah'],$request->wali['no_kec_ayah']),
            'desa_ayah' => $this->nama_kelurahan($request->wali['no_prop_ayah'],$request->wali['no_kab_ayah'],$request->wali['no_kec_ayah'],$request->wali['no_kel_ayah']),
            'lat_ayah' => $request->wali['lat_ayah'],
            'lng_ayah' => $request->wali['lng_ayah'],
            'telp_ayah' => $request->wali['telp_ayah'],
            'hp_ayah' => $request->wali['hp_ayah'],
            'whatsapp_ayah' => $request->wali['whatsapp_ayah'],
            'email_ayah' => $request->wali['email_ayah'],
            'nik_ayah' => $request->wali['nik_ayah'],
            'no_kk_ayah' => $request->wali['no_kk_ayah'],
            'foto_ayah' => $request->wali['foto_ayah'],
            'ktp_ayah' => $request->wali['ktp_ayah'],
            'status_ayah' => $request->wali['status_ayah'],
            'nama_ibu' => $request->wali['nama_ibu'],
            'temp_lahir_ibu' => $request->wali['temp_lahir_ibu'],
            'tgl_lahir_ibu' => $request->wali['tgl_lahir_ibu'] ? date('Y-m-d', strtotime($request->wali['tgl_lahir_ibu'])) : null,
            'id_agama_ibu' => $request->wali['id_agama_ibu'],
            'warganegara_ibu' => $request->wali['warganegara_ibu'],
            'negara_ibu' => $request->wali['negara_ibu'],
            'id_pendidikan_ibu' => $request->wali['id_pendidikan_ibu'],
            'id_pekerjaan_ibu' => $request->wali['id_pekerjaan_ibu'],
            'penghasilan_ibu' => $request->wali['penghasilan_ibu'],
            'alamat_ibu' => $request->wali['alamat_ibu'],
            'dusun_ibu' => $request->wali['dusun_ibu'],
            'rt_ibu' => $request->wali['rt_ibu'],
            'rw_ibu' => $request->wali['rw_ibu'],
            'no_prop_ibu' => $request->wali['no_prop_ibu'],
            'no_kab_ibu' => $request->wali['no_kab_ibu'],
            'no_kec_ibu' => $request->wali['no_kec_ibu'],
            'no_kel_ibu' => $request->wali['no_kel_ibu'],
            'propinsi_ibu' => $this->nama_propinsi($request->wali['no_prop_ibu']),
            'kota_ibu' => $this->nama_kota($request->wali['no_prop_ibu'],$request->wali['no_kab_ibu']),
            'kecamatan_ibu' => $this->nama_kecamatan($request->wali['no_prop_ibu'],$request->wali['no_kab_ibu'],$request->wali['no_kec_ibu']),
            'desa_ibu' => $this->nama_kelurahan($request->wali['no_prop_ibu'],$request->wali['no_kab_ibu'],$request->wali['no_kec_ibu'],$request->wali['no_kel_ibu']),
            'lat_ibu' => $request->wali['lat_ibu'],
            'lng_ibu' => $request->wali['lng_ibu'],
            'telp_ibu' => $request->wali['telp_ibu'],
            'hp_ibu' => $request->wali['hp_ibu'],
            'whatsapp_ibu' => $request->wali['whatsapp_ibu'],
            'email_ibu' => $request->wali['email_ibu'],
            'nik_ibu' => $request->wali['nik_ibu'],
            'no_kk_ibu' => $request->wali['no_kk_ibu'],
            'foto_ibu' => $request->wali['foto_ibu'],
            'ktp_ibu' => $request->wali['ktp_ibu'],
            'status_ibu' => $request->wali['status_ibu'],
            'nama_wali' => $request->wali['nama_wali'],
            'temp_lahir_wali' => $request->wali['temp_lahir_wali'],
            'tgl_lahir_wali' => $request->wali['tgl_lahir_wali'] ? date('Y-m-d', strtotime($request->wali['tgl_lahir_wali'])) : null,
            'id_agama_wali' => $request->wali['id_agama_wali'],
            'warganegara_wali' => $request->wali['warganegara_wali'],
            'negara_wali' => $request->wali['negara_wali'],
            'id_pendidikan_wali' => $request->wali['id_pendidikan_wali'],
            'id_pekerjaan_wali' => $request->wali['id_pekerjaan_wali'],
            'penghasilan_wali' => $request->wali['penghasilan_wali'],
            'alamat_wali' => $request->wali['alamat_wali'],
            'dusun_wali' => $request->wali['dusun_wali'],
            'rt_wali' => $request->wali['rt_wali'],
            'rw_wali' => $request->wali['rw_wali'],
            'no_prop_wali' => $request->wali['no_prop_wali'],
            'no_kab_wali' => $request->wali['no_kab_wali'],
            'no_kec_wali' => $request->wali['no_kec_wali'],
            'no_kel_wali' => $request->wali['no_kel_wali'],
            'propinsi_wali' => $this->nama_propinsi($request->wali['no_prop_wali']),
            'kota_wali' => $this->nama_kota($request->wali['no_prop_wali'],$request->wali['no_kab_wali']),
            'kecamatan_wali' => $this->nama_kecamatan($request->wali['no_prop_wali'],$request->wali['no_kab_wali'],$request->wali['no_kec_wali']),
            'desa_wali' => $this->nama_kelurahan($request->wali['no_prop_wali'],$request->wali['no_kab_wali'],$request->wali['no_kec_wali'],$request->wali['no_kel_wali']),
            'lat_wali' => $request->wali['lat_wali'],
            'lng_wali' => $request->wali['lng_wali'],
            'telp_wali' => $request->wali['telp_wali'],
            'hp_wali' => $request->wali['hp_wali'],
            'whatsapp_wali' => $request->wali['whatsapp_wali'],
            'email_wali' => $request->wali['email_wali'],
            'nik_wali' => $request->wali['nik_wali'],
            'no_kk_wali' => $request->wali['no_kk_wali'],
            'foto_wali' => $request->wali['foto_wali'],
            'ktp_wali' => $request->wali['ktp_wali'],
            'nisn' => $baru->nisn,
            'username' => $username,
            'password' => bcrypt('123456'),
            'petugas_create' => Auth::guard('simdik')->user()->id,
            'petugas_update' => Auth::guard('simdik')->user()->id,
          ]);
      }

      $waliBaru   = DB::table('walis')
                      ->where('username',$username)
                      ->orderby('id','desc')
                      ->first();

      DB::table('siswas')
        ->where('id',$baru->id)
        ->update([
          'id_wali' => $waliBaru->id,
        ]);

      $data   = DB::table('siswas')
                  ->where('id',$baru->id)
                  ->first();

      return response()->json($data);
    }

    public function edit(request $request){
      $cek  = DB::table('simdiks')->where('id',$request->id)->first();

      if($cek){
        $validator = Validator::make($request->all(), [
          'siswa.nama' => 'required|String',
          'siswa.id_sekolah' => 'required',
          'siswa.email' => 'required|email|unique:siswas,email,'.$request->id,
        ]);

        if ($validator->fails()) {
          return response()->json($validator->errors(), 422);
        }        

        DB::table('siswas')
          ->where('id',$request->id)
          ->update([
            'id_sekolah' => $request->siswa['id_sekolah'],
            'nama' => $request->siswa['nama'],
            'panggilan' => $request->siswa['panggilan'],
            'alamat_tinggal' => $request->siswa['alamat_tinggal'],
            'dusun_tinggal' => $request->siswa['dusun_tinggal'],
            'rt_tinggal' => $request->siswa['rt_tinggal'],
            'rw_tinggal' => $request->siswa['rw_tinggal'],
            'no_prop_tinggal' => $request->siswa['no_prop_tinggal'],
            'no_kab_tinggal' => $request->siswa['no_kab_tinggal'],
            'no_kec_tinggal' => $request->siswa['no_kec_tinggal'],
            'no_kel_tinggal' => $request->siswa['no_kel_tinggal'],
            'propinsi_tinggal' => $this->nama_propinsi($request->siswa['no_prop_tinggal']),
            'kota_tinggal' => $this->nama_kota($request->siswa['no_prop_tinggal'], $request->siswa['no_kab_tinggal']),
            'kecamatan_tinggal' => $this->nama_kecamatan($request->siswa['no_prop_tinggal'], $request->siswa['no_kab_tinggal'], $request->siswa['no_kec_tinggal']),
            'desa_tinggal' => $this->nama_kelurahan($request->siswa['no_prop_tinggal'], $request->siswa['no_kab_tinggal'], $request->siswa['no_kec_tinggal'], $request->siswa['no_kel_tinggal']),
            'kodepos_tinggal' => $request->siswa['kodepos_tinggal'],
            'lat_tinggal' => $request->siswa['lat_tinggal'],
            'lng_tinggal' => $request->siswa['lng_tinggal'],
            'status_tinggal' => $request->siswa['status_tinggal'],
            'jarak_sekolah' => $request->siswa['jarak_sekolah'],
            'tinggalrumah' => $request->siswa['tinggalrumah'],
            'jarak_rumah' => $request->siswa['jarak_rumah'],
            'alamat_rumah' => $request->siswa['alamat_rumah'],
            'dusun_rumah' => $request->siswa['dusun_rumah'],
            'rt_rumah' => $request->siswa['rt_rumah'],
            'rw_rumah' => $request->siswa['rw_rumah'],
            'no_prop_rumah' => $request->siswa['no_prop_rumah'],
            'no_kab_rumah' => $request->siswa['no_kab_rumah'],
            'no_kec_rumah' => $request->siswa['no_kec_rumah'],
            'no_kel_rumah' => $request->siswa['no_kel_rumah'],
            'propinsi_rumah' => $this->nama_propinsi($request->siswa['no_prop_rumah']),
            'kota_rumah' => $this->nama_kota($request->siswa['no_prop_rumah'], $request->siswa['no_kab_rumah']),
            'kecamatan_rumah' => $this->nama_kecamatan($request->siswa['no_prop_rumah'], $request->siswa['no_kab_rumah'], $request->siswa['no_kec_rumah']),
            'desa_rumah' => $this->nama_kelurahan($request->siswa['no_prop_rumah'], $request->siswa['no_kab_rumah'], $request->siswa['no_kec_rumah'], $request->siswa['no_kel_rumah']),
            'kodepos_rumah' => $request->siswa['kodepos_rumah'],
            'lat_rumah' => $request->siswa['lat_rumah'],
            'lng_rumah' => $request->siswa['lng_rumah'],
            'nis' => $request->siswa['nis'],
            'nisn' => $request->siswa['nisn'],
            'telp' => $request->siswa['telp'],
            'hp' => $request->siswa['hp'],
            'whatsapp' => $request->siswa['whatsapp'],
            'temp_lahir' => $request->siswa['temp_lahir'],
            'tgl_lahir' => $request->siswa['tgl_lahir'] ? date('Y-m-d', strtotime($request->siswa['tgl_lahir'])) : null,
            'kelamin' => $request->siswa['kelamin'],            
            'id_kelas_sekolah' => $request->siswa['id_kelas_sekolah'],
            'tgl_masuk' => $request->siswa['tgl_masuk'] ? date('Y-m-d', strtotime($request->siswa['tgl_masuk'])) : null,
            'asal_sekolah' => $request->siswa['asal_sekolah'],
            'tgl_sttb' => $request->siswa['tgl_sttb'] ? date('Y-m-d', strtotime($request->siswa['tgl_sttb'])) : null,
            'no_sttb' => $request->siswa['no_sttb'],
            'tgl_stl' => $request->siswa['tgl_stl'] ? date('Y-m-d', strtotime($request->siswa['tgl_stl'])) : null,
            'no_stl' => $request->siswa['no_stl'],
            'id_kelas_terima' => $request->siswa['id_kelas_terima'],
            'asal_pindahan' => $request->siswa['asal_pindahan'],
            'alasan_pindahan' => $request->siswa['alasan_pindahan'],
            'id_agama' => $request->siswa['id_agama'],
            'wna' => $request->siswa['wna'],
            'negara' => $request->siswa['negara'],
            'anak_ke' => $request->siswa['anak_ke'],
            'sdr_kandung' => $request->siswa['sdr_kandung'],
            'sdr_tiri' => $request->siswa['sdr_tiri'],
            'sdr_angkat' => $request->siswa['sdr_angkat'],
            'yatim' => $request->siswa['yatim'],
            'id_bahasa' => $request->siswa['id_bahasa'],
            'goldar' => $request->siswa['goldar'],
            'sakit' => $request->siswa['sakit'],
            'kelainan' => $request->siswa['kelainan'],
            'tinggi' => $request->siswa['tinggi'],
            'berat' => $request->siswa['berat'],
            'kesenian' => $request->siswa['kesenian'],
            'olahraga' => $request->siswa['olahraga'],
            'organisasi' => $request->siswa['organisasi'],
            'lain' => $request->siswa['lain'],
            'beasiswa' => $request->siswa['beasiswa'],
            'tgl_meninggalkan' => $request->siswa['tgl_meninggalkan'] ? date('Y-m-d', strtotime($request->siswa['tgl_meninggalkan'])) : null,
            'pindah_ke' => $request->siswa['pindah_ke'],
            'alasan_pindah_ke' => $request->siswa['alasan_pindah_ke'],
            'no_ijasah_lulus' => $request->siswa['no_ijasah_lulus'],
            'no_tanda_lulus' => $request->siswa['no_tanda_lulus'],
            'nilai_rata' => $request->siswa['nilai_rata'],
            'melanjutkan_ke' => $request->siswa['melanjutkan_ke'],
            'bekerja_di' => $request->siswa['bekerja_di'],
            'nik' => $request->siswa['nik'],
            'no_kk' => $request->siswa['no_kk'],            
            'email' => $request->siswa['email'],
            'simdik_update' => Auth::guard('simdik')->user()->id,
          ]);

        if($request->foto){
          DB::table('siswas')
            ->where('id',$request->id)
            ->update([
              'foto' => $request->foto,
            ]);
        }

        if($request->fc_akte){
          DB::table('siswas')
            ->where('id',$request->id)
            ->update([
              'fc_akte' => $request->fc_akte,
            ]);
        }

        if($request->fc_kk){
          DB::table('siswas')
            ->where('id',$request->id)
            ->update([
              'fc_kk' => $request->fc_kk,
            ]);
        }

        if($request->fc_ktp){
          DB::table('siswas')
            ->where('id',$request->id)
            ->update([
              'fc_ktp' => $request->fc_ktp,
            ]);
        }

        if($request->fc_nisn){
          DB::table('siswas')
            ->where('id',$request->id)
            ->update([
              'fc_nisn' => $request->fc_nisn,
            ]);
        }

        if($request->fc_skl){
          DB::table('siswas')
            ->where('id',$request->id)
            ->update([
              'fc_skl' => $request->fc_skl,
            ]);
        }

        $data   = DB::table('siswas')->where('id',$request->id)->first();

        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }

    public function detil($id){
      $agama    = DB::table('dt_agama')
                    ->where('hapus',0)
                    ->orderby('agama')
                    ->get();

      $bahasa   = DB::table('dt_bahasa')
                    ->where('hapus',0)
                    ->orderby('bahasa')
                    ->get();

      $pendidikan   = DB::table('dt_pendidikan')
                        ->where('hapus',0)
                        ->orderby('id')
                        ->get();

      $pekerjaan    = DB::table('dt_pekerjaan')
                        ->where('hapus',0)
                        ->orderby('pekerjaan')
                        ->get();

      $kelas    = DB::table('dt_kelas_sekolah')
                    ->where('id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                    ->where('hapus',0)
                    ->orderby('kelas')
                    ->orderby('kode')
                    ->orderby('sub_kelas')
                    ->get();

      $propinsi   = DB::table('dt_wilayah')
                      ->whereNotNull('no_prop')
                      ->whereNull('no_kab')
                      ->whereNull('no_kec')
                      ->whereNull('no_kel')
                      ->orderby('nama')
                      ->get();

      if($id == 0){
        return response()->json([
          'agama' => $agama,
          'bahasa' => $bahasa,
          'pendidikan' => $pendidikan,
          'pekerjaan' => $pekerjaan,
          'kelas' => $kelas,
          'propinsi' => $propinsi,
        ]);
      } else {
        $data  = DB::table('siswas')->where('id',$id)->first();

        if($data){
          $kota_tinggal       = DB::table('dt_wilayah')
                                  ->where('no_prop',$data->no_prop_tinggal)
                                  ->whereNotNull('no_kab')
                                  ->whereNull('no_kec')
                                  ->whereNull('no_kel')
                                  ->orderby('nama')
                                  ->get();

          $kecamatan_tinggal  = DB::table('dt_wilayah')
                                  ->where('no_prop',$data->no_prop_tinggal)
                                  ->where('no_kab',$data->no_kab_tinggal)
                                  ->whereNotNull('no_kec')
                                  ->whereNull('no_kel')
                                  ->orderby('nama')
                                  ->get();

          $desa_tinggal       = DB::table('dt_wilayah')
                                  ->where('no_prop',$data->no_prop_tinggal)
                                  ->where('no_kab',$data->no_kab_tinggal)
                                  ->where('no_kec',$data->no_kec_tinggal)
                                  ->whereNotNull('no_kel')
                                  ->orderby('nama')
                                  ->get();

          $kota_rumah         = DB::table('dt_wilayah')
                                  ->where('no_prop',$data->no_prop_rumah)
                                  ->whereNotNull('no_kab')
                                  ->whereNull('no_kec')
                                  ->whereNull('no_kel')
                                  ->orderby('nama')
                                  ->get();

          $kecamatan_rumah    = DB::table('dt_wilayah')
                                  ->where('no_prop',$data->no_prop_rumah)
                                  ->where('no_kab',$data->no_kab_rumah)
                                  ->whereNotNull('no_kec')
                                  ->whereNull('no_kel')
                                  ->orderby('nama')
                                  ->get();

          $desa_rumah         = DB::table('dt_wilayah')
                                  ->where('no_prop',$data->no_prop_rumah)
                                  ->where('no_kab',$data->no_kab_rumah)
                                  ->where('no_kec',$data->no_kec_rumah)
                                  ->whereNotNull('no_kel')
                                  ->orderby('nama')
                                  ->get();

          $wali               = DB::table('walis')
                                  ->where('id',$data->id_wali)
                                  ->first();

          $kota_ayah         = DB::table('dt_wilayah')
                                  ->where('no_prop',$wali->no_prop_ayah)
                                  ->whereNotNull('no_kab')
                                  ->whereNull('no_kec')
                                  ->whereNull('no_kel')
                                  ->orderby('nama')
                                  ->get();

          $kecamatan_ayah    = DB::table('dt_wilayah')
                                  ->where('no_prop',$wali->no_prop_ayah)
                                  ->where('no_kab',$wali->no_kab_ayah)
                                  ->whereNotNull('no_kec')
                                  ->whereNull('no_kel')
                                  ->orderby('nama')
                                  ->get();

          $desa_ayah         = DB::table('dt_wilayah')
                                  ->where('no_prop',$wali->no_prop_ayah)
                                  ->where('no_kab',$wali->no_kab_ayah)
                                  ->where('no_kec',$wali->no_kec_ayah)
                                  ->whereNotNull('no_kel')
                                  ->orderby('nama')
                                  ->get();

          $kota_ibu         = DB::table('dt_wilayah')
                                  ->where('no_prop',$wali->no_prop_ibu)
                                  ->whereNotNull('no_kab')
                                  ->whereNull('no_kec')
                                  ->whereNull('no_kel')
                                  ->orderby('nama')
                                  ->get();

          $kecamatan_ibu    = DB::table('dt_wilayah')
                                  ->where('no_prop',$wali->no_prop_ibu)
                                  ->where('no_kab',$wali->no_kab_ibu)
                                  ->whereNotNull('no_kec')
                                  ->whereNull('no_kel')
                                  ->orderby('nama')
                                  ->get();

          $desa_ibu         = DB::table('dt_wilayah')
                                  ->where('no_prop',$wali->no_prop_ibu)
                                  ->where('no_kab',$wali->no_kab_ibu)
                                  ->where('no_kec',$wali->no_kec_ibu)
                                  ->whereNotNull('no_kel')
                                  ->orderby('nama')
                                  ->get();

          $kota_wali         = DB::table('dt_wilayah')
                                  ->where('no_prop',$wali->no_prop_wali)
                                  ->whereNotNull('no_kab')
                                  ->whereNull('no_kec')
                                  ->whereNull('no_kel')
                                  ->orderby('nama')
                                  ->get();

          $kecamatan_wali    = DB::table('dt_wilayah')
                                  ->where('no_prop',$wali->no_prop_wali)
                                  ->where('no_kab',$wali->no_kab_wali)
                                  ->whereNotNull('no_kec')
                                  ->whereNull('no_kel')
                                  ->orderby('nama')
                                  ->get();

          $desa_wali         = DB::table('dt_wilayah')
                                  ->where('no_prop',$wali->no_prop_wali)
                                  ->where('no_kab',$wali->no_kab_wali)
                                  ->where('no_kec',$wali->no_kec_wali)
                                  ->whereNotNull('no_kel')
                                  ->orderby('nama')
                                  ->get();

          return response()->json([
            'data' => $data,
            'wali' => $wali,
            'agama' => $agama,
            'bahasa' => $bahasa,
            'pendidikan' => $pendidikan,
            'pekerjaan' => $pekerjaan,
            'kelas' => $kelas,
            'propinsi' => $propinsi,
            'kota_tinggal' => $kota_tinggal,
            'kecamatan_tinggal' => $kecamatan_tinggal,
            'desa_tinggal' => $desa_tinggal,
            'kota_rumah' => $kota_rumah,
            'kecamatan_rumah' => $kecamatan_rumah,
            'desa_rumah' => $desa_rumah,
            'kota_ayah' => $kota_ayah,
            'kecamatan_ayah' => $kecamatan_ayah,
            'desa_ayah' => $desa_ayah,
            'kota_ibu' => $kota_ibu,
            'kecamatan_ibu' => $kecamatan_ibu,
            'desa_ibu' => $desa_ibu,
            'kota_wali' => $kota_wali,
            'kecamatan_wali' => $kecamatan_wali,
            'desa_wali' => $desa_wali,
          ]);
        } else {
          return response()->json([
            'status' => 'error',
            'message' => 'Data tidak ditemukan',
          ], 401);
        }
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

    public function hapus($id) {
      $cek  = DB::table('siswas')->where('id',$id)->first();

      if($cek){
        DB::table('siswas')
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

    public function reset(request $request){
      $cek  = DB::table('siswas')->where('id',$request->id)->first();

      if($cek){
        DB::table('siswas')
          ->where('id',$request->id)
          ->update([
            'password' => Hash::make('123456'),
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

      $data   = DB::table('siswas')
                  ->when($id, function ($query) use ($id) {
                      return $query->whereNot('siswas.id',$id);
                    })
                  ->when($username, function ($query) use ($username) {
                      return $query->where('siswas.username',$username);
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

    public function import(request $request){
      $this->validate($request, [
        'file' => 'required|mimes:xls,xlsx'
      ]);

      Excel::import(new SiswaImport, request()->file('file'));

      DB::table('siswas')
        ->where('id_kelas_sekolah',null)
        ->where('id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
        ->update([
          'id_kelas_sekolah' => $request->id_kelas_sekolah,
          'simdik_create' => Auth::guard('simdik')->user()->id,
          'simdik_update' => Auth::guard('simdik')->user()->id,
        ]);

      return response()->json([
        'status' => 'success',
        'message' => 'Berhasil di import'
      ],200);
    }

    public function list(request $request){
      $data     = DB::table('siswas')
                    ->leftjoin('dt_kelas_sekolah','siswas.id_kelas_sekolah','=','dt_kelas_sekolah.id')
                    ->selectRaw('siswas.id, 
                                 siswas.id_sekolah, 
                                 siswas.nama, 
                                 siswas.nis, 
                                 siswas.nisn, 
                                 siswas.foto,
                                 dt_kelas_sekolah.kelas,
                                 dt_kelas_sekolah.kode,
                                 dt_kelas_sekolah.sub_kelas')
                    ->orderby('siswas.nama')
                    ->where('siswas.hapus',0)
                    ->where('siswas.id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                    ->get();

      if($data){
        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }

    public function detil_perpus(request $request){
      $data     = DB::table('siswas')
                    ->leftjoin('dt_kelas_sekolah','siswas.id_kelas_sekolah','=','dt_kelas_sekolah.id')
                    ->selectRaw('siswas.id, 
                                 siswas.id_sekolah, 
                                 siswas.nama, 
                                 siswas.nis, 
                                 siswas.nisn, 
                                 siswas.foto,
                                 dt_kelas_sekolah.kelas,
                                 dt_kelas_sekolah.kode,
                                 dt_kelas_sekolah.sub_kelas')
                    ->orderby('siswas.nama')
                    ->where('siswas.hapus',0)
                    ->where('siswas.id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                    ->where('siswas.id',$request->id)
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
