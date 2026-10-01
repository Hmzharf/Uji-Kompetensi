<?php
    $pageTitle = 'Pembayaran SPP';
    require_once 'views/sidebar.php';
    require_once 'classes/Siswa.php';
    require_once 'classes/Pembayaran.php';

    $siswaModel = new Siswa();
    $pembayaranModel = new Pembayaran();

    if (isset($_POST['bayar'])) {
        $ok = $pembayaranModel->bayar($_POST);
        if ($ok) setFlash('Pembayaran berhasil diproses!');
        else setFlash('Gagal memproses pembayaran!', 'danger');
        header('Location: riwayat.php');
        exit;
    }

    $siswa = $siswaModel->getAll();
    $nisnTerpilih = isset($_GET['nisn']) ? $_GET['nisn'] : '';
    ?>

    <h3 class="fw-bold mb-3">Entri Pembayaran SPP</h3>

    <div class="row">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">ID Pembayaran</label>
                            <input type="text" name="id_pembayaran" class="form-control" value="TRX<?= time() ?>" required readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Siswa</label>
                            <select name="nisn" id="nisn" class="form-select" required onchange="updateTarif()">
                                <option value="">-- Pilih Siswa --</option>
                                <?php foreach($siswa as $s): ?>
                                    <option value="<?= $s['nisn'] ?>" data-spp="<?= $s['id_spp'] ?>" data-nominal="<?= $s['nominal'] ?>" <?= $nisnTerpilih === $s['nisn'] ? 'selected' : '' ?>>
                                        <?= $s['nisn'] ?> - <?= $s['nama'] ?> (<?= $s['nama_kelas'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="id_spp" id="id_spp">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Jumlah Bulan Dibayar</label>
                                <input type="number" name="jumlah_bulan" id="jumlah_bulan" class="form-control" value="1" min="1" required oninput="hitungTotal()">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Total Wajib Bayar (Rp)</label>
                                <input type="number" name="nominal_bayar" id="nominal_bayar" class="form-control" readonly required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Uang Diterima (Rp)</label>
                            <input type="number" name="jumlah_bayar" id="jumlah_bayar" class="form-control" placeholder="Masukkan jumlah uang" required oninput="hitungKembalian()">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Kembalian (Rp)</label>
                            <input type="text" id="kembalian_teks" class="form-control" readonly value="0">
                        </div>
                        <button type="submit" name="bayar" class="btn btn-primary w-100 py-2 fw-bold">Proses & Simpan Pembayaran</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    let tarifPerBulan = 0;
    function updateTarif() {
        const select = document.getElementById('nisn');
        const selected = select.options[select.selectedIndex];
        tarifPerBulan = parseFloat(selected.getAttribute('data-nominal')) || 0;
        document.getElementById('id_spp').value = selected.getAttribute('data-spp') || '';
        hitungTotal();
    }
    function hitungTotal() {
        const bulan = parseInt(document.getElementById('jumlah_bulan').value) || 1;
        const total = bulan * tarifPerBulan;
        document.getElementById('nominal_bayar').value = total;
        hitungKembalian();
    }
    function hitungKembalian() {
        const total = parseFloat(document.getElementById('nominal_bayar').value) || 0;
        const uang = parseFloat(document.getElementById('jumlah_bayar').value) || 0;
        const kembalian = uang - total;
        document.getElementById('kembalian_teks').value = 'Rp ' + (kembalian >= 0 ? kembalian.toLocaleString('id-ID') : '0 (Kurang)');
    }
    window.onload = function() {
        if (document.getElementById('nisn').value) {
            updateTarif();
        }
    };
    </script>

    <?php require_once 'views/footer.php'; ?>