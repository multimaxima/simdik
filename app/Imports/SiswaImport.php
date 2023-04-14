<?php

namespace App\Imports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Auth;
use Hash;
use DB;

HeadingRowFormatter::default('none');

class SiswaImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    /**
    * @param Collection $collection
    */
    public function model(array $row){
      DB::table('walis')
        ->insert([
          'id_sekolah' => Auth::guard('simdik')->user()->id_sekolah, 
          'nisn' => $row['NISN'],
          'username' => $row['NIS'].str_pad(Auth::guard('simdik')->user()->id_sekolah,3,'0',STR_PAD_LEFT),
          'password' => Hash::make('123456'),
        ]);

      $wali   = DB::table('walis')
                  ->where('id_sekolah',Auth::guard('simdik')->user()->id_sekolah)
                  ->where('nisn',$row['NISN'])
                  ->orderby('id','desc')
                  ->first();

      if($row['L/P'] == 'L'){
        $kelamin = 1;
      } else {
        $kelamin = 2;
      }

      DB::table('siswas')
        ->insert([
          'id_sekolah' => Auth::guard('simdik')->user()->id_sekolah, 
          'nis' => $row['NIS'],
          'nisn' => $row['NISN'],
          'nama' => $row['NAMA'],
          'kelamin' => $kelamin,
          'id_wali' => $wali->id,
          'username' => $row['NIS'].str_pad(Auth::guard('simdik')->user()->id_sekolah,3,'0',STR_PAD_LEFT),
          'password' => Hash::make('123456'),
        ]);
    }

    public function batchSize(): int {
      return 1000;
    }

    public function chunkSize(): int {
      return 1000;
    }
}
