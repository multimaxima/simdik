<?php

namespace App\Http\Controllers\Api\Simdik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\bankSoal;
use App\Models\bankSoalJawaban;
use Validator;
use DB;
use Auth;

class BankSoalController extends Controller
{
    public function __construct(){
      $this->middleware('auth:simdik');
    }

    public function buka(request $request){
      $strata   = DB::table('sekolah')
                    ->where('id',Auth::guard('simdik')->user()->id_sekolah)
                    ->first();

      $kelas    = DB::table('dt_kelas')
                    ->where('strata',$strata->strata)
                    ->orderby('dt_kelas.id')
                    ->get();

      $mapel    = DB::table('dt_mapel')
                    ->when($strata->strata == 1, function ($query) {
                        return $query->where('dt_mapel.tingkat_1',1);
                      })
                    ->when($strata->strata == 2, function ($query) {
                        return $query->where('dt_mapel.tingkat_2',1);
                      })
                    ->when($strata->strata == 3, function ($query) {
                        return $query->where('dt_mapel.tingkat_3',1);
                      })
                    ->where('dt_mapel.id','>',2)
                    ->orderby('dt_mapel.mapel')
                    ->get();

      return response()->json([
        'kelas' => $kelas,
        'mapel' => $mapel,
      ]);
    }

    public function index(request $request){
      $strata   = DB::table('sekolah')
                    ->where('id',Auth::guard('simdik')->user()->id_sekolah)
                    ->first();

      $data = bankSoal::with(['jawaban'])
                      ->where('bank_soal.strata',$strata->strata)
                      ->where('bank_soal.id_kelas',$request->id_kelas)
                      ->where('bank_soal.id_mapel',$request->id_mapel)
                      ->get();

      return response()->json($data);
    }

    public function baru(request $request){
      DB::table('bank_soal')
        ->insert([
          'strata' => $request->strata,
          'id_kelas' => $request->id_kelas,
          'id_mapel' => $request->id_mapel,
          'jenis' => $request->jenis,
          'soal' => $request->soal,
          'simdik_create' => Auth::guard('simdik')->user()->id,
          'simdik_update' => Auth::guard('simdik')->user()->id,
        ]);

      $data   = DB::table('bank_soal')
                  ->orderby('id','desc')
                  ->first();

      if($request->jawaban){
        foreach($request->jawaban as $jawab){
          DB::table('bank_soal_jawaban')
            ->insert([
              'id_bank_soal' => $data->id,
              'jawaban' => $jawab['jawaban'],
              'kunci' => $jawab['kunci'] == 1 ? 1 : 0,
            ]);
        }
      }

      return response()->json([
        'status' => 'success',
        'message' => 'Berhasil',
      ], 200);
    }

    public function edit(request $request){
      $cek  = DB::table('bank_soal')
                ->where('id',$request->id)
                ->first();

      if($cek){
        DB::table('bank_soal')
          ->where('id',$request->id)
          ->update([
            'strata' => $request->strata,
            'id_kelas' => $request->id_kelas,
            'id_mapel' => $request->id_mapel,
            'jenis' => $request->jenis,
            'soal' => $request->soal,
            'simdik_update' => Auth::guard('simdik')->user()->id,
          ]);

        if($request->jawaban){
          foreach($request->jawaban as $jawab){
            if($jawab['id']){
              DB::table('bank_soal_jawaban')
                ->where('id',$jawab['id'])
                ->update([
                  'jawaban' => $jawab['jawaban'],
                  'kunci' => $jawab['kunci'] == 1 ? 1 : 0,
                ]);
            } else {
              DB::table('bank_soal_jawaban')
                ->insert([
                  'id_bank_soal' => $request->id,
                  'jawaban' => $jawab['jawaban'],
                  'kunci' => $jawab['kunci'] == 1 ? 1 : 0,
                ]);
            }            
          }
        }

        return response()->json([
          'status' => 'success',
          'message' => 'Berhasil',
        ], 200);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan'
        ], 401);
      }
    }

    public function hapus($id){
      $cek  = DB::table('bank_soal')
                ->where('id',$id)
                ->first();

      if($cek){
        DB::table('bank_soal')
          ->where('id',$id)
          ->delete();

        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }

    public function hapus_jawaban($id){
      $cek  = DB::table('bank_soal_jawaban')
                ->where('id',$id)
                ->first();

      if($cek){
        DB::table('bank_soal_jawaban')
          ->where('id',$id)
          ->delete();

        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      } else {
        return response()->json([
          'status' => 'error',
          'message' => 'Data tidak ditemukan',
        ], 401);
      }
    }
}
