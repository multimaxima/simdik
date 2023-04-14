<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Siswa extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
      'nama',
      'nis',
      'nisn',
      'username',
      'email',
      'password',
    ];

    protected $hidden = [
      'password',
      'remember_token',
      'email_verified_at',
      'petugas_create',
      'petugas_update',
      'created_at',
      'updated_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier(){
      return $this->getKey();
    }

    public function getJWTCustomClaims(){
      return [];
    }
}
