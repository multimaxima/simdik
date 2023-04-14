<?php

namespace App\Http\Controllers\Api\Simdik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;
use Auth;

class BelEventController extends Controller
{
    public function __construct(){
      $this->middleware('auth:simdik');
    }

    public function index(request $request){
      $data   = DB::table('bel_event')
                  ->leftjoin('bel_suara','bel_event.id_suara','=','bel_suara.id')
                  ->selectRaw('bel_event.id,
                               bel_event.id_sekolah,
                               bel_event.tanggal,
                               bel_event.jam,
                               bel_event.id_suara,
                               bel_event.keterangan,
                               bel_suara.suara')
                  ->where('bel_event.id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->orderby('bel_event.tanggal')
                  ->orderby('bel_event.jam')
                  ->get();

      return response()->json($data);
    }

    public function baru(request $request){
      DB::table('bel_event')
        ->insert([
          'id_sekolah' => Auth::guard('simdik')->user()->id_sekolah,
          'tanggal' => $request->tanggal ? date('Y-m-d', strtotime($request->tanggal)) : null,
          'jam' => $request->jam,
          'id_suara' => $request->id_suara,
          'keterangan' => $request->keterangan,
          'simdik_create' => Auth::guard('simdik')->user()->id,
          'simdik_update' => Auth::guard('simdik')->user()->id,
        ]);

      $data   = DB::table('bel_event')
                  ->where('bel_event.id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->orderby('id','desc')
                  ->first();

      return response()->json($data);
    }

    public function edit(request $request){
      $cek   = DB::table('bel_event')
                  ->where('id',$request->id)
                  ->where('id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->first();

      if($cek){
        DB::table('bel_event')
          ->where('id',$request->id)
          ->update([
            'tanggal' => $request->tanggal ? date('Y-m-d', strtotime($request->tanggal)) : null,
            'jam' => $request->jam,
            'id_suara' => $request->id_suara,
            'keterangan' => $request->keterangan,
            'simdik_update' => Auth::guard('simdik')->user()->id,
          ]);

        $data   = DB::table('bel_event')
                    ->where('id',$request->id)
                    ->first();

        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }

    public function hapus($id){
      $cek    = DB::table('bel_event')
                  ->where('id',$id)
                  ->where('id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->first();

      if($cek){
        DB::table('bel_event')
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
        ], 401);
      }
    }

    public function detil($id){
      $data   = DB::table('bel_event')
                  ->where('id',$id)
                  ->where('id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->first();

      if($data){
        return response()->json($data);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }
}
