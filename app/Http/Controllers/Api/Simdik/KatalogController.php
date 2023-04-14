<?php

namespace App\Http\Controllers\Api\Simdik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;
use Auth;

class KatalogController extends Controller
{
    public function __construct(){
      $this->middleware('auth:simdik');
    }

    public function index(request $request){
      if($request->id_kategori){
        $id_kategori = $request->id_kategori;
      } else {
        $id_kategori = '';
      }

      if($request->id_jenis){
        $id_jenis = $request->id_jenis;
      } else {
        $id_jenis = '';
      }

      if($request->id_kelas){
        $id_kelas = $request->id_kelas;
      } else {
        $id_kelas = '';
      }

      $kategori   = DB::table('dt_katalog_kategori')
                      ->where('hapus',0)
                      ->orderby('kategori')
                      ->orderby('diskripsi')
                      ->get();

      $jenis      = DB::table('dt_katalog_jenis')
                      ->where('hapus',0)
                      ->orderby('id')
                      ->get();

      $sekolah    = DB::table('sekolah')
                      ->where('id',Auth::guard('simdik')->user()->id_sekolah)
                      ->first();

      $kelas      = DB::table('dt_kelas')
                      ->where('strata',$sekolah->bentuk_pendidikan)
                      ->get();

      $data   = DB::table('katalog')
                  ->leftjoin('dt_katalog_kategori','katalog.id_kategori','=','dt_katalog_kategori.id')
                  ->leftjoin('dt_katalog_jenis','katalog.id_jenis','=','dt_katalog_jenis.id')
                  ->selectRaw('katalog.id, 
                               katalog.id_sekolah, 
                               katalog.id_kategori, 
                               katalog.id_jenis, 
                               katalog.wajib, 
                               katalog.id_kelas_tingkat, 
                               dt_katalog_jenis.kode as jenis_kode,
                               dt_katalog_jenis.jenis,
                               dt_katalog_kategori.kode as kategori_kode,
                               dt_katalog_kategori.kategori,
                               dt_katalog_kategori.diskripsi as kategori_diskripsi,
                               katalog.kode, 
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
                               katalog.buka,
                               katalog.download, 
                               (SELECT COUNT(katalog_item.id)
                                FROM katalog_item
                                WHERE katalog_item.id_katalog = katalog.id) as item, 
                               (SELECT COUNT(katalog_item.id)
                                FROM katalog_item
                                WHERE katalog_item.id_katalog = katalog.id
                                AND katalog_item.ada = 1) as ada, 
                               (SELECT COUNT(katalog_item.id)
                                FROM katalog_item
                                WHERE katalog_item.id_katalog = katalog.id
                                AND katalog_item.ada = 0) as dipinjam, 
                               (SELECT COUNT(katalog_item.id)
                                FROM katalog_item
                                WHERE katalog_item.id_katalog = katalog.id
                                AND katalog_item.hilang = 1) as hilang, 
                               katalog.harga_buku, 
                               katalog.harga, 
                               katalog.denda, 
                               katalog.utama')
                  ->where('katalog.id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->where('katalog.hapus',0)
                  ->when($id_kategori, function ($query) use($id_kategori) {
                      return $query->where('katalog.id_kategori',$id_kategori);
                    })
                  ->when($id_jenis, function ($query) use($id_jenis) {
                      return $query->where('katalog.id_jenis',$id_jenis);
                    })
                  ->when($id_kelas, function ($query) use($id_kelas) {
                      return $query->where('katalog.id_kelas_tingkat',$id_kelas);
                    })
                  ->get();

      if($data){
        return response()->json([
          'data' => $data,
          'kategori' => $kategori,
          'jenis' => $jenis,
          'kelas' => $kelas,
        ]);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Error',
        ], 401);
      }      
    }

    public function baru(request $request){
      DB::table('katalog')
        ->insert([
          'id_sekolah' => Auth::guard('simdik')->user()->id_sekolah, 
          'id_kategori' => $request->id_kategori, 
          'id_jenis' => $request->id_jenis, 
          'wajib' => $request->wajib, 
          'id_kelas_tingkat' => $request->id_kelas_tingkat, 
          'kode' => $request->kode, 
          'judul' => $request->judul, 
          'diskripsi' => $request->diskripsi, 
          'penulis' => $request->penulis, 
          'penerbit' => $request->penerbit, 
          'tahun_terbit' => $request->tahun_terbit, 
          'gambar_1' => $request->gambar_1, 
          'gambar_2' => $request->gambar_2, 
          'tahun_pengadaan' => $request->tahun_pengadaan, 
          'bisa_dipinjam' => $request->bisa_dipinjam, 
          'masa_pinjam' => $request->masa_pinjam, 
          'facebook' => $request->facebook, 
          'twitter' => $request->twitter, 
          'instagram' => $request->instagram, 
          'youtube' => $request->youtube, 
          'google' => $request->google, 
          'buka' => $request->buka, 
          'download' => $request->download, 
          'item' => $request->item, 
          'harga_buku' => $request->harga_buku, 
          'harga' => $request->harga, 
          'denda' => $request->denda, 
          'simdik_create' => Auth::guard('simdik')->user()->id,
          'simdik_update' => Auth::guard('simdik')->user()->id,
        ]);

      $data   = DB::table('katalog')
                  ->where('id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->orderby('id','desc')
                  ->first();

      return response()->json($data);
    }

    public function edit(request $request){
      DB::table('katalog')
        ->where('id',$request->id)
        ->update([
          'id_kategori' => $request->id_kategori, 
          'id_jenis' => $request->id_jenis, 
          'wajib' => $request->wajib, 
          'id_kelas_tingkat' => $request->id_kelas_tingkat, 
          'kode' => $request->kode, 
          'judul' => $request->judul, 
          'diskripsi' => $request->diskripsi, 
          'penulis' => $request->penulis, 
          'penerbit' => $request->penerbit, 
          'tahun_terbit' => $request->tahun_terbit, 
          'gambar_1' => $request->gambar_1, 
          'gambar_2' => $request->gambar_2, 
          'tahun_pengadaan' => $request->tahun_pengadaan, 
          'bisa_dipinjam' => $request->bisa_dipinjam, 
          'masa_pinjam' => $request->masa_pinjam, 
          'facebook' => $request->facebook, 
          'twitter' => $request->twitter, 
          'instagram' => $request->instagram, 
          'youtube' => $request->youtube, 
          'google' => $request->google, 
          'buka' => $request->buka, 
          'download' => $request->download, 
          'item' => $request->item, 
          'harga_buku' => $request->harga_buku, 
          'harga' => $request->harga, 
          'denda' => $request->denda, 
          'simdik_update' => Auth::guard('simdik')->user()->id,
        ]);

      if($request->cover){
        DB::table('katalog')
          ->where('id',$request->id)
          ->update([
            'cover' => $request->cover,
          ]);
      }

      $data   = DB::table('katalog')
                  ->where('id',$request->id)
                  ->first();

      return response()->json($data);
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
            'simdik_update' => Auth::guard('simdik')->user()->id,
          ]);

