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


class WaliController extends Controller
{
    public function __construct(){
      $this->middleware('auth:simdik');
    }

    public function index(request $request){
      $data     = DB::table('walis')
                    ->leftjoin('siswas','walis.id','=','siswas.id_wali')
                    ->leftjoin('dt_kelas_sekolah','siswas.id_kelas_sekolah','=','dt_kelas_sekolah.id')
                    ->selectRaw('walis.id, 
                                 siswas.nama,
                                 siswas.nis,
                                 siswas.foto,
                                 dt_kelas_sekolah.kelas,
                                 dt_kelas_sekolah.kode,
                                 dt_kelas_sekolah.sub_kelas,
                                 walis.id_sekolah, 
                                 walis.nama_ayah, 
                                 (SELECT dt_pekerjaan.pekerjaan
                                  FROM dt_pekerjaan
                                  WHERE dt_pekerjaan.id = walis.id_pekerjaan_ayah) as pekerjaan_ayah,
                                 walis.penghasilan_ayah, 
                                 walis.alamat_ayah, 
                                 walis.dusun_ayah, 
                                 walis.rt_ayah, 
                                 walis.rw_ayah, 
                                 walis.propinsi_ayah, 
                                 walis.kota_ayah, 
                                 walis.kecamatan_ayah, 
                                 walis.desa_ayah, 
                                 walis.hp_ayah, 
                                 walis.foto_ayah, 
                                 walis.status_ayah, 
                                 walis.nama_ibu, 
                                 (SELECT dt_pekerjaan.pekerjaan
                                  FROM dt_pekerjaan
                                  WHERE dt_pekerjaan.id = walis.id_pekerjaan_ibu) as pekerjaan_ibu,
                                 walis.penghasilan_ibu, 
                                 walis.alamat_ibu, 
                                 walis.dusun_ibu, 
                                 walis.rt_ibu, 
                                 walis.rw_ibu, 
                                 walis.propinsi_ibu, 
                                 walis.kota_ibu, 
                                 walis.kecamatan_ibu, 
                                 walis.desa_ibu, 
                                 walis.hp_ibu, 
                                 walis.foto_ibu, 
                                 walis.status_ibu, 
                                 walis.nama_wali, 
                                 (SELECT dt_pekerjaan.pekerjaan
                                  FROM dt_pekerjaan
                                  WHERE dt_pekerjaan.id = walis.id_pekerjaan_wali) as pekerjaan_wali, 
                                 walis.penghasilan_wali, 
                                 walis.alamat_wali, 
                                 walis.dusun_wali, 
                                 walis.rt_wali, 
                                 walis.rw_wali, 
                                 walis.propinsi_wali, 
                                 walis.kota_wali, 
                                 walis.kecamatan_wali, 
                                 walis.desa_wali, 
                                 walis.hp_wali, 
                                 walis.foto_wali')
                    ->orderby('siswas.nama')
                    ->where('siswas.hapus',0)
                    ->where('walis.id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                    ->get();

      if($data) {
        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
        ], 401);
      }
    }
}
