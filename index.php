    <?php
    $pageTitle = 'Dashboard - Aplikasi SPP';
    require_once 'views/sidebar.php';
    require_once 'classes/Siswa.php';
    require_once 'classes/Pembayaran.php';

    $siswaModel = new Siswa();
    $pembayaranModel = new Pembayaran();

    $daftarSiswa = $siswaModel->getSiswaDenganStatus();
    $totalKas    = $pembayaranModel->getTotalKas();

    $totalSiswa = count($daftarSiswa);
    $totalLunas = 0;
    $totalBelumLunas = 0;

    foreach ($daftarSiswa as $siswa) {
        if ($siswa['status_bayar'] === 'Lunas') {
            $totalLunas++;
        } else {
            $totalBelumLunas++;
        }
    }
    ?>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h3 class="fw-bold mb-0">Dashboard Pembayaran SPP</h3>
            <small class="text-muted"><?= date('l, d F Y') ?> | Login: <strong><?= htmlspecialchars($_SESSION['user']['nama_lengkap']) ?></strong></small>
        </div>
        <div>
            <a href="siswa.php" class="btn btn-outline-primary me-2">Data Siswa</a>
            <a href="pembayaran.php" class="btn btn-primary">+ Entri Pembayaran</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm p-3 bg-primary text-white rounded-3">
                <span class="small text-uppercase">Total Seluruh Siswa</span>
                <h2 class="fw-bold my-1"><?= $totalSiswa ?></h2>
                <small class="text-white-50">Siswa Terdaftar</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm p-3 bg-success text-white rounded-3">
                <span class="small text-uppercase">Siswa Lunas</span>
                <h2 class="fw-bold my-1"><?= $totalLunas ?></h2>
                <small class="text-white-50">Sudah Menyelesaikan SPP</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm p-3 bg-danger text-white rounded-3">
                <span class="small text-uppercase">Siswa Belum Lunas</span>
                <h2 class="fw-bold my-1"><?= $totalBelumLunas ?></h2>
                <small class="text-white-50">Memiliki Tunggakan</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm p-3 bg-warning text-dark rounded-3">
                <span class="small text-uppercase">Total Kas Masuk</span>
                <h3 class="fw-bold my-1"><?= rupiah($totalKas) ?></h3>
                <small class="text-muted">Total Pembayaran Masuk</small>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Daftar Status Siswa (Lunas & Belum Lunas)</h5>
            <span class="badge bg-light text-dark border">Total: <?= $totalSiswa ?> Siswa</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NISN</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Tarif SPP</th>
                            <th>Tgl Terakhir Bayar</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($daftarSiswa) > 0): ?>
                            <?php $no = 1; foreach($daftarSiswa as $row):
                                $isLunas = ($row['status_bayar'] === 'Lunas');
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nisn']) ?></td>
                                <td><strong><?= htmlspecialchars($row['nama']) ?></strong></td>
                                <td><?= htmlspecialchars($row['nama_kelas']) ?></td>
                                <td><?= rupiah($row['nominal']) ?> (<?= $row['tahun'] ?>)</td>
                                <td>
                                    <?= $row['tgl_terakhir_bayar'] ? date('d/m/Y H:i', strtotime($row['tgl_terakhir_bayar'])) : '<span class="text-muted">-</span>' ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $isLunas ? 'success' : 'danger' ?>">
                                        <?= $isLunas ? 'Lunas' : 'Belum Lunas' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if (!$isLunas): ?>
                                        <a href="pembayaran.php?nisn=<?= urlencode($row['nisn']) ?>" class="btn btn-sm btn-outline-danger">Bayar Sekarang</a>
                                    <?php else: ?>
                                        <a href="cek_pembayaran.php?nisn=<?= urlencode($row['nisn']) ?>" class="btn btn-sm btn-outline-secondary">Lihat Detail</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Belum ada data siswa.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php require_once 'views/footer.php'; ?>