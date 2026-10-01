<?php
session_start();
require_once 'config/database.php';
require_once 'classes/Transaksi.php';
require_once 'includes/functions.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$db = (new Database())->getConnection();
$transaksiObj = new Transaksi($db);

// Endpoint JSON untuk detail transaksi
if (isset($_GET['aksi']) && $_GET['aksi'] == 'detail' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $id = $_GET['id'];
    $detail = $transaksiObj->tampilDetailTransaksi($id); // Asumsi method ini ada
    echo json_encode($detail);
    exit;
}

$pageTitle = 'Riwayat Transaksi';
require_once 'includes/header.php';
$riwayat = $transaksiObj->tampilRiwayat();
?>

<div class="container mt-4">
    <h2>Riwayat Transaksi</h2>
    <table class="table table-bordered table-striped mt-3">
        <thead>
            <tr>
                <th>No Nota</th>
                <th>Kasir</th>
                <th>Total Harga</th>
                <th>Pajak</th>
                <th>Diskon</th>
                <th>Total Bayar</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($riwayat)): ?>
                <tr><td colspan="8" class="text-center">Belum ada riwayat transaksi.</td></tr>
            <?php else: ?>
                <?php foreach ($riwayat as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['no_nota']) ?></td>
                    <td><?= htmlspecialchars($r['nama_kasir'] ?? 'Kasir') ?></td>
                    <td><?= formatRupiah($r['total_harga']) ?></td>
                    <td><?= formatRupiah($r['pajak']) ?></td>
                    <td><?= formatRupiah($r['diskon']) ?></td>
                    <td><strong><?= formatRupiah($r['total_bayar']) ?></strong></td>
                    <td><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></td>
                    <td>
                        <button class="btn btn-sm btn-info btn-detail" data-id="<?= $r['id_transaksi'] ?>">Detail</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Transaksi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Menu</th>
                    <th>Harga</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody id="detail-body">
                <!-- Data AJAX -->
            </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
document.querySelectorAll('.btn-detail').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        fetch('riwayat.php?aksi=detail&id=' + id)
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('detail-body');
                tbody.innerHTML = '';
                if(data && data.length > 0) {
                    data.forEach(item => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${item.nama_menu}</td>
                            <td>${formatRupiahJS(item.harga_satuan)}</td>
                            <td>${item.qty}</td>
                            <td>${formatRupiahJS(item.subtotal)}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center">Data tidak ditemukan</td></tr>';
                }
                var myModal = new bootstrap.Modal(document.getElementById('modalDetail'));
                myModal.show();
            });
    });
});

function formatRupiahJS(angka) {
    return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
}
</script>

<?php require_once 'includes/footer.php'; ?>
