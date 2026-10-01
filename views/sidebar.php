   <?php
    require_once __DIR__ . '/../helpers/functions.php';

    $currentPage = basename($_SERVER['PHP_SELF']);
    if (!isset($_SESSION['user']) && $currentPage !== 'login.php') {
        header('Location: login.php');
        exit;
    }

    $pageTitle = isset($pageTitle) ? $pageTitle : 'Aplikasi SPP';
    $userLevel = isset($_SESSION['user']['level']) ? $_SESSION['user']['level'] : '';
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($pageTitle) ?></title>
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <style>
            body { min-height: 100vh; background-color: #f8f9fa; }
            .sidebar {
                width: 250px; height: 100vh; position: fixed; top: 0; left: 0;
                background-color: #0d6efd; color: #fff; display: flex; flex-direction: column; z-index: 1000;
            }
            .sidebar .brand {
                padding: 20px 15px; font-size: 1.1rem; font-weight: bold;
                border-bottom: 1px solid rgba(255, 255, 255, 0.15); text-align: center;
            }
            .sidebar-menu { list-style: none; padding: 0; margin: 15px 0 0 0; flex-grow: 1; overflow-y: auto; }
            .sidebar-menu .nav-link {
                color: rgba(255, 255, 255, 0.85); padding: 10px 20px; display: block; text-decoration: none; transition: 0.2s;
            }
            .sidebar-menu .nav-link:hover, .sidebar-menu .nav-link.active {
                color: #fff; background-color: rgba(255, 255, 255, 0.18); border-left: 4px solid #fff;
            }
            .sidebar .user-info {
                padding: 15px 20px; border-top: 1px solid rgba(255, 255, 255, 0.15); background-color: rgba(0, 0, 0, 0.05);
            }
            .main-content { margin-left: 250px; padding: 25px; }
            @media (max-width: 768px) {
                .sidebar { position: static; width: 100%; height: auto; }
                .main-content { margin-left: 0; padding: 15px; }
            }
        </style>
    </head>
    <body>

    <div class="sidebar">
        <div class="brand">Aplikasi SPP Sekolah</div>
        <ul class="sidebar-menu">
            <li>
                <a class="nav-link <?= $currentPage == 'index.php' ? 'active' : '' ?>" href="index.php">Dashboard</a>
            </li>
            <li>
                <a class="nav-link <?= $currentPage == 'siswa.php' ? 'active' : '' ?>" href="siswa.php">Data Siswa</a>
            </li>
            <li>
                <a class="nav-link <?= $currentPage == 'kelas.php' ? 'active' : '' ?>" href="kelas.php">Data Kelas</a>
            </li>
            <li>
                <a class="nav-link <?= $currentPage == 'cekPembayaran.php' ? 'active' : '' ?>" href="cekPembayaran.php">Cek Pembayaran</a>
            </li>
            <li>
                <a class="nav-link <?= $currentPage == 'pembayaran.php' ? 'active' : '' ?>" href="pembayaran.php">Pembayaran</a>
            </li>
            <li>
                <a class="nav-link <?= $currentPage == 'riwayat.php' ? 'active' : '' ?>" href="riwayat.php">Data Pembayaran</a>
            </li>
            <?php if ($userLevel === 'admin'): ?>
            <li>
                <a class="nav-link <?= $currentPage == 'petugas.php' ? 'active' : '' ?>" href="petugas.php">Data Petugas</a>
            </li>
            <?php endif; ?>
        </ul>

        <div class="user-info">
            <div class="small text-light">Halo, <strong><?= htmlspecialchars($_SESSION['user']['nama_lengkap']) ?></strong> (<?= ucfirst($userLevel) ?>)</div>
            <div class="mt-2">
                <a href="logout.php" class="btn btn-sm btn-danger w-100" onclick="return confirm('Yakin ingin logout?')">Logout</a>
            </div>
        </div>
    </div>

    <div class="main-content">
    <?= getFlash(); ?>