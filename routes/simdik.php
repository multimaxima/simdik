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

Route::controller(\App\Http\Controllers\Api\Simdik\LoginController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('logout', 'logout');
    Route::get('getSekolah', 'getSekolah');

    Route::get('status', 'status');
    Route::get('refresh', 'refresh');

    Route::get('profil', 'profil');
    Route::get('profil-edit', 'profil_edit');
    Route::put('profil', 'profil_simpan');
    Route::post('profil-password', 'profil_password');
});

Route::controller(\App\Http\Controllers\Api\Simdik\HomeController::class)->group(function () {
    Route::get('propinsi', 'propinsi');
    Route::get('kota', 'kota');
    Route::get('kecamatan', 'kecamatan');
    Route::get('desa', 'desa');
    Route::get('admin', 'admin');
});

Route::controller(\App\Http\Controllers\Api\Simdik\AgamaController::class)->group(function () {
    Route::get('agama', 'index');
    Route::post('agama', 'baru');
    Route::put('agama', 'edit');
    Route::delete('agama/{id}', 'hapus');
    Route::get('agama/{id}', 'detil');
});

Route::controller(\App\Http\Controllers\Api\Simdik\BahasaController::class)->group(function () {
    Route::get('bahasa', 'index');
    Route::post('bahasa', 'baru');
    Route::put('bahasa', 'edit');
    Route::delete('bahasa/{id}', 'hapus');
    Route::get('bahasa/{id}', 'detil');
});

Route::controller(\App\Http\Controllers\Api\Simdik\PekerjaanController::class)->group(function () {
    Route::get('pekerjaan', 'index');
    Route::post('pekerjaan', 'baru');
    Route::put('pekerjaan', 'edit');
    Route::delete('pekerjaan/{id}', 'hapus');
    Route::get('pekerjaan/{id}', 'detil');
});

Route::controller(\App\Http\Controllers\Api\Simdik\PendidikanController::class)->group(function () {
    Route::get('pendidikan', 'index');
    Route::post('pendidikan', 'baru');
    Route::put('pendidikan', 'edit');
    Route::delete('pendidikan/{id}', 'hapus');
    Route::get('pendidikan/{id}', 'detil');
});

Route::controller(\App\Http\Controllers\Api\Simdik\GuruController::class)->group(function () {
    Route::get('guru', 'index');
    Route::post('guru', 'baru');
    Route::put('guru', 'edit');
    Route::delete('guru/{id}', 'hapus');
    Route::get('guru-detil', 'detil');
    Route::get('guru-reset', 'reset');
    Route::get('guru-validasi', 'validasi');
    Route::get('guru-detil-perpus', 'detil_perpus');
});

Route::controller(\App\Http\Controllers\Api\Simdik\SekolahController::class)->group(function () {
    Route::get('sekolah', 'index');
    Route::put('sekolah', 'edit');
});

Route::controller(\App\Http\Controllers\Api\Simdik\SiswaController::class)->group(function () {
    Route::get('siswa', 'index');
    Route::get('siswa-list', 'list');
    Route::post('siswa', 'baru');
    Route::put('siswa', 'edit');
    Route::delete('siswa/{id}', 'hapus');
    Route::get('siswa/{id}', 'detil');
    Route::get('siswa-reset', 'reset');
    Route::get('siswa-validasi', 'validasi');
    Route::post('siswa-import', 'import');
    Route::get('siswa-detil', 'detil_perpus');
});

Route::controller(\App\Http\Controllers\Api\Simdik\WaliController::class)->group(function () {
    Route::get('wali', 'index');
});

Route::controller(\App\Http\Controllers\Api\Simdik\KelasController::class)->group(function () {
    Route::get('kelas', 'index');
    Route::post('kelas', 'baru');
    Route::put('kelas', 'edit');
    Route::delete('kelas/{id}', 'hapus');
    Route::get('kelas-detil', 'detil');
    Route::get('kelas-baru', 'tambah');
});

Route::controller(\App\Http\Controllers\Api\Simdik\MapelController::class)->group(function () {
    Route::get('mapel', 'index');
    Route::post('mapel', 'baru');
    Route::put('mapel', 'edit');
    Route::delete('mapel/{id}', 'hapus');
    Route::get('mapel/{id}', 'detil');
});

Route::controller(\App\Http\Controllers\Api\Simdik\VarMapelController::class)->group(function () {
    Route::get('mata-pelajaran', 'index');
    Route::post('mata-pelajaran', 'baru');
    Route::put('mata-pelajaran', 'edit');
    Route::delete('mata-pelajaran/{id}', 'hapus');
    Route::get('mata-pelajaran/{id}', 'detil');
});

