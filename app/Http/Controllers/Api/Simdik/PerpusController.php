<?php

namespace App\Http\Controllers\Api\Simdik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;
use Auth;

class PerpusController extends Controller
{
    public function __construct(){
      $this->middleware('auth:simdik');
    }

    public function data_katalog_dipinjam(request $request){
      $data   = DB::table('katalog_pinjam')
                  ->leftjoin('katalog','katalog_pinjam.id_katalog','=','katalog.id')
                  ->leftjoin('siswas','katalog_pinjam.id_siswa','=','siswas.id')
                  ->leftjoin('dt_kelas_sekolah','siswas.id_kelas_sekolah','=','dt_kelas_sekolah.id')
                  ->leftjoin('simdiks','katalog_pinjam.id_guru','=','simdiks.id')
                  ->selectRaw('katalog_pinjam.id,
                               katalog_pinjam.id_siswa,
                               katalog_pinjam.id_guru,
                               katalog_pinjam.id_katalog,
                               katalog_pinjam.id_katalog_item,
                               katalog_pinjam.barcode,
                               katalog_pinjam.tgl_pinjam,
                               katalog_pinjam.tgl_harus_kembali,
                               katalog_pinjam.tgl_kembali,
                               katalog_pinjam.kembali,
                               siswas.nama as siswa,
                               siswas.nis,
                               simdiks.nama as guru,
                               simdiks.gelar_depan,
                               simdiks.gelar_belakang,
                               dt_kelas_sekolah.kelas,
                               dt_kelas_sekolah.kode as kode_kelas,
                               dt_kelas_sekolah.sub_kelas,
                               IF(katalog_pinjam.id_siswa IS NOT NULL,
                               katalog.denda * 
                               IF(DATEDIFF(CURDATE(),katalog_pinjam.tgl_harus_kembali) > 0, DATEDIFF(CURDATE(),katalog_pinjam.tgl_harus_kembali), 0),0) as denda,
                               katalog.judul,
                               katalog.cover')
                  ->where('katalog_pinjam.kembali',0)
                  ->orderby('katalog_pinjam.denda')
                  ->get();

      return response()->json($data);
    }

    public function katalog_dipinjam(request $request){
      if($request->idSiswa){
        $idSiswa = $request->idSiswa;
      } else {
        $idSiswa = '';
      }

      if($request->idGuru){
        $idGuru = $request->idGuru;
      } else {
        $idGuru = '';
      }

      $data   = DB::table('katalog_pinjam')
                  ->leftjoin('katalog','katalog_pinjam.id_katalog','=','katalog.id')
                  ->selectRaw('katalog_pinjam.id,
                               katalog_pinjam.id_siswa,
                               katalog_pinjam.id_guru,
                               katalog_pinjam.id_katalog,
                               katalog_pinjam.id_katalog_item,
                               katalog_pinjam.barcode,
                               katalog_pinjam.tgl_pinjam,
                               katalog_pinjam.tgl_harus_kembali,
                               katalog_pinjam.tgl_kembali,
                               katalog_pinjam.kembali,
                               IF(katalog_pinjam.id_siswa IS NOT NULL,
                               katalog.denda * 
                               IF(DATEDIFF(CURDATE(),katalog_pinjam.tgl_harus_kembali) > 0, DATEDIFF(CURDATE(),katalog_pinjam.tgl_harus_kembali), 0),0) as denda,
                               katalog.judul,
                               katalog.cover')
                  ->when($idSiswa, function ($query) use($idSiswa) {
                      return $query->where('katalog_pinjam.id_siswa',$idSiswa);
                    })
                  ->when($idGuru, function ($query) use($idGuru) {
                      return $query->where('katalog_pinjam.id_guru',$idGuru);
                    })
                  ->where('katalog_pinjam.kembali',0)
                  ->orderby('katalog_pinjam.kembali')
                  ->get();

      return response()->json($data);
    }

    public function katalog_pinjam(request $request){
      if($request->id_siswa){
        $id_siswa = $request->id_siswa;
      } else {
        $id_siswa = null;
      }

      if($request->id_guru){
        $id_guru = $request->id_guru;
      } else {
        $id_guru = null;
      }

      $item   = DB::table('katalog_item')
                  ->leftjoin('katalog','katalog_item.id_katalog','=','katalog.id')
                  ->selectRaw('katalog_item.id,
                               katalog_item.kode,
                               katalog_item.id_katalog,
                               katalog_item.ada,
                               katalog.masa_pinjam,
                               DATE_ADD(CURDATE(), INTERVAL katalog.masa_pinjam DAY) as kembali')
                  ->where('katalog_item.kode',$request->barcode)
                  ->first();

      if($item){
        if($item->ada == 1){
          DB::table('katalog_pinjam')
            ->insert([
              'id_sekolah' => Auth::guard('simdik')->user()->id_sekolah,
              'id_siswa' => $id_siswa,
              'id_guru' => $id_guru,
              'id_katalog' => $item->id_katalog,
              'id_katalog_item' => $item->id,
              'barcode' => $request->barcode,
              'tgl_pinjam' => date("Y-m-d"),
              'tgl_harus_kembali' => $item->kembali,
              'petugas_create' => Auth::guard('simdik')->user()->id,
              'petugas_update' => Auth::guard('simdik')->user()->id,
            ]);

          DB::table('katalog_item')
            ->where('id',$item->id)
            ->update([
              'ada' => 0,
              'id_siswa_peminjam' => $id_siswa,
              'id_guru_peminjam' => $id_guru,
              'tgl_pinjam' => date("Y-m-d"),
              'tgl_harus_kembali' => $item->kembali,
              'petugas_update' => Auth::guard('simdik')->user()->id,
            ]);

          $data   = DB::table('katalog_pinjam')
                      ->leftjoin('katalog','katalog_pinjam.id_katalog','=','katalog.id')
                      ->leftjoin('dt_katalog_jenis','katalog.id_jenis','=','dt_katalog_jenis.id')
                      ->leftjoin('dt_katalog_kategori','katalog.id_kategori','=','dt_katalog_kategori.id')
                      ->selectRaw('katalog_pinjam.id,
                                   katalog_pinjam.id_siswa,
                                   katalog_pinjam.id_guru,
                                   katalog_pinjam.id_katalog,
                                   katalog_pinjam.id_katalog_item,
                                   katalog_pinjam.barcode,
                                   katalog_pinjam.tgl_pinjam,
                                   katalog_pinjam.tgl_harus_kembali,
                                   katalog_pinjam.tgl_kembali,
                                   katalog_pinjam.kembali,
                                   katalog_pinjam.denda,
                                   katalog.judul,
                                   katalog.cover,
                                   katalog.kode,
                                   dt_katalog_jenis.kode as kode_jenis,
                                   dt_katalog_kategori.kode as kode_kategori')
                      ->when($id_siswa, function ($query) use($id_siswa) {
                          return $query->where('katalog_pinjam.id_siswa',$id_siswa);
                        })
                      ->when($id_guru, function ($query) use($id_guru) {
                          return $query->where('katalog_pinjam.id_guru',$id_guru);
                        })
                      ->where('katalog_pinjam.kembali',0)
                      ->get();

          return response()->json($data);
        } else {
          return response()->json([
          'status' => 'error',
          'message' => 'Buku dengan barcode '.$request->barcode.' berstatus DIPINJAM',
        ], 404);
        }
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Barcode '.$request->barcode.' tidak ditemukan',
        ], 404);
      }
    }

    public function katalog_kembali(request $request){
      if($request->barcode){
        $barcode = $request->barcode;
      } else {
        $barcode = '';
      }

      if($request->id){
        $id = $request->id;
      } else {
        $id = '';
      }

      $cek  = DB::table('katalog_pinjam')
                ->leftjoin('katalog','katalog_pinjam.id_katalog','=','katalog.id')
                ->selectRaw('katalog_pinjam.id,
                             katalog_pinjam.barcode,
                             katalog_pinjam.id_katalog_item,
                             katalog_pinjam.id_siswa,
                             IF(katalog_pinjam.id_siswa IS NOT NULL,
                             katalog.denda * 
                             IF(DATEDIFF(CURDATE(),katalog_pinjam.tgl_harus_kembali) > 0, DATEDIFF(CURDATE(),katalog_pinjam.tgl_harus_kembali), 0),0) as denda')
                ->where('katalog_pinjam.id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                ->where('katalog_pinjam.kembali',0)
                ->when($barcode, function ($query) use($barcode) {
                    return $query->where('katalog_pinjam.barcode',$barcode);
                  })
                ->when($id, function ($query) use($id) {
                    return $query->where('katalog_pinjam.id',$id);
                  })
                ->first();

      if($cek){
        DB::table('katalog_pinjam')
          ->where('id',$cek->id)
          ->update([
            'tgl_kembali' => date("Y-m-d"),
            'kembali' => 1,
            'denda' => $cek->denda,
            'petugas_update' => Auth::guard('simdik')->user()->id,
          ]);

        DB::table('katalog_item')
          ->where('id',$cek->id_katalog_item)
          ->update([
            'ada' => 1,
            'id_siswa_peminjam' => null,
            'id_guru_peminjam' => null,
            'tgl_pinjam' => null,
            'tgl_harus_kembali' => null,
            'tgl_kembali' => null,
            'petugas_update' => Auth::guard('simdik')->user()->id,
          ]);

        if($cek->denda > 0 && $cek->id_siswa){
          $kas  = DB::table('perpus_kas')
                    ->where('id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                    ->orderby('id','desc')
                    ->first();

          if($kas){
            $sisa   = $kas->sisa;
          } else {
            $sisa   = 0;
          }

          DB::table('perpus_kas')
            ->insert([
              'id_sekolah' => Auth::guard('simdik')->user()->id_sekolah,
              'waktu' => now(),
              'id_siswa' => $cek->id_siswa,
              'id_pinjam' => $cek->id,
              'id_petugas' => Auth::guard('simdik')->user()->id,
              'keterangan' => 'Denda keterlambatan pengembalian',
              'masuk' => $cek->denda,
              'sisa' => $cek->denda + $sisa,
              'petugas_create' => Auth::guard('simdik')->user()->id,
            ]);
        }

        return response()->json([
          'status' => 'success',
          'message' => 'Barcode '.$cek->barcode.' berhasil dikembalikan',
        ], 200);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Barcode '.$request->barcode.' tidak ditemukan',
        ], 404);
      }
    }

    public function katalog_kembali_barcode(request $request){
      $cek  = DB::table('katalog_pinjam')
                ->leftjoin('katalog','katalog_pinjam.id_katalog','=','katalog.id')
                ->selectRaw('katalog_pinjam.id,
                             katalog_pinjam.barcode,
                             katalog_pinjam.id_katalog_item,
                             katalog_pinjam.id_siswa,
                             katalog_pinjam.id_guru,
                             katalog.judul,
                             IF(katalog_pinjam.id_siswa IS NOT NULL,
                             katalog.denda * 
                             IF(DATEDIFF(CURDATE(),katalog_pinjam.tgl_harus_kembali) > 0, DATEDIFF(CURDATE(),katalog_pinjam.tgl_harus_kembali), 0),0) as denda')
                ->where('katalog_pinjam.id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                ->where('katalog_pinjam.kembali',0)
                ->where('katalog_pinjam.barcode',$request->barcode)
                ->first();

      if($cek){
        if($cek->id_siswa){
          $siswa  = DB::table('siswas')
                      ->leftjoin('dt_kelas_sekolah','siswas.id_kelas_sekolah','=','dt_kelas_sekolah.id')
                      ->selectRaw('siswas.id,
                                   siswas.foto,
                                   siswas.nama,
                                   siswas.nis,
                                   dt_kelas_sekolah.kelas,
                                   dt_kelas_sekolah.kode,
                                   dt_kelas_sekolah.sub_kelas')
                      ->where('siswas.id',$cek->id_siswa)
                      ->first();

          $buku   = DB::table('katalog_pinjam')
                      ->leftjoin('katalog','katalog_pinjam.id_katalog','=','katalog.id')
                      ->leftjoin('siswas','katalog_pinjam.id_siswa','=','siswas.id')
                      ->leftjoin('dt_kelas_sekolah','siswas.id_kelas_sekolah','=','dt_kelas_sekolah.id')
                      ->leftjoin('simdiks','katalog_pinjam.id_guru','=','simdiks.id')
                      ->selectRaw('katalog_pinjam.id,
                                   katalog_pinjam.id_siswa,
                                   katalog_pinjam.id_guru,
                                   katalog_pinjam.id_katalog,
                                   katalog_pinjam.id_katalog_item,
                                   katalog_pinjam.barcode,
                                   katalog_pinjam.tgl_pinjam,
                                   katalog_pinjam.tgl_harus_kembali,
                                   katalog_pinjam.tgl_kembali,
                                   katalog_pinjam.kembali,
                                   siswas.nama as siswa,
                                   siswas.nis,
                                   simdiks.nama as guru,
                                   simdiks.gelar_depan,
                                   simdiks.gelar_belakang,
                                   dt_kelas_sekolah.kelas,
                                   dt_kelas_sekolah.kode as kode_kelas,
                                   dt_kelas_sekolah.sub_kelas,
                                   IF(katalog_pinjam.id_siswa IS NOT NULL,
                                   katalog.denda * 
                                   IF(DATEDIFF(CURDATE(),katalog_pinjam.tgl_harus_kembali) > 0, DATEDIFF(CURDATE(),katalog_pinjam.tgl_harus_kembali), 0),0) as denda,
                                   katalog.judul,
                                   katalog.cover')
                      ->where('katalog_pinjam.kembali',0)
                      ->WHERE('katalog_pinjam.id_siswa',$siswa->id)
                      ->orderby('katalog_pinjam.denda')
                      ->get();
        } else {
          $siswa  = '';
        }

        if($cek->id_guru){
          $guru   = DB::table('simdiks')
                      ->selectRaw('simdiks.id,
                                   simdiks.foto,
                                   simdiks.nama,
                                   simdiks.gelar_depan,
                                   simdiks.gelar_belakang,
                                   simdiks.nip')
                      ->where('simdiks.id',$cek->id_guru)
                      ->first();

          $buku   = DB::table('katalog_pinjam')
                      ->leftjoin('katalog','katalog_pinjam.id_katalog','=','katalog.id')
                      ->leftjoin('siswas','katalog_pinjam.id_siswa','=','siswas.id')
                      ->leftjoin('dt_kelas_sekolah','siswas.id_kelas_sekolah','=','dt_kelas_sekolah.id')
                      ->leftjoin('simdiks','katalog_pinjam.id_guru','=','simdiks.id')
                      ->selectRaw('katalog_pinjam.id,
                                   katalog_pinjam.id_siswa,
                                   katalog_pinjam.id_guru,
                                   katalog_pinjam.id_katalog,
                                   katalog_pinjam.id_katalog_item,
                                   katalog_pinjam.barcode,
                                   katalog_pinjam.tgl_pinjam,
                                   katalog_pinjam.tgl_harus_kembali,
                                   katalog_pinjam.tgl_kembali,
                                   katalog_pinjam.kembali,
                                   siswas.nama as siswa,
                                   siswas.nis,
                                   simdiks.nama as guru,
                                   simdiks.gelar_depan,
                                   simdiks.gelar_belakang,
                                   dt_kelas_sekolah.kelas,
                                   dt_kelas_sekolah.kode as kode_kelas,
                                   dt_kelas_sekolah.sub_kelas,
                                   IF(katalog_pinjam.id_siswa IS NOT NULL,
                                   katalog.denda * 
                                   IF(DATEDIFF(CURDATE(),katalog_pinjam.tgl_harus_kembali) > 0, DATEDIFF(CURDATE(),katalog_pinjam.tgl_harus_kembali), 0),0) as denda,
                                   katalog.judul,
                                   katalog.cover')
                      ->where('katalog_pinjam.kembali',0)
                      ->WHERE('katalog_pinjam.id_guru',$guru->id)
                      ->orderby('katalog_pinjam.denda')
                      ->get();
        } else {
          $guru  = '';
        }

        return response()->json([
          'siswa' => $siswa,
          'guru' => $guru,
          'buku' => $buku,
          'cek' => $cek,
        ]);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Barcode '.$request->barcode.' tidak ditemukan',
        ], 404);
      }
    }

    public function katalog_kembali_nis(request $request){
      $siswa  = DB::table('siswas')
                  ->leftjoin('dt_kelas_sekolah','siswas.id_kelas_sekolah','=','dt_kelas_sekolah.id')
                  ->selectRaw('siswas.id,
                               siswas.foto,
                               siswas.nama,
                               siswas.nis,
                               dt_kelas_sekolah.kelas,
                               dt_kelas_sekolah.kode,
                               dt_kelas_sekolah.sub_kelas')
                  ->where('siswas.nis',$request->nis)
                  ->where('siswas.id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->first();

      if($siswa){
        $buku   = DB::table('katalog_pinjam')
                    ->leftjoin('katalog','katalog_pinjam.id_katalog','=','katalog.id')
                    ->leftjoin('siswas','katalog_pinjam.id_siswa','=','siswas.id')
                    ->leftjoin('dt_kelas_sekolah','siswas.id_kelas_sekolah','=','dt_kelas_sekolah.id')
                    ->leftjoin('simdiks','katalog_pinjam.id_guru','=','simdiks.id')
                    ->selectRaw('katalog_pinjam.id,
                                 katalog_pinjam.id_siswa,
                                 katalog_pinjam.id_guru,
                                 katalog_pinjam.id_katalog,
                                 katalog_pinjam.id_katalog_item,
                                 katalog_pinjam.barcode,
                                 katalog_pinjam.tgl_pinjam,
                                 katalog_pinjam.tgl_harus_kembali,
                                 katalog_pinjam.tgl_kembali,
                                 katalog_pinjam.kembali,
                                 siswas.nama as siswa,
                                 siswas.nis,
                                 simdiks.nama as guru,
                                 simdiks.gelar_depan,
                                 simdiks.gelar_belakang,
                                 dt_kelas_sekolah.kelas,
                                 dt_kelas_sekolah.kode as kode_kelas,
                                 dt_kelas_sekolah.sub_kelas,
                                 IF(katalog_pinjam.id_siswa IS NOT NULL,
                                 katalog.denda * 
                                 IF(DATEDIFF(CURDATE(),katalog_pinjam.tgl_harus_kembali) > 0, DATEDIFF(CURDATE(),katalog_pinjam.tgl_harus_kembali), 0),0) as denda,
                                 katalog.judul,
                                 katalog.cover')
                    ->where('katalog_pinjam.kembali',0)
                    ->WHERE('katalog_pinjam.id_siswa',$siswa->id)
                    ->orderby('katalog_pinjam.denda')
                    ->get();

        return response()->json([
          'siswa' => $siswa,
          'buku' => $buku,
        ]);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data siswa tidak ditemukan',
        ], 404);
      }
    }

    public function katalog_kembali_nip(request $request){
      $guru  = DB::table('simdiks')
                  ->where('simdiks.nip',$request->nip)
                  ->where('simdiks.id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->first();

      if($guru){
        $buku   = DB::table('katalog_pinjam')
                    ->leftjoin('katalog','katalog_pinjam.id_katalog','=','katalog.id')
                    ->leftjoin('siswas','katalog_pinjam.id_siswa','=','siswas.id')
                    ->leftjoin('dt_kelas_sekolah','siswas.id_kelas_sekolah','=','dt_kelas_sekolah.id')
                    ->leftjoin('simdiks','katalog_pinjam.id_guru','=','simdiks.id')
                    ->selectRaw('katalog_pinjam.id,
                                 katalog_pinjam.id_siswa,
                                 katalog_pinjam.id_guru,
                                 katalog_pinjam.id_katalog,
                                 katalog_pinjam.id_katalog_item,
                                 katalog_pinjam.barcode,
                                 katalog_pinjam.tgl_pinjam,
                                 katalog_pinjam.tgl_harus_kembali,
                                 katalog_pinjam.tgl_kembali,
                                 katalog_pinjam.kembali,
                                 siswas.nama as siswa,
                                 siswas.nis,
                                 simdiks.nama as guru,
                                 simdiks.gelar_depan,
                                 simdiks.gelar_belakang,
                                 dt_kelas_sekolah.kelas,
                                 dt_kelas_sekolah.kode as kode_kelas,
                                 dt_kelas_sekolah.sub_kelas,
                                 IF(katalog_pinjam.id_siswa IS NOT NULL,
                                 katalog.denda * 
                                 IF(DATEDIFF(CURDATE(),katalog_pinjam.tgl_harus_kembali) > 0, DATEDIFF(CURDATE(),katalog_pinjam.tgl_harus_kembali), 0),0) as denda,
                                 katalog.judul,
                                 katalog.cover')
                    ->where('katalog_pinjam.kembali',0)
                    ->WHERE('katalog_pinjam.id_guru',$guru->id)
                    ->orderby('katalog_pinjam.denda')
                    ->get();

        return response()->json([
          'guru' => $guru,
          'buku' => $buku,
        ]);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data guru tidak ditemukan',
        ], 404);
      }
    }
}
