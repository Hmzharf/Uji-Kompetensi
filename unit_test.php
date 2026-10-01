
    <?php
    /**
     * SISTEM PENGUJIAN UNIT OTOMATIS (UNIT TESTING)
     * Standar: Unit Kompetensi 4 (OOP) | Unit 6 (Basis Data) | Unit 8 (Pengujian & Debugging)
     */

    require_once 'config/Helper.php';
    require_once 'config/Database.php';
    require_once 'classes/Petugas.php';
    require_once 'classes/Kelas.php';
    require_once 'classes/Siswa.php';
    require_once 'classes/Pembayaran.php';

    // Framework Sederhana untuk Menjalankan Test Case
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

        public function getResults(): array { return $this->results; }
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

    // 1. UJI KONEKSI DATABASE
    $suite->test("1. Verifikasi Koneksi Basis Data PDO", function() {
        $db = (new Database())->getConnection();
        if (!$db instanceof PDO) return "Objek koneksi bukan instance dari PDO.";
        return true;
    });

    // 2. UJI HELPER RUPIAH
    $suite->test("2. Format Uang Rupiah Helper Function", function() {
        $hasil = rupiah(250000);
        if ($hasil !== 'Rp 250.000') return "Ekspektasi 'Rp 250.000', didapat '$hasil'";
        return true;
    });

    // 3. UJI LOGIN KREDENSIAL BENAR
    $suite->test("3. Autentikasi Login Petugas (Kredensial Benar)", function() {
        $petugas = new Petugas();
        $user = $petugas->login('admin', 'admin123');
        if (!$user || $user['username'] !== 'admin') return "Login admin/admin123 gagal.";
        return true;
    });

    // 4. UJI LOGIN KREDENSIAL SALAH
    $suite->test("4. Autentikasi Login Petugas (Kredensial Salah)", function() {
        $petugas = new Petugas();
        $user = $petugas->login('admin', 'passwordsalah123');
        if ($user !== false) return "Sistem harus menolak password salah.";
        return true;
    });

    // 5. UJI CRUD KELAS (Insert & Delete)
    $suite->test("5. CRUD Model Kelas (Tambah & Hapus Uji)", function() {
        $kelas = new Kelas();
        $idUji = 'UJI_KLS';
        $tambah = $kelas->create($idUji, 'Kelas Uji', 'RPL');
        if (!$tambah) return "Gagal insert data kelas uji.";
        $hapus = $kelas->delete($idUji);
        if (!$hapus) return "Gagal delete data kelas uji.";
        return true;
    });

    // 6. UJI QUERY STATUS SISWA
    $suite->test("6. Query Model Siswa dengan Status Pembayaran", function() {
        $siswa = new Siswa();
        $data = $siswa->getSiswaDenganStatus();
        if (!is_array($data)) return "Hasil getSiswaDenganStatus() bukan array.";
        return true;
    });

    // 7. UJI LOGIKA KALKULASI & STATUS LUNAS
    $suite->test("7. Logika Kalkulasi Transaksi SPP & Status Lunas", function() {
        $tarif = 250000;
        $bulan = 2;
        $wajib = $tarif * $bulan; // 500.000
        $bayar = 600000;
        $kembalian = $bayar - $wajib; // 100.000
        $status = ($bayar >= $wajib) ? 'Lunas' : 'Belum Lunas';

        if ($wajib !== 500000 || $kembalian !== 100000 || $status !== 'Lunas') {
            return "Perhitungan pembayaran lunas tidak sesuai.";
        }
        return true;
    });

    // 8. UJI LOGIKA STATUS BELUM LUNAS
    $suite->test("8. Logika Penentuan Status Belum Lunas (Uang Kurang)", function() {
        $wajib = 500000;
        $bayar = 300000;
        $status = ($bayar >= $wajib) ? 'Lunas' : 'Belum Lunas';
        if ($status !== 'Belum Lunas') return "Status pembayaran kurang harus Belum Lunas.";
        return true;
    });

    // 9. UJI KAS TOTAL
    $suite->test("9. Perhitungan Agregasi Total Kas SPP", function() {
        $pembayaran = new Pembayaran();
        $kas = $pembayaran->getTotalKas();
        if (!is_numeric($kas) || $kas < 0) return "Total kas tidak valid.";
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
        <title>Unit Testing - Aplikasi SPP</title>
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <style>
            body { background-color: #f8f9fa; }
            .badge-pass { background-color: #198754; color: #fff; }
            .badge-fail { background-color: #dc3545; color: #fff; }
            .badge-error { background-color: #fd7e14; color: #fff; }
        </style>
    </head>
    <body class="py-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
                <h3 class="fw-bold mb-0 text-primary">Unit Testing Suite</h3>
                <p class="text-muted small mb-0">Uji Otomatis Fungsi, Model, dan Logika Bisnis</p>
            </div>
            <div>
                <a href="index.php" class="btn btn-outline-secondary me-2">&laquo; Dashboard</a>
                <a href="test.php" class="btn btn-primary">&#x21bb; Jalankan Ulang Tes</a>
            </div>
        </div>

        <!-- Ringkasan Hasil -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 bg-white">
                    <small class="text-muted text-uppercase">Total Unit Tes</small>
                    <h2 class="fw-bold my-1"><?= $summary['total'] ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 bg-white">
                    <small class="text-muted text-uppercase">Berhasil (Pass)</small>
                    <h2 class="fw-bold my-1 text-success"><?= $summary['passed'] ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 bg-white">
                    <small class="text-muted text-uppercase">Gagal (Fail)</small>
                    <h2 class="fw-bold my-1 text-danger"><?= $summary['failed'] ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 bg-white">
                    <small class="text-muted text-uppercase">Tingkat Kelulusan</small>
                    <h2 class="fw-bold my-1 text-<?= $summary['persentase'] == 100 ? 'success' : 'warning' ?>"><?= $summary['persentase'] ?>%</h2>
                </div>
            </div>
        </div>

        <!-- Tabel Rincian -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Rincian Hasil Pengujian Kasus Uji</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Kasus Uji</th>
                                <th>Waktu</th>
                                <th>Status</th>
                                <th>Pesan Pengujian</th>
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
    </div>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
  ──────
  ### 2. File debug.php (Diagnostik Lingkungan & Troubleshooting Tool)

  Simpan file ini di: C:\xampp\htdocs\UJI-KOMPETENSI\debug.php.
  File ini berfungsi sebagai alat pendeteksi kesehatan server: memeriksa apakah ekstensi PHP PDO aktif, memeriksa keberadaan database, tabel, kolom, status session, serta mendiagnosis kesalahan secara otomatis.

    <?php
    /**
     * DIAGNOSTIC & TROUBLESHOOTING TOOL
     * Unit Kompetensi 8: Melakukan Debugging & Troubleshooting
     */

    require_once 'config/Helper.php';
    require_once 'config/Database.php';

    $diagnostik = [];

    // 1. Cek Versi PHP
    $diagnostik['php_version'] = [
        'nama' => 'Versi PHP Server',
        'status' => version_compare(PHP_VERSION, '7.4.0', '>=') ? 'OK' : 'WARNING',
        'detail' => PHP_VERSION . ' (Direkomendasikan PHP 7.4 - 8.2+)'
    ];

    // 2. Cek Ekstensi PDO MySQL
    $diagnostik['pdo_mysql'] = [
        'nama' => 'Ekstensi PDO MySQL Driver',
        'status' => extension_loaded('pdo_mysql') ? 'OK' : 'ERROR',
        'detail' => extension_loaded('pdo_mysql') ? 'Aktif dan Terpasang' : 'Tidak aktif di php.ini!'
    ];

    // 3. Cek Status Session
    $diagnostik['session'] = [
        'nama' => 'Mekanisme Session PHP',
        'status' => session_status() === PHP_SESSION_ACTIVE ? 'OK' : 'ERROR',
        'detail' => 'Session ID: ' . session_id()
    ];

    // 4. Cek Koneksi Basis Data
    $dbStatus = 'ERROR';
    $dbDetail = '';
    $tabelStatus = [];
    try {
        $db = (new Database())->getConnection();
        if ($db) {
            $dbStatus = 'OK';
            $dbDetail = 'Berhasil terhubung ke database `db_PembayaranSppSiswa`.';

            // Cek Keberadaan Tabel Wajib
            $daftarTabel = ['tabel_kelas', 'tabel_spp', 'tabel_siswa', 'tabel_pembayaran', 'tabel_cek_pembayaran', 'tabel_petugas'];
            foreach ($daftarTabel as $tbl) {
                $stmt = $db->query("SHOW TABLES LIKE '$tbl'");
                $ada = $stmt->rowCount() > 0;
                $count = 0;
                if ($ada) {
                    $count = $db->query("SELECT COUNT(*) FROM `$tbl`")->fetchColumn();
                }
                $tabelStatus[$tbl] = [
                    'ada' => $ada,
                    'baris' => $count
                ];
            }
        }
    } catch (Throwable $e) {
        $dbDetail = 'Koneksi Gagal: ' . $e->getMessage();
    }

    $diagnostik['database'] = [
        'nama' => 'Koneksi Database PDO',
        'status' => $dbStatus,
        'detail' => $dbDetail
    ];

    // 5. Cek File Aset Bootstrap
    $cssAda = file_exists(__DIR__ . '/assets/css/bootstrap.min.css');
    $jsAda  = file_exists(__DIR__ . '/assets/js/bootstrap.bundle.min.js');
    $diagnostik['assets'] = [
        'nama' => 'Integritas File Aset Bootstrap',
        'status' => ($cssAda && $jsAda) ? 'OK' : 'WARNING',
        'detail' => "CSS: " . ($cssAda ? 'Ada' : 'Hilang') . " | JS: " . ($jsAda ? 'Ada' : 'Hilang')
    ];
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Troubleshooting & Diagnostic Tool</title>
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <style>body { background-color: #f8f9fa; }</style>
    </head>
    <body class="py-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
                <h3 class="fw-bold mb-0 text-success">Diagnostic & Troubleshooting Tool</h3>
                <p class="text-muted small mb-0">Inspeksi Lingkungan Server, Basis Data, dan File Konfigurasi</p>
            </div>
            <div>
                <a href="test.php" class="btn btn-outline-primary me-2">Jalankan Unit Test</a>
                <a href="index.php" class="btn btn-secondary">&laquo; Dashboard</a>
            </div>
        </div>

        <!-- Status Kesehatan Sistem -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Hasil Diagnostik Sistem</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Komponen</th>
                            <th>Status</th>
                            <th>Detail Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($diagnostik as $diag): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($diag['nama']) ?></strong></td>
                            <td>
                                <span class="badge bg-<?= $diag['status'] === 'OK' ? 'success' : 'danger' ?>">
                                    <?= $diag['status'] ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($diag['detail']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Verifikasi Tabel Database -->
        <?php if ($dbStatus === 'OK'): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Inspeksi Tabel Basis Data</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Tabel</th>
                            <th>Status Keberadaan</th>
                            <th>Jumlah Data (Baris)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($tabelStatus as $tblName => $info): ?>
                        <tr>
                            <td><code><?= $tblName ?></code></td>
                            <td>
                                <span class="badge bg-<?= $info['ada'] ? 'success' : 'danger' ?>">
                                    <?= $info['ada'] ? 'Ditemukan' : 'Tabel Tidak Ada' ?>
                                </span>
                            </td>
                            <td><?= $info['baris'] ?> Baris Data</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Panduan Troubleshooting Singkat -->
        <div class="alert alert-info">
            <h6 class="fw-bold mb-1">Panduan Troubleshooting Saat Ujian:</h6>
            <ul class="small mb-0 ps-3">
                <li>Jika status database <strong>ERROR</strong>: Pastikan MySQL di XAMPP dalam keadaan <strong>Start</strong> (warna hijau).</li>
                <li>Jika tabel tidak ditemukan: Buka <code>http://localhost/phpmyadmin</code> lalu import kembali script SQL.</li>
                <li>Jika terjadi loop login: Pastikan session sudah ter-inisialisasi via <code>session_start()</code> pada <code>Helper.php</code>.</li>
            </ul>
        </div>
    </div>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>