Route::controller(\App\Http\Controllers\Api\Simdik\JurusanController::class)->group(function () {
    Route::get('jurusan', 'index');
    Route::post('jurusan', 'baru');
    Route::put('jurusan', 'edit');
    Route::delete('jurusan/{id}', 'hapus');
    Route::get('jurusan/{id}', 'detil');
});

Route::controller(\App\Http\Controllers\Api\Simdik\JenisUjianController::class)->group(function () {
    Route::get('jenis-ujian', 'index');
    Route::post('jenis-ujian', 'baru');
    Route::put('jenis-ujian', 'edit');
    Route::delete('jenis-ujian/{id}', 'hapus');
    Route::get('jenis-ujian/{id}', 'detil');
});

Route::controller(\App\Http\Controllers\Api\Simdik\SarprasController::class)->group(function () {
    Route::get('sarpras', 'index');
    Route::put('sarpras', 'edit');
});

Route::controller(\App\Http\Controllers\Api\Simdik\SanitasiController::class)->group(function () {
    Route::get('sanitasi', 'index');
    Route::put('sanitasi', 'edit');
});

Route::controller(\App\Http\Controllers\Api\Simdik\KatalogController::class)->group(function () {
    Route::get('katalog', 'index');
    Route::post('katalog', 'baru');
    Route::put('katalog', 'edit');
    Route::delete('katalog/{id}', 'hapus');
    Route::get('katalog-detil', 'detil');
    Route::get('katalog-generate', 'generate');
    Route::get('katalog-item', 'katalog_item');
});

Route::controller(\App\Http\Controllers\Api\Simdik\HariController::class)->group(function () {
  Route::get('hari', 'index');
});  

Route::controller(\App\Http\Controllers\Api\Simdik\BelSuaraController::class)->group(function () {
  Route::get('bel-suara', 'index');
  Route::get('bel-suara/{id}', 'detil');
});  

Route::controller(\App\Http\Controllers\Api\Simdik\BelController::class)->group(function () {
  Route::get('bel-sekolah', 'index');
  Route::get('bel-sekolah-refresh', 'refresh');
  Route::post('bel-sekolah', 'baru');
  Route::put('bel-sekolah', 'edit');
  Route::delete('bel-sekolah/{id}', 'hapus');
  Route::get('bel-sekolah-detil', 'detil');
});

Route::controller(\App\Http\Controllers\Api\Simdik\BelEventController::class)->group(function () {
  Route::get('bel-event', 'index');
  Route::post('bel-event', 'baru');
  Route::put('bel-event', 'edit');
  Route::delete('bel-event/{id}', 'hapus');
  Route::get('bel-event/{id}', 'detil');
});

Route::controller(\App\Http\Controllers\Api\Simdik\KasPerpusController::class)->group(function () {
  Route::get('kas-perpustakaan', 'index');
});

Route::controller(\App\Http\Controllers\Api\Simdik\PembayaranController::class)->group(function () {
  Route::get('pengaturan-pembayaran', 'index');
  Route::post('pengaturan-pembayaran', 'baru');
  Route::put('pengaturan-pembayaran', 'edit');
  Route::delete('pengaturan-pembayaran/{id}', 'hapus');
  Route::get('pengaturan-pembayaran/{id}', 'detil');
});

Route::controller(\App\Http\Controllers\Api\Simdik\PerpusController::class)->group(function () {
  Route::get('data-katalog-dipinjam', 'data_katalog_dipinjam');
  Route::get('katalog-dipinjam', 'katalog_dipinjam');
  Route::post('katalog-pinjam', 'katalog_pinjam');
  Route::get('katalog-kembali', 'katalog_kembali');
  Route::post('katalog-kembali-nis', 'katalog_kembali_nis');
  Route::post('katalog-kembali-nip', 'katalog_kembali_nip');
  Route::post('katalog-kembali-barcode', 'katalog_kembali_barcode');
});

Route::controller(\App\Http\Controllers\Api\Simdik\KurikulumController::class)->group(function () {
  Route::get('kurikulum', 'index');
});

Route::controller(\App\Http\Controllers\Api\Simdik\BankSoalController::class)->group(function () {
  Route::get('bank-soal-buka', 'buka');
  Route::get('bank-soal', 'index');
  Route::post('bank-soal', 'baru');
  Route::put('bank-soal', 'edit');
  Route::delete('bank-soal/{id}', 'hapus');
  Route::delete('bank-soal-jawaban/{id}', 'hapus_jawaban');
});

Route::controller(\App\Http\Controllers\Api\Simdik\UjianController::class)->group(function () {
  Route::get('materi-ulangan-tugas', 'ulangan_tugas');
});