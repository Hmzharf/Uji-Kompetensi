<?php
$pageTitle = 'Cek Pembayaran';
require_once 'views/sidebar.php';
require_once 'classes/Pembayaran.php';
require_once 'classes/Siswa.php';

$pembayaranModel = new Pembayaran();
$siswaModel = new Siswa();

// Tangkap NISN dari URL (misal: ?nisn=7490279027)
$cari = isset($_GET['nisn']) ? trim($_GET['nisn']) : '';
$hasil = [];
$siswaInfo = null;

// Jika NISN terisi dari URL atau form, otomatis langsung cari datanya
if ($cari !== '') {
    $hasil = $pembayaranModel->getByNisn($cari);
    $siswaInfo = $siswaModel->getByNisn($cari);
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold">Cek Pembayaran Siswa</h3>
    <a href="index.php" class="btn btn-outline-secondary btn-sm">&laquo; Kembali ke Dashboard</a>
</div>

<!-- Form Pencarian (Otomatis Terisi jika ada parameter ?nisn=...) -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="cek_pembayaran.php" class="row g-2 align-items-center">
            <div class="col-md-9">
                <input type="text" name="nisn" class="form-control form-control-lg" 
                       placeholder="Masukkan NISN Siswa (contoh: 0051234567)..." 
                       value="<?= htmlspecialchars($cari) ?>" required autofocus>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">Cari Data</button>
            </div>
        </form>
    </div>
</div>

<!-- Hasil Pencarian Otomatis -->
<?php if ($cari !== ''): ?>
    <?php if ($siswaInfo): ?>
        <!-- Kartu Identitas Siswa -->
        <div class="card border-0 shadow-sm mb-4 bg-light">
            <div class="card-body">
                <h5 class="fw-bold text-primary mb-3">Informasi Siswa</h5>
                <div class="row">
                    <div class="col-md-3">
                        <small class="text-muted d-block">NISN / NIS</small>
                        <strong><?= htmlspecialchars($siswaInfo['nisn']) ?> / <?= htmlspecialchars($siswaInfo['nis']) ?></strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Nama Lengkap</small>
                        <strong><?= htmlspecialchars($siswaInfo['nama']) ?></strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Kelas</small>
                        <strong><?= htmlspecialchars($siswaInfo['nama_kelas']) ?></strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">No. Telepon</small>
                        <strong><?= htmlspecialchars($siswaInfo['no_telepon']) ?></strong>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Tabel Riwayat Pembayaran Siswa -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Riwayat Pembayaran untuk NISN: <?= htmlspecialchars($cari) ?></h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Bayar</th>
                            <th>Jatuh Tempo</th>
                            <th>Jumlah Bulan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($hasil) > 0): ?>
                            <?php $no = 1; foreach($hasil as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($row['tanggal_bayar'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($row['batas_pembayaran'])) ?></td>
                                <td><?= htmlspecialchars($row['jumlah_bulan']) ?> Bulan</td>
                                <td>
                                    <span class="badge bg-<?= $row['status'] === 'Lunas' ? 'success' : 'danger' ?> px-2 py-1">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Belum ada transaksi pembayaran yang tercatat untuk NISN <strong><?= htmlspecialchars($cari) ?></strong>.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once 'views/footer.php'; ?>
