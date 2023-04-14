<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    // return $request->user();
// });

// Route::controller(\App\Http\Controllers\Api\Sim\HomeController::class)->group(function () {
    // Route::get('reset', 'reset');
// });    

Route::controller(\App\Http\Controllers\Api\Sim\LoginController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('logout', 'logout');

    Route::get('status', 'status');
    Route::get('refresh', 'refresh');

    Route::get('profil', 'profil');
    Route::put('profil', 'profil_simpan');
    Route::post('profil-password', 'profil_password');
});

Route::controller(\App\Http\Controllers\Api\Sim\HomeController::class)->group(function () {
    Route::get('propinsi', 'propinsi');
    Route::get('kota', 'kota');
    Route::get('kecamatan', 'kecamatan');
    Route::get('desa', 'desa');
});

Route::controller(\App\Http\Controllers\Api\Sim\PetugasController::class)->group(function () {
    Route::get('petugas', 'index');
    Route::post('petugas', 'baru');
    Route::put('petugas', 'edit');
    Route::delete('petugas/{id}', 'hapus');
    Route::get('petugas-detil/{id}', 'detil');
    Route::get('petugas-reset/{id}', 'reset');
    Route::get('petugas-validasi', 'validasi');
});

Route::controller(\App\Http\Controllers\Api\Sim\SekolahController::class)->group(function () {
    Route::get('sekolah', 'index');
    Route::post('sekolah', 'baru');
    Route::put('sekolah', 'edit');
    Route::delete('sekolah/{id}', 'hapus');
    Route::get('sekolah/{id}', 'detil');
    Route::get('sekolah-edit/{id}', 'edit_show');
    Route::get('sekolah-blokir/{id}', 'blokir');
    Route::get('sekolah-buka/{id}', 'buka');
});

Route::controller(\App\Http\Controllers\Api\Sim\GuruController::class)->group(function () {
    Route::get('daftar-guru/{id}', 'index');
    Route::post('guru', 'baru');
    Route::put('guru', 'edit');
    Route::delete('guru/{id}', 'hapus');
    Route::get('guru/{id}', 'detil');
    Route::get('guru-detil/{id}', 'detil');
    Route::get('guru-reset/{id}', 'reset');
    Route::get('guru-validasi', 'validasi');
});

Route::controller(\App\Http\Controllers\Api\Sim\SiswaController::class)->group(function () {
    Route::get('siswa', 'index');
    Route::post('siswa', 'baru');
    Route::put('siswa', 'edit');
    Route::delete('siswa/{id}', 'hapus');
    Route::get('siswa/{id}', 'detil');
    Route::get('siswa-reset/{id}', 'reset');
    Route::get('siswa-validasi', 'validasi');
});

Route::controller(\App\Http\Controllers\Api\Sim\AgamaController::class)->group(function () {
    Route::get('agama', 'index');
    Route::post('agama', 'baru');
    Route::put('agama', 'edit');
    Route::delete('agama/{id}', 'hapus');
    Route::get('agama/{id}', 'detil');
});

Route::controller(\App\Http\Controllers\Api\Sim\BahasaController::class)->group(function () {
    Route::get('bahasa', 'index');
    Route::post('bahasa', 'baru');
    Route::put('bahasa', 'edit');
    Route::delete('bahasa/{id}', 'hapus');
    Route::get('bahasa/{id}', 'detil');
});

Route::controller(\App\Http\Controllers\Api\Sim\BankController::class)->group(function () {
    Route::get('bank', 'index');
    Route::post('bank', 'baru');
    Route::put('bank', 'edit');
    Route::delete('bank/{id}', 'hapus');
    Route::get('bank/{id}', 'detil');
});

Route::controller(\App\Http\Controllers\Api\Sim\BentukPendidikanController::class)->group(function () {
    Route::get('bentuk-pendidikan', 'index');
    Route::post('bentuk-pendidikan', 'baru');
    Route::put('bentuk-pendidikan', 'edit');
    Route::delete('bentuk-pendidikan/{id}', 'hapus');
    Route::get('bentuk-pendidikan/{id}', 'detil');
});

Route::controller(\App\Http\Controllers\Api\Sim\JenisUjianController::class)->group(function () {
    Route::get('jenis-ujian', 'index');
    Route::post('jenis-ujian', 'baru');
    Route::put('jenis-ujian', 'edit');
    Route::delete('jenis-ujian/{id}', 'hapus');
    Route::get('jenis-ujian/{id}', 'detil');
});

