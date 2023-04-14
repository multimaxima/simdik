<?php

namespace App\Http\Controllers\Api\Sim;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;
use Auth;

class BelController extends Controller
{
    public function __construct(){
      $this->middleware('auth:sim');
    }

    public function index(request $request){
      if($request->kategori){
        $kategori = $request->kategori;
      } else {
        $kategori = '';
      }

      $data   = DB::table('bel_suara')
                  ->orderby('bel_suara.kategori')
                  ->orderby('bel_suara.keterangan')
                  ->when($kategori, function ($query) use ($kategori) {
                      return $query->where('bel_suara.kategori',$kategori);
                    })
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
      DB::table('bel_suara')
        ->insert([
          'kategori' => $request->kategori,
          'keterangan' => $request->keterangan,
          'suara' => $request->suara,
          'sim_create' => Auth::guard('sim')->user()->id,
          'sim_update' => Auth::guard('sim')->user()->id,
        ]);

      $data   = DB::table('bel_suara')->orderby('id','desc')->first();
      
      return response()->json($data);
    }

    public function edit(request $request){
      $cek  = DB::table('bel_suara')->where('id',$request->id)->first();

      if($cek){
        DB::table('bel_suara')
          ->where('id',$request->id)
          ->update([
            'kategori' => $request->kategori,
            'keterangan' => $request->keterangan,
            'sim_update' => Auth::guard('sim')->user()->id,
          ]);

        if($request->suara){
          DB::table('bel_suara')
          ->where('id',$request->id)
          ->update([
            'suara' => $request->suara,
          ]);          
        }

        $data   = DB::table('bel_suara')->where('id',$request->id)->first();

        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }

    public function hapus($id){
      $cek  = DB::table('bel_suara')->where('id',$id)->first();

      if($cek) {
        DB::table('bel_suara')
          ->where('id',$id)
          ->delete();

        return response()->json([
          'status' => 'success',
          'message' => 'Berhasil',
        ]);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }
}
