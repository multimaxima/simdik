<?php

namespace App\Http\Controllers\Api\Simdik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;
use Auth;

class SanitasiController extends Controller
{
    public function __construct(){
      $this->middleware('auth:simdik');
    }

    public function index(){
      $data   = DB::table('sanitasi')
                  ->where('id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->first();

      if($data){
        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Error',
        ], 401);
      } 
    }

    public function edit(request $request){      
      DB::table('sanitasi')
        ->where('id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
        ->update([
          'sumber_air' => $request->sumber_air, 
          'sumber_air_minum' => $request->sumber_air_minum, 
          'kecukupan_air_bersih' => $request->kecukupan_air_bersih, 
          'jamban_kebutuhan_khusus' => $request->jamban_kebutuhan_khusus, 
          'tipe_jamban' => $request->tipe_jamban, 
          'hari_cuci_tangan' => $request->hari_cuci_tangan, 
          'jml_cuci_tangan' => $request->jml_cuci_tangan, 
          'jml_cuci_tangan_rusak' => $request->jml_cuci_tangan_rusak, 
          'sabun_air_mengalir' => $request->sabun_air_mengalir, 
          'pembuangan_limbah_jamban' => $request->pembuangan_limbah_jamban, 
          'kuras_tangki_septik' => $request->kuras_tangki_septik, 
          'simdik_update' => Auth::guard('simdik')->user()->id,
        ]);

      $data   = DB::table('sanitasi')
                  ->where('id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->first();

      return response()->json($data);
    }
}
