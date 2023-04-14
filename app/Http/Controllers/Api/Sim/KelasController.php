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

class KelasController extends Controller
{
    public function __construct(){
      $this->middleware('auth:sim');
    }

    public function index(request $request){
      $data   = DB::table('dt_kelas')
                  ->orderby('id')
                  ->where('hapus',0)
                  ->where('strata',$request->strata)
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
