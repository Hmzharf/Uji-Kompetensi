<?php
$pageTitle = 'Data SPP';
require_once 'views/sidebar.php';
require_once 'classes/Spp.php';

$sppModel = new Spp();

if (isset($_POST['tambah'])) {
    $res = $sppModel->create($_POST['id_spp'], (int)$_POST['tahun'], $_POST['nominal']);
    if ($res['status']) {
        setFlash($res['pesan'], 'success');
    } else {
        setFlash($res['pesan'], 'danger');
    }
    header('Location: spp.php');
    exit;
}

if (isset($_GET['hapus'])) {
    $sppModel->delete($_GET['hapus']);
    setFlash('Data SPP berhasil dihapus!');
    header('Location: spp.php');
    exit;
}

$spp = $sppModel->getAll();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold">Data SPP</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah SPP</button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID SPP</th>
                    <th>Tahun</th>
                    <th>Nominal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($spp as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id_spp']) ?></td>
                    <td><?= htmlspecialchars($row['tahun']) ?></td>
                    <td><?= rupiah($row['nominal']) ?></td>
                    <td>
                        <a href="spp.php?hapus=<?= $row['id_spp'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus SPP ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Tarif SPP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">ID SPP</label>
                    <input type="text" name="id_spp" class="form-control" required placeholder="Contoh: 3">
                </div>
                <div class="mb-3">
                    <label class="form-label">Tahun</label>
                    <input type="number" name="tahun" class="form-control" required value="<?= date('Y') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nominal (Rp)</label>
                    <input type="number" name="nominal" class="form-control" required placeholder="Contoh: 350000">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'views/footer.php'; ?>
