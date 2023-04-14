<?php

namespace App\Http\Controllers\Api\Simdik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Crypt;
use Hash;

class HomeController extends Controller
{
    public function propinsi(request $request){
      $data   = DB::table('dt_wilayah')
                  ->orderby('nama')
                  ->whereNotNull('no_prop')
                  ->whereNull('no_kab')
                  ->whereNull('no_kec')
                  ->whereNull('no_kel')
                  ->get();

      if($data) {
        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
        ], 401);
      }
    }

    public function kota(request $request){
      $data   = DB::table('dt_wilayah')
                  ->orderby('nama')
                  ->where('no_prop',$request->no_prop)
                  ->whereNotNull('no_kab')
                  ->whereNull('no_kec')
                  ->whereNull('no_kel')
                  ->get();

      if($data) {
        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
        ], 401);
      }
    }

    public function kecamatan(request $request){
      $data   = DB::table('dt_wilayah')
                  ->orderby('nama')
                  ->where('no_prop',$request->no_prop)
                  ->where('no_kab',$request->no_kab)
                  ->whereNotNull('no_kec')
                  ->whereNull('no_kel')
                  ->get();

      if($data) {
        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
        ], 401);
      }
    }

    public function desa(request $request){
      $data   = DB::table('dt_wilayah')
                  ->orderby('nama')
                  ->where('no_prop',$request->no_prop)
                  ->where('no_kab',$request->no_kab)
                  ->where('no_kec',$request->no_kec)
                  ->whereNotNull('no_kel')
                  ->get();

      if($data) {
        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
        ], 401);
      }
    }

    public function admin(request $request){
      $data   = DB::table('sims')
                  ->where('hapus',0)
                  ->get();

      return response()->json($data);
    }
}
