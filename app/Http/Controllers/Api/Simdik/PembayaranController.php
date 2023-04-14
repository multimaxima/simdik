<?php

namespace App\Http\Controllers\Api\Simdik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;
use Auth;

class PembayaranController extends Controller
{
    public function __construct(){
      $this->middleware('auth:simdik');
    }

    public function index(){
      $data   = DB::table('sekolah_pembayaran')
                  ->where('id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
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
      DB::table('sekolah_pembayaran')
        ->insert([
          'id_sekolah' => Auth::guard('simdik')->user()->id_sekolah,
          'jenis' => $request->jenis,
          'keterangan' => $request->keterangan,
          'tanggal' => $request->tanggal,
          'akhir' => $request->akhir ? date('Y-m-d', strtotime($request->akhir)) : null,
          'nominal' => $request->nominal,
          'simdik_create' => Auth::guard('simdik')->user()->id,
          'simdik_update' => Auth::guard('simdik')->user()->id,
        ]);

      $data   = DB::table('sekolah_pembayaran')
                  ->where('id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->orderby('id','desc')
                  ->first();

      return response()->json($data);
    }

    public function edit(request $request){
      $cek  = DB::table('sekolah_pembayaran')
                ->where('id',$request->id)
                ->where('id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                ->first();

      if($cek){
        DB::table('sekolah_pembayaran')
          ->where('id',$request->id)
          ->update([
            'jenis' => $request->jenis,
            'keterangan' => $request->keterangan,
            'tanggal' => $request->tanggal,
            'akhir' => $request->akhir ? date('Y-m-d', strtotime($request->akhir)) : null,
            'nominal' => $request->nominal,
            'simdik_update' => Auth::guard('simdik')->user()->id,
          ]);

        $data   = DB::table('sekolah_pembayaran')
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
      $cek  = DB::table('sekolah_pembayaran')
                ->where('id',$id)
                ->where('id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                ->first();

      if($cek){
        DB::table('sekolah_pembayaran')
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
      $data  = DB::table('sekolah_pembayaran')
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
