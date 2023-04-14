<?php

namespace App\Http\Controllers\Api\Simdik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;
use Auth;

class BelController extends Controller
{
    public function __construct(){
      $this->middleware('auth:simdik');
    }

    public function index(request $request){
      $data   = DB::table('bel')
                  ->leftjoin('bel_suara','bel.id_suara','=','bel_suara.id')
                  ->selectRaw('bel.id,
                               bel.id_sekolah,
                               bel.id_hari,
                               bel.hari,
                               bel.jam,
                               bel.id_suara,
                               bel.keterangan,
                               bel_suara.suara,
                               bel_suara.keterangan as ket')
                  ->where('bel.id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->orderby('bel.id_hari')
                  ->orderby('bel.jam')
                  ->get();

      $hari   = DB::table('dt_hari')->get();

      $suara  = DB::table('bel_suara')
                  ->orderby('kategori')
                  ->orderby('keterangan')
                  ->get();

      if($data) {
        return response()->json([
          'data' => $data,
          'hari' => $hari,
          'suara' => $suara,
        ]);
      } else {
        return response()->json([
          'status' => 'error',
        ], 401);
      }
    }

    public function refresh(request $request){
      $data   = DB::table('bel')
                  ->leftjoin('bel_suara','bel.id_suara','=','bel_suara.id')
                  ->selectRaw('bel.id,
                               bel.id_sekolah,
                               bel.id_hari,
                               bel.hari,
                               bel.jam,
                               bel.id_suara,
                               bel.keterangan,
                               bel_suara.suara,
                               bel_suara.keterangan as ket')
                  ->where('bel.id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
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
      $hari   = DB::table('dt_hari')
                  ->where('id',$request->id_hari)
                  ->first();

      DB::table('bel')
        ->insert([
          'id_sekolah' => Auth::guard('simdik')->user()->id_sekolah,
          'id_hari' => $request->id_hari,
          'hari' => $hari->hari,
          'jam' => $request->jam,
          'id_suara' => $request->id_suara,
          'keterangan' => $request->keterangan,
        ]);

      $data   = DB::table('bel')
                  ->where('id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->orderby('id','desc')
                  ->first();

      return response()->json($data);
    }

    public function edit(request $request){
      $cek    = DB::table('bel')
                  ->where('id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->where('id',$request->id)
                  ->first();

      if($cek){
        $hari   = DB::table('dt_hari')
                    ->where('id',$request->id_hari)
                    ->first();

        DB::table('bel')
          ->where('id',$request->id)
          ->update([
            'id_hari' => $request->id_hari,
            'hari' => $hari->hari,
            'jam' => $request->jam,
            'id_suara' => $request->id_suara,
            'keterangan' => $request->keterangan,
          ]);

        $data   = DB::table('bel')->where('id',$request->id)->first();

        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 404);
      }
    }

    public function hapus($id){
      $cek    = DB::table('bel')
                  ->where('id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->where('id',$id)
                  ->first();

      if($cek){
        DB::table('bel')
          ->where('id',$id)
          ->delete();

        return response()->json([
          'status' => 'success',
          'message' => 'Data berhasil dihapus',
        ], 200);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 404);
      }
    }

    public function detil(request $request){
      if($request->id == 0){
        return response()->json([
          'suara' => $suara,
          'hari' => $hari,
        ]);
      } else {
        $data   = DB::table('bel')
                    ->where('id',$request->id)
                    ->first();

        if($data){
          return response()->json([
            'data' => $data,
            'suara' => $suara,
            'hari' => $hari,
          ]);
        } else {
          return response()->json([
            'status' => 'error',
            'message' => 'Data tidak ditemukan',
          ], 404);
        }
      }
    }
}
