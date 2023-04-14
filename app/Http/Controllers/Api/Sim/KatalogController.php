<?php

namespace App\Http\Controllers\Api\Sim;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;
use Auth;

class KatalogController extends Controller
{
    public function __construct(){
      $this->middleware('auth:sim');
    }

    public function index(request $request){
      if($request->id_sekolah){
        $id_sekolah = $request->id_sekolah;
      } else {
        $id_sekolah = '';
      }

      $data   = DB::table('katalog')
                  ->leftjoin('dt_katalog_jenis','katalog.id_jenis','=','dt_katalog_jenis.id')
                  ->leftjoin('dt_katalog_kategori','katalog.id_kategori','=','dt_katalog_kategori.id')
                  ->leftjoin('sekolah','katalog.id_sekolah','=','sekolah.id')
                  ->leftjoin('dt_kelas_sekolah','katalog.id_kelas_tingkat','=','dt_kelas_sekolah.id')
                  ->selectRaw('katalog.id, 
                               katalog.id_sekolah, 
                               katalog.id_kategori, 
                               katalog.id_jenis, 
                               katalog.wajib, 
                               katalog.id_kelas_tingkat, 
                               katalog.kode as kode_katalog, 
                               katalog.judul, 
                               katalog.diskripsi, 
                               katalog.penulis, 
                               katalog.penerbit, 
                               katalog.tahun_terbit, 
                               katalog.cover, 
                               katalog.gambar_1, 
                               katalog.gambar_2, 
                               katalog.file, 
                               katalog.tahun_pengadaan, 
                               katalog.bisa_dipinjam, 
                               katalog.masa_pinjam, 
                               katalog.facebook, 
                               katalog.twitter, 
                               katalog.instagram, 
                               katalog.youtube, 
                               katalog.google, 
                               katalog.buka, 
                               katalog.download, 
                               katalog.item, 
                               katalog.harga_buku, 
                               katalog.harga, 
                               katalog.denda, 
                               katalog.utama, 
                               dt_katalog_jenis.kode as kode_jenis, 
                               dt_katalog_jenis.jenis, 
                               dt_katalog_kategori.kode as kode_kategori, 
                               dt_katalog_kategori.kategori,
                               sekolah.nama,
                               sekolah.kota,
                               dt_kelas_sekolah.kelas,
                               dt_kelas_sekolah.kode,
                               dt_kelas_sekolah.sub_kelas')
                  ->where('katalog.hapus',0)
                  ->when($id_sekolah, function ($query) use ($id_sekolah) {
                      return $query->whereNot('katalog.id_sekolah',$id_sekolah);
                    })
                  ->get();

      $sekolah  = DB::table('sekolah')
                    ->where('hapus',0)
                    ->orderby('nama')
                    ->get();

      if($data) {
        return response()->json([
          'data' => $data,
          'sekolah' => $sekolah,
        ]);
      } else {
        return response()->json([
          'status' => 'error',
        ], 401);
      }
    }

    public function baru(request $request){
      $validator = Validator::make($request->all(), [
        'jenis' => 'required|String',
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
      }

      DB::table('katalog')
        ->insert([
          'kode' => $request->kode,
          'jenis' => $request->jenis,
          'diskripsi' => $request->diskripsi,
          'sim_create' => Auth::guard('sim')->user()->id,
          'sim_update' => Auth::guard('sim')->user()->id,
        ]);

      $data   = DB::table('katalog')
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
      $cek  = DB::table('katalog')
                ->where('id',$request->id)
                ->first();

      if($cek){
        $validator = Validator::make($request->all(), [
          'jenis' => 'required|String',
        ]);

        if ($validator->fails()) {
          return response()->json($validator->errors(), 422);
        }

        DB::table('katalog')
          ->where('katalog.id',$request->id)
          ->update([
            'kode' => $request->kode,
            'jenis' => $request->jenis,
            'diskripsi' => $request->diskripsi,
            'sim_update' => Auth::guard('sim')->user()->id,
          ]);

        $data   = DB::table('katalog')
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
      $cek  = DB::table('katalog')
                ->where('id',$id)
                ->first();

      if($cek){
        DB::table('katalog')
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
