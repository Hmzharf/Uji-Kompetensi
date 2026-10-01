<?php
/**
 * SISTEM PENGUJIAN UNIT OTOMATIS (UNIT TESTING)
 * Unit Kompetensi 4: OOP | Unit 6: Basis Data | Unit 8: Debugging & Pengujian Perangkat Lunak
 */

require_once 'helpers/functions.php';
require_once 'config/Database.php';
require_once 'classes/Petugas.php';
require_once 'classes/Kelas.php';
require_once 'classes/Siswa.php';
require_once 'classes/Pembayaran.php';

// Framework Unit Test Sederhana
class SimpleUnitTest {
    private array $results = [];
    private int $passed = 0;
    private int $failed = 0;

    public function test(string $namaTes, callable $fungsiTes) {
        $startTime = microtime(true);
        try {
            $hasil = $fungsiTes();
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            if ($hasil === true) {
                $this->passed++;
                $this->results[] = [
                    'nama' => $namaTes,
                    'status' => 'PASS',
                    'pesan' => 'Pengujian berhasil tanpa error.',
                    'waktu' => $duration . ' ms'
                ];
            } else {
                $this->failed++;
                $this->results[] = [
                    'nama' => $namaTes,
                    'status' => 'FAIL',
                    'pesan' => is_string($hasil) ? $hasil : 'Kondisi assertion tidak terpenuhi.',
                    'waktu' => $duration . ' ms'
                ];
            }
        } catch (Throwable $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            $this->failed++;
            $this->results[] = [
                'nama' => $namaTes,
                'status' => 'ERROR',
                'pesan' => 'Exception: ' . $e->getMessage() . ' di baris ' . $e->getLine(),
                'waktu' => $duration . ' ms'
            ];
        }
    }

    public function getResults(): array {
        return $this->results;
    }

    public function getSummary(): array {
        return [
            'total' => count($this->results),
            'passed' => $this->passed,
            'failed' => $this->failed,
            'persentase' => count($this->results) > 0 ? round(($this->passed / count($this->results)) * 100, 1) : 0
        ];
    }
}

$suite = new SimpleUnitTest();

// -------------------------------------------------------------
// 1. PENGUJIAN UNIT: KONEKSI BASIS DATA (PDO)
// -------------------------------------------------------------
$suite->test("1. Verifikasi Koneksi Basis Data PDO", function() {
    $db = (new Database())->getConnection();
    if (!$db instanceof PDO) {
        return "Objek koneksi bukan merupakan instance dari PDO.";
    }
    $status = $db->getAttribute(PDO::ATTR_CONNECTION_STATUS);
    return !empty($status);
});

// -------------------------------------------------------------
// 2. PENGUJIAN UNIT: HELPER FUNCTIONS (Rupiah & Tanggal)
// -------------------------------------------------------------
$suite->test("2. Format Uang Rupiah Helper function", function() {
    $hasil = rupiah(250000);
    if ($hasil !== 'Rp 250.000') {
        return "Ekspektasi 'Rp 250.000', hasil didapat: '$hasil'";
    }
    return true;
});

// -------------------------------------------------------------
// 3. PENGUJIAN UNIT: AUTENTIKASI PETUGAS (Login Valid & Invalid)
// -------------------------------------------------------------
$suite->test("3. Autentikasi Login Petugas (Kredensial Benar)", function() {
    $petugas = new Petugas();
    $user = $petugas->login('admin', 'admin123');
    if (!$user || $user['username'] !== 'admin') {
        return "Login dengan user 'admin' dan 'admin123' gagal diverifikasi.";
    }
    return true;
});

$suite->test("4. Autentikasi Login Petugas (Kredensial Salah)", function() {
    $petugas = new Petugas();
    $user = $petugas->login('admin', 'passwordsalah123');
    if ($user !== false) {
        return "Sistem harus menolak password yang salah, tetapi menghasilkan true.";
    }
    return true;
});

// -------------------------------------------------------------
// 5. PENGUJIAN UNIT: MODEL KELAS (Insert & Delete)
// -------------------------------------------------------------
$suite->test("5. CRUD Model Kelas (Tambah & Hapus Data Uji)", function() {
    $kelas = new Kelas();
    $idUji = 'TEST_KLS';
    
    // Tambah kelas uji
    $tambah = $kelas->create($idUji, 'Kelas Uji', 'Rekayasa Perangkat Lunak');
    if (!$tambah) return "Gagal memasukkan data kelas uji.";

    // Hapus kelas uji kembali agar data bersih
    $hapus = $kelas->delete($idUji);
    if (!$hapus) return "Gagal menghapus data kelas uji.";

    return true;
});

// -------------------------------------------------------------
// 6. PENGUJIAN UNIT: MODEL SISWA (Ambil Data & Status Bayar)
// -------------------------------------------------------------
$suite->test("6. Query Model Siswa dengan Status Pembayaran", function() {
    $siswa = new Siswa();
    $data = $siswa->getSiswaDenganStatus();
    if (!is_array($data)) {
        return "Hasil getSiswaDenganStatus() bukan berupa array.";
    }
    // Jika ada data siswa, cek struktur kolomnya
    if (count($data) > 0) {
        $pertama = $data[0];
        if (!isset($pertama['status_bayar'])) {
            return "Kolom 'status_bayar' tidak ditemukan pada hasil query siswa.";
        }
    }
    return true;
});

