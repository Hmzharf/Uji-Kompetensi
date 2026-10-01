<?php
    $pageTitle = 'Data Petugas';
    require_once 'views/sidebar.php';
    require_once 'classes/Petugas.php';

    if ($userLevel !== 'admin') {
        echo "<div class='alert alert-danger'>Akses ditolak! Halaman ini hanya untuk Admin.</div>";
        require_once 'views/footer.php';
        exit;
    }

    $petugasModel = new Petugas();

    if (isset($_POST['tambah'])) {
        $res = $petugasModel->create([
            'id_petugas'   => $_POST['id_petugas'],
            'username'     => $_POST['username'],
            'password'     => $_POST['password'],
            'nama_petugas' => $_POST['nama_petugas'],
            'level'        => $_POST['level']
        ]);
        if ($res['status']) {
            setFlash($res['pesan'], 'success');
        } else {
            setFlash($res['pesan'], 'danger');
        }
        header('Location: petugas.php');
        exit;
    }

    if (isset($_GET['hapus'])) {
        if ($_GET['hapus'] !== $_SESSION['user']['id_petugas']) {
            $petugasModel->delete($_GET['hapus']);
            setFlash('Petugas berhasil dihapus!');
        }
        header('Location: petugas.php');
        exit;
    }

    $petugas = $petugasModel->getAll();
    ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold">Data Petugas</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Petugas</button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID Petugas</th>
                        <th>Username</th>
                        <th>Nama Petugas</th>
                        <th>Level</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($petugas as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id_petugas']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['nama_petugas']) ?></td>
                        <td>
                            <span class="badge bg-<?= $row['level'] === 'admin' ? 'primary' : 'secondary' ?>">
                                <?= ucfirst($row['level']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($row['id_petugas'] !== $_SESSION['user']['id_petugas']): ?>
                                <a href="petugas.php?hapus=<?= $row['id_petugas'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus petugas ini?')">Hapus</a>
                            <?php else: ?>
                                <span class="text-muted small">Akun Anda</span>
                            <?php endif; ?>
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
                    <h5 class="modal-title">Tambah Petugas Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ID Petugas</label>
                        <input type="text" name="id_petugas" class="form-control" required placeholder="Contoh: 3">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_petugas" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Level Hak Akses</label>
                        <select name="level" class="form-select" required>
                            <option value="admin">Admin</option>
                            <option value="siswa">Siswa</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <?php require_once 'views/footer.php'; ?>
