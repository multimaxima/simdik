<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bankSoalJawaban extends Model
{
    use HasFactory;

    protected $table = 'bank_soal_jawaban';

    protected $fillable = [
      'id',
      'id_bank_soal', 
      'jawaban', 
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
}
