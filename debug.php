    <?php
    /**
     * DIAGNOSTIC & TROUBLESHOOTING TOOL
     * Unit Kompetensi 8: Melakukan Debugging & Troubleshooting
     */

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Cek otomatis ketersediaan file helper
    if (file_exists(__DIR__ . '/helpers/functions.php')) {
        require_once __DIR__ . '/helpers/functions.php';
    } elseif (file_exists(__DIR__ . '/helpers/functions.php')) {
        require_once __DIR__ . '/helpers/funtions.php';
    }

    require_once __DIR__ . '/config/Database.php';

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
        'detail' => 'Session ID: ' . (session_id() ?: 'Tidak Aktif')
    ];

    // 4. Cek Koneksi Basis Data
    $dbStatus = 'ERROR';
    $dbDetail = '';
    $tabelStatus = [];
    try {
        $databaseObj = new Database();
        $db = $databaseObj->getConnection();
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
                <a href="debug.php" class="btn btn-outline-primary me-2">Jalankan Unit debugging</a>
                <a href="test.php" class="btn btn-outline-primary me-2">Jalankan Unit testing</a>
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
                <li>Jika terjadi loop login: Pastikan session sudah ter-inisialisasi via <code>session_start()</code> pada <code>helpers/functions.php</code>.</li>
            </ul>
        </div>
    </div>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>