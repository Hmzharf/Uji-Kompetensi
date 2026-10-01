    <?php
    $pageTitle = 'Data Siswa';
    require_once 'views/sidebar.php';
    require_once 'classes/Siswa.php';
    require_once 'classes/Kelas.php';
    require_once 'classes/Spp.php';

    $siswaModel = new Siswa();
    $kelasModel = new Kelas();
    $sppModel   = new Spp();


    if (isset($_POST['tambah'])) {
        $result = $siswaModel->create([
            'nisn'       => trim($_POST['nisn']),
            'nis'        => trim($_POST['nis']),
            'nama'       => trim($_POST['nama']),
            'id_kelas'   => $_POST['id_kelas'],
            'alamat'     => trim($_POST['alamat']),
            'no_telepon' => trim($_POST['no_telepon']),
            'id_spp'     => $_POST['id_spp']
        ]);

    // Tampilkan pesan sukses jika berhasil, atau pesan merah jika ada duplikasi
    if ($result['status']) {
        setFlash($result['pesan'], 'success');
        } else {
            setFlash($result['pesan'], 'danger');
        }
        header('Location: siswa.php');
        exit;
    }

/**if (isset($_POST['tambah'])) {
        $result = $siswaModel->create([
            'nisn'       => trim($_POST['nisn']),
            'nis'        => trim($_POST['nis']),
            'nama'       => trim($_POST['nama']),
            'id_kelas'   => $_POST['id_kelas'],
            'alamat'     => trim($_POST['alamat']),
            'no_telepon' => trim($_POST['no_telepon']),
            'id_spp'     => $_POST['id_spp']
        ]);
        if ($result['status']) {
            setFlash($result['pesan'], 'success');
        } else {
            setFlash($result['pesan'], 'danger');
        }
        header('Location: siswa.php');
        exit;
    }
*/

    if (isset($_GET['hapus'])) {
        $siswaModel->delete($_GET['hapus']);
        setFlash('Siswa berhasil dihapus!');
        header('Location: siswa.php');
        exit;
    }

    $siswa = $siswaModel->getAll();
    $kelas = $kelasModel->getAll();
    $spp   = $sppModel->getAll();
    ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold">Data Siswa</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Siswa</button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>NISN</th>
                            <th>NIS</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>No Telepon</th>
                            <th>Tarif SPP</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($siswa as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nisn']) ?></td>
                            <td><?= htmlspecialchars($row['nis']) ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['nama_kelas']) ?></td>
                            <td><?= htmlspecialchars($row['no_telepon']) ?></td>
                            <td><?= rupiah($row['nominal']) ?> (<?= $row['tahun'] ?>)</td>
                            <td>
                                <a href="siswa.php?hapus=<?= $row['nisn'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus siswa ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Siswa Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">NISN (10 Digit)</label>
                            <input type="text" name="nisn" maxlength="10" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">NIS (6 Digit)</label>
                            <input type="text" name="nis" maxlength="6" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Kelas</label>
                        <select name="id_kelas" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach($kelas as $k): ?>
                                <option value="<?= $k['id_kelas'] ?>"><?= $k['nama_kelas'] ?> (<?= $k['kom_keahlian'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Tarif SPP</label>
                        <select name="id_spp" class="form-select" required>
                            <option value="">-- Pilih Tarif SPP --</option>
                            <?php foreach($spp as $sp): ?>
                                <option value="<?= $sp['id_spp'] ?>">Tahun <?= $sp['tahun'] ?> - <?= rupiah($sp['nominal']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">No Telepon</label>
                        <input type="text" name="no_telepon" maxlength="13" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <?php require_once 'views/footer.php'; ?>