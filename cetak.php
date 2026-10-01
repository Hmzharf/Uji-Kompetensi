<?php
    require_once 'helper/Helper.php';
    require_once 'config/Database.php';

    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }

    $id = isset($_GET['id']) ? trim($_GET['id']) : '';
    $db = (new Database())->getConnection();

    $stmt = $db->prepare("SELECT p.*, s.nama, s.nis, s.nama_kelas, sp.tahun
        FROM tabel_pembayaran p
        JOIN tabel_siswa s ON p.nisn = s.nisn
        JOIN tabel_spp sp ON p.id_spp = sp.id_spp
        WHERE p.id_pembayaran = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    if (!$data) {
        die("Transaksi tidak ditemukan!");
    }
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Struk Pembayaran SPP - <?= htmlspecialchars($data['id_pembayaran']) ?></title>
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <style>
            body { background: #fff; font-family: monospace; }
            .struk-card { max-width: 420px; margin: 30px auto; border: 1px dashed #000; padding: 20px; }
            @media print {
                .no-print { display: none; }
                .struk-card { border: none; }
            }
        </style>
    </head>
    <body>

    <div class="struk-card">
        <div class="text-center mb-3">
            <h5 class="fw-bold mb-0">SMK BISA HEBAT</h5>
            <small>Bukti Pembayaran SPP Siswa</small>
            <hr class="my-2">
        </div>

        <table class="table table-sm table-borderless small mb-2">
            <tr>
                <td>No. Transaksi</td>
                <td>: <strong><?= $data['id_pembayaran'] ?></strong></td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>: <?= date('d/m/Y H:i', strtotime($data['tanggal_bayar'])) ?></td>
            </tr>
            <tr>
                <td>NISN / NIS</td>
                <td>: <?= $data['nisn'] ?> / <?= $data['nis'] ?></td>
            </tr>
            <tr>
                <td>Nama Siswa</td>
                <td>: <strong><?= htmlspecialchars($data['nama']) ?></strong></td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>: <?= htmlspecialchars($data['nama_kelas']) ?></td>
            </tr>
            <tr>
                <td>SPP Tahun</td>
                <td>: <?= $data['tahun'] ?></td>
            </tr>
            <tr>
                <td>Jumlah Bulan</td>
                <td>: <?= $data['jumlah_bulan'] ?> Bulan</td>
            </tr>
        </table>

        <hr class="my-2">

        <table class="table table-sm table-borderless small mb-3">
            <tr>
                <td>Total Wajib Bayar</td>
                <td class="text-end"><?= rupiah($data['nominal_bayar']) ?></td>
            </tr>
            <tr>
                <td>Uang Diterima</td>
                <td class="text-end"><?= rupiah($data['jumlah_bayar']) ?></td>
            </tr>
            <tr>
                <td>Kembalian</td>
                <td class="text-end"><?= rupiah($data['kembalian']) ?></td>
            </tr>
            <tr class="fw-bold">
                <td>Status</td>
                <td class="text-end"><?= strtoupper($data['status']) ?></td>
            </tr>
        </table>

        <div class="text-center small mt-4">
            <p class="mb-1">Terima Kasih atas Pembayaran Anda</p>
            <small class="text-muted">Simpan struk ini sebagai bukti pembayaran sah.</small>
        </div>

        <div class="mt-4 no-print text-center">
            <button onclick="window.print()" class="btn btn-primary btn-sm me-2">Cetak Struk</button>
            <a href="riwayat.php" class="btn btn-secondary btn-sm">Kembali</a>
        </div>
    </div>

    </body>
    </html>