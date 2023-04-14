<?php

namespace App\Http\Controllers\Api\Simdik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UjianController extends Controller
{
    public function __construct(){
      $this->middleware('auth:simdik');
    }

    public function ulangan_tugas(request $request){
      $data   = DB::table('ujian')
                  ->leftjoin('dt_jenis_ujian','ujian.id_jenis','=','dt_jenis_ujian.id')
                  ->leftjoin('dt_kelas_sekolah','ujian.id_kelas_sekolah','=','dt_kelas_sekolah.id')
                  ->leftjoin('dt_mapel','ujian.id_mapel','=','dt_mapel.id')
                  ->selectRaw('ujian.id,
                               ujian.id_sekolah,
                               ujian.id_jenis,
                               ujian.keterangan,
                               ujian.tanggal,
                               ujian.jam,
                               ujian.id_kelas_sekolah,
                               ujian.id_mapel,
                               ujian.ta,
                               ujian.semester,
                               dt_jenis_ujian.jenis_ujian,
                               dt_kelas_sekolah.kelas,
                               dt_kelas_sekolah.kode,
                               dt_kelas_sekolah.sub_kelas,
                               dt_mapel.mapel')
                  ->get();

      return response()->json($data);
    }
}
