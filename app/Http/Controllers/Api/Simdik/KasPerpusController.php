<?php

namespace App\Http\Controllers\Api\Simdik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;
use Auth;

class KasPerpusController extends Controller
{
    public function __construct(){
      $this->middleware('auth:simdik');
    }

    public function index(request $request){
      $data   = DB::table('perpus_kas')
                  ->where('id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->orderby('id')
                  ->get();

      return response()->json($data);
    }
}
