<?php

namespace App\Http\Controllers\Api\Simdik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;
use Auth;

class BelSuaraController extends Controller
{
    public function __construct(){
      $this->middleware('auth:simdik');
    }

    public function index(request $request){
      $data   = DB::table('bel_suara')
                  ->orderby('kategori')
                  ->orderby('keterangan')
                  ->get();

      if($data) {
        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
        ], 401);
      }
    }

    public function detil($id){
      $data   = DB::table('bel_suara')
                  ->selectRaw('bel_suara.suara')
                  ->where('id',$id)
                  ->first();

      if($data) {
        return response()->json($data->suara);
      } else {
        return response()->json([
          'status' => 'error',
        ], 401);
      }
    }
}
