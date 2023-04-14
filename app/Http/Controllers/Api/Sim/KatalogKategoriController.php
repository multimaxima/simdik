<?php

namespace App\Http\Controllers\Api\Sim;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;
use Auth;

class KatalogKategoriController extends Controller
{
    public function __construct(){
      $this->middleware('auth:sim');
    }

    public function index(request $request){
      $data   = DB::table('dt_katalog_kategori')
                  ->where('dt_katalog_kategori.hapus',0)
                  ->get();

      if($data) {
        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
        ], 401);
      }
    }

    public function baru(request $request){
      $validator = Validator::make($request->all(), [
        'kategori' => 'required|String',
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
      }

      DB::table('dt_katalog_kategori')
        ->insert([
          'kode' => $request->kode,
          'kategori' => $request->kategori,
          'diskripsi' => $request->diskripsi,
          'sim_create' => Auth::guard('sim')->user()->id,
          'sim_update' => Auth::guard('sim')->user()->id,
        ]);

      $data   = DB::table('dt_katalog_kategori')
                  ->orderby('id','desc')
                  ->first();

      if($data) {
        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
        ], 401);
      }
    }

    public function edit(request $request){
      $cek  = DB::table('dt_katalog_kategori')
                ->where('id',$request->id)
                ->first();

      if($cek){
        $validator = Validator::make($request->all(), [
          'kategori' => 'required|String',
        ]);

        if ($validator->fails()) {
          return response()->json($validator->errors(), 422);
        }

        DB::table('dt_katalog_kategori')
          ->where('id',$request->id)
          ->update([
            'kode' => $request->kode,
            'kategori' => $request->kategori,
            'diskripsi' => $request->diskripsi,
            'sim_update' => Auth::guard('sim')->user()->id,
          ]);

        $data   = DB::table('dt_katalog_kategori')
                  ->where('id',$request->id)
                  ->first();

        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ],404);
      }
    }

    public function hapus($id){
      $cek  = DB::table('dt_katalog_kategori')
                ->where('id',$id)
                ->first();

      if($cek){
        DB::table('dt_katalog_kategori')
          ->where('id',$id)
          ->update([
            'hapus' => 1,
            'sim_update' => Auth::guard('sim')->user()->id
          ]);

        return response()->json([
          'status' => 'success',
          'message' => 'Data berhasil dihapus',
        ]);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ],404);
      }
    }
}
