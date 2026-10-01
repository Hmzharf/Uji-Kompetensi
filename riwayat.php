<?php
$pageTitle = 'Data Pembayaran';
require_once 'views/sidebar.php';
require_once 'classes/Pembayaran.php';

$pembayaranModel = new Pembayaran();
$query = $pembayaranModel->getAll();
?>

<h3 class="fw-bold mb-3">Data Pembayaran SPP</h3>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID TRX</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Tgl Bayar</th>
                        <th>Bulan</th>
                        <th>Wajib Bayar</th>
                        <th>Jumlah Bayar</th>
                        <th>Kembalian</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($query) > 0): ?>
                        <?php foreach($query as $row): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['id_pembayaran']) ?></strong></td>
                            <td><?= htmlspecialchars($row['nama']) ?> (<?= $row['nisn'] ?>)</td>
                            <td><?= htmlspecialchars($row['nama_kelas']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['tanggal_bayar'])) ?></td>
                            <td><?= htmlspecialchars($row['jumlah_bulan']) ?> bln</td>
                            <td><?= rupiah($row['nominal_bayar']) ?></td>
                            <td><?= rupiah($row['jumlah_bayar']) ?></td>
                            <td><?= rupiah($row['kembalian']) ?></td>
                            <td>
                                <span class="badge bg-<?= $row['status'] == 'Lunas' ? 'success' : 'danger' ?>">
                                    <?= $row['status'] ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">Belum ada data pembayaran yang tercatat.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'views/footer.php'; ?>