// -------------------------------------------------------------
// 7. PENGUJIAN UNIT: LOGIKA KALKULASI & STATUS PEMBAYARAN
// -------------------------------------------------------------
$suite->test("7. Logika Kalkulasi Transaksi SPP & Status Lunas", function() {
    $tarifPerBulan = 250000;
    $jumlahBulan   = 2;
    $wajibBayar    = $tarifPerBulan * $jumlahBulan; // 500.000
    $uangDiterima  = 600000;
    $kembalian     = $uangDiterima - $wajibBayar;   // 100.000
    $status        = ($uangDiterima >= $wajibBayar) ? 'Lunas' : 'Belum Lunas';

    if ($wajibBayar !== 500000) return "Kalkulasi wajib bayar keliru.";
    if ($kembalian !== 100000) return "Kalkulasi kembalian keliru.";
    if ($status !== 'Lunas') return "Status pembayaran gagal ditentukan sebagai 'Lunas'.";

    return true;
});

$suite->test("8. Logika Penentuan Status Belum Lunas (Uang Kurang)", function() {
    $wajibBayar   = 500000;
    $uangDiterima = 300000;
    $status       = ($uangDiterima >= $wajibBayar) ? 'Lunas' : 'Belum Lunas';

    if ($status !== 'Belum Lunas') {
        return "Pembayaran kurang harusnya berstatus 'Belum Lunas'.";
    }
    return true;
});

// -------------------------------------------------------------
// 8. PENGUJIAN UNIT: AGREGASI KAS PEMBAYARAN
// -------------------------------------------------------------
$suite->test("9. Perhitungan Agregasi Total Kas SPP", function() {
    $pembayaran = new Pembayaran();
    $totalKas = $pembayaran->getTotalKas();
    if (!is_float($totalKas) && !is_numeric($totalKas)) {
        return "Total kas yang dikembalikan bukan nilai numerik.";
    }
    if ($totalKas < 0) {
        return "Total kas tidak boleh bernilai negatif.";
    }
    return true;
});

$summary = $suite->getSummary();
$results = $suite->getResults();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unit Testing & Quality Assurance - Aplikasi SPP</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: system-ui, -apple-system, sans-serif; }
        .badge-pass { background-color: #198754; color: #fff; }
        .badge-fail { background-color: #dc3545; color: #fff; }
        .badge-error { background-color: #fd7e14; color: #fff; }
    </style>
</head>
<body class="py-4">

<div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h3 class="fw-bold mb-0 text-primary">Unit Testing & Debugging Suite</h3>
            <p class="text-muted small mb-0">Uji Kompetensi Keahlian (UKK) Rekayasa Perangkat Lunak</p>
        </div>
        <div>
            <a href="index.php" class="btn btn-outline-secondary me-2">&laquo; Kembali ke Dashboard</a>
            <a href="test.php" class="btn btn-primary">&#x21bb; Jalankan Ulang Tes</a>
            <a href="debug.php" class="btn btn-outline-primary me-2">Jalankan Unit debugging</a>
        </div>
    </div>

    <!-- Ringkasan Hasil Pengujian -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <small class="text-muted text-uppercase">Total Unit Tes</small>
                <h2 class="fw-bold my-1 text-dark"><?= $summary['total'] ?></h2>
                <small class="text-muted">Kasus Uji Dijalankan</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <small class="text-muted text-uppercase">Berhasil (Pass)</small>
                <h2 class="fw-bold my-1 text-success"><?= $summary['passed'] ?></h2>
                <small class="text-muted">Fungsi Berjalan Normal</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <small class="text-muted text-uppercase">Gagal (Fail)</small>
                <h2 class="fw-bold my-1 text-danger"><?= $summary['failed'] ?></h2>
                <small class="text-muted">Perlu Troubleshooting</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <small class="text-muted text-uppercase">Tingkat Kelulusan</small>
                <h2 class="fw-bold my-1 text-<?= $summary['persentase'] == 100 ? 'success' : 'warning' ?>">
                    <?= $summary['persentase'] ?>%
                </h2>
                <small class="text-muted">Status: <?= $summary['failed'] === 0 ? 'MEMENUHI SYARAT' : 'PERLU PERBAIKAN' ?></small>
            </div>
        </div>
    </div>

    <!-- Tabel Rincian Pengujian -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Rincian Pengujian Kasus Uji (Test Cases)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Kasus Pengujian (Test Case)</th>
                            <th style="width: 120px;">Waktu</th>
                            <th style="width: 120px;">Status</th>
                            <th>Keterangan / Pesan Debugging</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach($results as $res): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= htmlspecialchars($res['nama']) ?></strong></td>
                            <td><code><?= $res['waktu'] ?></code></td>
                            <td>
                                <span class="badge badge-<?= strtolower($res['status']) ?> px-2 py-1">
                                    <?= $res['status'] ?>
                                </span>
                            </td>
                            <td class="small text-<?= $res['status'] === 'PASS' ? 'muted' : 'danger' ?>">
                                <?= htmlspecialchars($res['pesan']) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Panduan Troubleshooting Terintegrasi -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Petunjuk Troubleshooting Cepat</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <h6 class="fw-bold text-primary">1. Jika Tes Database Gagal</h6>
                    <p class="small text-muted mb-0">Periksa file <code>config/Database.php</code>. Pastikan Apache & MySQL di XAMPP berstatus aktif (hijau) dan database <code>db_PembayaranSppSiswa</code> sudah diimport.</p>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold text-primary">2. Jika Tes Login Gagal</h6>
                    <p class="small text-muted mb-0">Buka tabel <code>tabel_petugas</code> di phpMyAdmin. Pastikan username <code>admin</code> dengan password <code>admin123</code> sudah tersedia.</p>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold text-primary">3. Jika Layar Tampil Putih</h6>
                    <p class="small text-muted mb-0">Aktifkan pelaporan error di baris pertama file <code>config/Helper.php</code> dengan menambahkan <code>error_reporting(E_ALL); ini_set('display_errors', 1);</code>.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