        return response()->json(['status' => 'success'], 200);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }

    public function detil(request $request){
      $kategori   = DB::table('dt_katalog_kategori')
                      ->where('hapus',0)
                      ->orderby('kategori')
                      ->orderby('diskripsi')
                      ->get();

      $jenis      = DB::table('dt_katalog_jenis')
                      ->where('hapus',0)
                      ->orderby('id')
                      ->get();

      $sekolah    = DB::table('sekolah')
                      ->where('id',Auth::guard('simdik')->user()->id_sekolah)
                      ->first();

      $kelas      = DB::table('dt_kelas')
                      ->where('strata',$sekolah->bentuk_pendidikan)
                      ->get();

      if($request->id == 0){
        return response()->json([
          'kategori' => $kategori,
          'jenis' => $jenis,
          'kelas' => $kelas,
        ]);
      } else {
        $data   = DB::table('katalog')
                    ->where('katalog.id',$request->id)
                    ->selectRaw('katalog.id, 
                                 katalog.id_sekolah, 
                                 katalog.id_kategori, 
                                 katalog.id_jenis, 
                                 katalog.wajib, 
                                 katalog.id_kelas_tingkat, 
                                 katalog.kode, 
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
                                 (SELECT COUNT(katalog_item.id)
                                  FROM katalog_item
                                  WHERE katalog_item.id_katalog = katalog.id) as item')
                    ->first();

        $item       = DB::table('katalog_item')
                      ->where('id_katalog',$request->id)
                      ->get();

        if($data){
          return response()->json([
            'data' => $data,
            'item' => $item,
            'kategori' => $kategori,
            'jenis' => $jenis,
            'kelas' => $kelas,
          ]);
        } else {
          return response()->json([
            'status' => 'error',
            'message' => 'Data tidak ditemukan',
          ], 401);
        }
      }      
    }

    public function generate(request $request){
      $data   = DB::table('katalog')
                  ->leftjoin('dt_katalog_jenis','katalog.id_jenis','=','dt_katalog_jenis.id')
                  ->leftjoin('dt_katalog_kategori','katalog.id_kategori','=','dt_katalog_kategori.id')
                  ->selectRaw('katalog.id,
                               katalog.id_kategori,
                               katalog.id_jenis,
                               dt_katalog_jenis.kode as kode_jenis,
                               dt_katalog_kategori.kode as kode_kategori,
                               katalog.kode,
                               katalog.judul')
                  ->where('katalog.id',$request->id)
                  ->first();

      if($data){
        $i = 1;

        for($i = 1; $i <= $request->item; $i++){
          DB::table('katalog_item')
            ->insert([
              'id_sekolah' => $request->id_sekolah,
              'id_kategori' => $data->id_kategori,
              'id_jenis' => $data->id_jenis,
              'id_katalog' => $data->id,
              'kode' => $data->kode_jenis.$data->kode_kategori.str_pad($i,4,'0',STR_PAD_LEFT),
              'nomor' => $i,
            ]);
        }

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

    public function katalog_item(){
      $data   = DB::table('katalog_item')
                  ->leftjoin('katalog','katalog_item.id_katalog','=','katalog.id')
                  ->leftjoin('siswas','katalog_item.id_siswa_peminjam','=','siswas.id')
                  ->leftjoin('simdiks','katalog_item.id_guru_peminjam','=','simdiks.id')
                  ->selectRaw('katalog_item.id,
                               katalog.judul,
                               katalog_item.kode,
                               katalog_item.ada,
                               katalog_item.hilang,
                               katalog_item.id_siswa_peminjam,
                               katalog_item.id_guru_peminjam,
                               siswas.nama as siswa,
                               siswas.nis,
                               simdiks.nama as guru,
                               simdiks.gelar_depan,
                               simdiks.gelar_belakang,
                               katalog_item.tgl_pinjam,
                               katalog_item.tgl_harus_kembali')
                  ->where('katalog_item.id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->orderby('katalog_item.kode')
                  ->get();

      return response()->json($data);
    }
}
