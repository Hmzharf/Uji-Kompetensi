    <?php
    $pageTitle = 'Data Kelas';
    require_once 'views/sidebar.php';
    require_once 'classes/Kelas.php';

    $kelasModel = new Kelas();

    if (isset($_POST['tambah'])) {
        $res = $kelasModel->create($_POST['id_kelas'], $_POST['nama_kelas'], $_POST['kom_keahlian']);
        if ($res['status']) {
            setFlash($res['pesan'], 'success');
        } else {
            setFlash($res['pesan'], 'danger');
        }
        header('Location: kelas.php');
        exit;
    }

    if (isset($_GET['hapus'])) {
        $kelasModel->delete($_GET['hapus']);
        setFlash('Kelas berhasil dihapus!');
        header('Location: kelas.php');
        exit;
    }

    $kelas = $kelasModel->getAll();
    ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold">Data Kelas</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Kelas</button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID Kelas</th>
                        <th>Nama Kelas</th>
                        <th>Kompetensi Keahlian</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($kelas as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id_kelas']) ?></td>
                        <td><?= htmlspecialchars($row['nama_kelas']) ?></td>
                        <td><?= htmlspecialchars($row['kom_keahlian']) ?></td>
                        <td>
                            <a href="kelas.php?hapus=<?= $row['id_kelas'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus kelas ini?')">Hapus</a>
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
                    <h5 class="modal-title">Tambah Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ID Kelas</label>
                        <input type="text" name="id_kelas" class="form-control" required placeholder="Contoh: 4">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control" required placeholder="Contoh: XII RPL 3">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kompetensi Keahlian</label>
                        <input type="text" name="kom_keahlian" class="form-control" required placeholder="Contoh: Rekayasa Perangkat Lunak">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <?php require_once 'views/footer.php'; ?>