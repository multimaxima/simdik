<?php

namespace App\Http\Controllers\Api\Simdik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;
use Auth;

class SarprasController extends Controller
{
    public function __construct(){
      $this->middleware('auth:simdik');
    }

    public function index(){
      $data   = DB::table('sarpras')
                  ->leftjoin('dt_sarpras','sarpras.dt_sarpras','=','dt_sarpras.id')
                  ->selectRaw('sarpras.id,
                               sarpras.id_sekolah,
                               sarpras.dt_sarpras,
                               dt_sarpras.sarpras,
                               sarpras.tahun,
                               sarpras.ganjil,
                               sarpras.genap')
                  ->where('sarpras.id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->get();

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
      $data   = DB::table('sarpras')
                  ->where('id',$request->id)
                  ->first();

      if($data){
        DB::table('sarpras')
          ->where('id',$data->id)
          ->update([
            'ganjil' => $request->ganjil,
            'genap' => $request->genap,
            'simdik_update' => Auth::guard('simdik')->user()->id,
          ]);

        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }      
    }
}