Route::controller(\App\Http\Controllers\Api\Sim\MapelController::class)->group(function () {
    Route::get('mapel', 'index');
    Route::post('mapel', 'baru');
    Route::put('mapel', 'edit');
    Route::delete('mapel/{id}', 'hapus');
    Route::get('mapel/{id}', 'detil');
});

Route::controller(\App\Http\Controllers\Api\Sim\PekerjaanController::class)->group(function () {
    Route::get('pekerjaan', 'index');
    Route::post('pekerjaan', 'baru');
    Route::put('pekerjaan', 'edit');
    Route::delete('pekerjaan/{id}', 'hapus');
    Route::get('pekerjaan/{id}', 'detil');
});

Route::controller(\App\Http\Controllers\Api\Sim\PendidikanController::class)->group(function () {
    Route::get('pendidikan', 'index');
    Route::post('pendidikan', 'baru');
    Route::put('pendidikan', 'edit');
    Route::delete('pendidikan/{id}', 'hapus');
    Route::get('pendidikan/{id}', 'detil');
});

Route::controller(\App\Http\Controllers\Api\Sim\IsoController::class)->group(function () {
    Route::get('iso', 'index');
    Route::post('iso', 'baru');
    Route::put('iso', 'edit');
    Route::delete('iso/{id}', 'hapus');
    Route::get('iso/{id}', 'detil');
});

Route::controller(\App\Http\Controllers\Api\Sim\KepemilikanController::class)->group(function () {
    Route::get('status-kepemilikan', 'index');
    Route::post('status-kepemilikan', 'baru');
    Route::put('status-kepemilikan', 'edit');
    Route::delete('status-kepemilikan/{id}', 'hapus');
    Route::get('status-kepemilikan/{id}', 'detil');
});

Route::controller(\App\Http\Controllers\Api\Sim\KurikulumController::class)->group(function () {
    Route::get('kurikulum', 'index');
    Route::post('kurikulum', 'baru');
    Route::put('kurikulum', 'edit');
    Route::delete('kurikulum/{id}', 'hapus');
    Route::get('kurikulum/{id}', 'detil');
});

Route::controller(\App\Http\Controllers\Api\Sim\JurusanController::class)->group(function () {
    Route::get('jurusan', 'index');
    Route::post('jurusan', 'baru');
    Route::put('jurusan', 'edit');
    Route::delete('jurusan/{id}', 'hapus');
    Route::get('jurusan/{id}', 'detil');
});

Route::controller(\App\Http\Controllers\Api\Sim\KelasController::class)->group(function () {
    Route::get('kelas', 'index');
});

Route::controller(\App\Http\Controllers\Api\Sim\KelasSekolahController::class)->group(function () {
    Route::get('kelas-sekolah', 'index');
    Route::post('kelas-sekolah', 'baru');
    Route::put('kelas-sekolah', 'edit');
    Route::delete('kelas-sekolah/{id}', 'hapus');
    Route::get('kelas-sekolah/{id}', 'detil');
});

Route::controller(\App\Http\Controllers\Api\Sim\KatalogJenisController::class)->group(function () {
  Route::get('katalog-jenis', 'index');
  Route::post('katalog-jenis', 'baru');
  Route::put('katalog-jenis', 'edit');
  Route::delete('katalog-jenis/{id}', 'hapus');
});

Route::controller(\App\Http\Controllers\Api\Sim\KatalogKategoriController::class)->group(function () {
  Route::get('katalog-kategori', 'index');
  Route::post('katalog-kategori', 'baru');
  Route::put('katalog-kategori', 'edit');
  Route::delete('katalog-kategori/{id}', 'hapus');
});

Route::controller(\App\Http\Controllers\Api\Sim\KatalogController::class)->group(function () {
  Route::get('katalog', 'index');
  Route::post('katalog', 'baru');
  Route::put('katalog', 'edit');
  Route::delete('katalog/{id}', 'hapus');
});

Route::controller(\App\Http\Controllers\Api\Sim\BelController::class)->group(function () {
    Route::get('suara-bel', 'index');
    Route::post('suara-bel', 'baru');
    Route::put('suara-bel', 'edit');
    Route::delete('suara-bel', 'hapus');
});