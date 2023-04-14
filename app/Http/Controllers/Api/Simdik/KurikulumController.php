<?php

namespace App\Http\Controllers\Api\Simdik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;
use Auth;

class KurikulumController extends Controller
{
    public function __construct(){
      $this->middleware('auth:simdik');
    }

    public function index(){
      $data   = DB::table('dt_kurikulum')->get();

      return response()->json($data);
    }
}
