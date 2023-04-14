<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bankSoal extends Model
{
    use HasFactory;

    protected $table = 'bank_soal';

    protected $fillable = [
      'id',
      'strata', 
      'id_kelas', 
      'id_mapel', 
      'jenis', 
      'soal', 
      'kunci', 
      'sim_create', 
      'sim_update', 
      'simdik_create', 
      'simdik_update', 
    ];

    protected $hidden = [
      'created_at',
      'updated_at',
    ];

    public function jawaban()
    {
        return $this->hasMany(bankSoalJawaban::class,'id_bank_soal','id');
    }    
}
