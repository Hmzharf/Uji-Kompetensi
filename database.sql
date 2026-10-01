    CREATE DATABASE IF NOT EXISTS `db_PembayaranSppSiswa`;
    USE `db_PembayaranSppSiswa`;

    -- 1. TABEL KELAS
    CREATE TABLE `tabel_kelas` (
      `id_kelas` VARCHAR(11) PRIMARY KEY,
      `nama_kelas` VARCHAR(10) UNIQUE NOT NULL, -- UNIQUE agar bisa direlasikan
      `kom_keahlian` VARCHAR(50) NOT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;

    -- 2. TABEL SPP
    CREATE TABLE `tabel_spp` (
      `id_spp` VARCHAR(11) PRIMARY KEY,
      `tahun` INT(11) NOT NULL,
      `nominal` VARCHAR(40) NOT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;

    -- 3. TABEL SISWA
    CREATE TABLE `tabel_siswa` (
      `nisn` VARCHAR(10) PRIMARY KEY,
      `nis` VARCHAR(6) UNIQUE NOT NULL,
      `nama` VARCHAR(50) NOT NULL,
      `id_kelas` VARCHAR(11) NOT NULL,
      `nama_kelas` VARCHAR(10) NOT NULL,
      `alamat` TEXT NOT NULL,
      `no_telepon` VARCHAR(13) UNIQUE NOT NULL, -- UNIQUE agar bisa direlasikan ke tabel cek pembayaran
      `id_spp` VARCHAR(11) NOT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (`id_kelas`) REFERENCES `tabel_kelas`(`id_kelas`) ON UPDATE CASCADE ON DELETE CASCADE,
      FOREIGN KEY (`nama_kelas`) REFERENCES `tabel_kelas`(`nama_kelas`) ON UPDATE CASCADE ON DELETE CASCADE, -- Relasi nama_kelas
      FOREIGN KEY (`id_spp`) REFERENCES `tabel_spp`(`id_spp`) ON UPDATE CASCADE ON DELETE CASCADE
    ) ENGINE=InnoDB;

    -- 4. TABEL PEMBAYARAN
    CREATE TABLE `tabel_pembayaran` (
      `id_pembayaran` VARCHAR(11) PRIMARY KEY,
      `status` ENUM('Lunas', 'Belum Lunas') NOT NULL,
      `nisn` VARCHAR(10) NOT NULL,
      `tanggal_bayar` DATETIME NOT NULL,
      `tgl_terakhir_bayar` DATETIME NOT NULL,
      `batas_pembayaran` DATETIME NOT NULL,
      `jumlah_bulan` VARCHAR(11) NOT NULL,
      `id_spp` VARCHAR(11) NOT NULL,
      `nominal_bayar` VARCHAR(100) NOT NULL,
      `jumlah_bayar` VARCHAR(40) NOT NULL,
      `kembalian` VARCHAR(40) NOT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (`nisn`) REFERENCES `tabel_siswa`(`nisn`) ON UPDATE CASCADE ON DELETE CASCADE,
      FOREIGN KEY (`id_spp`) REFERENCES `tabel_spp`(`id_spp`) ON UPDATE CASCADE ON DELETE CASCADE
    ) ENGINE=InnoDB;

    -- 5. TABEL CEK PEMBAYARAN
    CREATE TABLE `tabel_cek_pembayaran` (
      `id_cek_pembayaran` VARCHAR(11) PRIMARY KEY,
      `nisn` VARCHAR(10) NOT NULL,
      `status` ENUM('Lunas', 'Belum Lunas') NOT NULL,
      `tanggal_bayar` DATETIME NOT NULL,
      `tgl_terakhir_bayar` DATETIME NOT NULL,
      `batas_pembayaran` DATETIME NOT NULL,
      `jumlah_bulan` VARCHAR(11) NOT NULL,
      `nama` VARCHAR(50) NOT NULL,
      `no_telepon` VARCHAR(13) NOT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (`nisn`) REFERENCES `tabel_siswa`(`nisn`) ON UPDATE CASCADE ON DELETE CASCADE,
      FOREIGN KEY (`no_telepon`) REFERENCES `tabel_siswa`(`no_telepon`) ON UPDATE CASCADE ON DELETE CASCADE -- Relasi no_telepon
    ) ENGINE=InnoDB;

    -- 6. TABEL PETUGAS
    CREATE TABLE `tabel_petugas` (
      `id_petugas` VARCHAR(11) PRIMARY KEY,
      `username` VARCHAR(50) UNIQUE NOT NULL,
      `password` VARCHAR(255) NOT NULL,
      `nama_petugas` VARCHAR(50) NOT NULL,
      `level` ENUM('admin', 'siswa') NOT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;

    -- ==========================================
    -- DATA AWAL
    -- ==========================================

    REPLACE INTO `tabel_petugas` (`id_petugas`, `username`, `password`, `nama_petugas`, `level`) VALUES
    ('1', 'admin', 'admin123', 'Admin', 'admin'),
    ('2', 'siswa', 'siswa123', 'Siswa', 'siswa');

    REPLACE INTO `tabel_kelas` (`id_kelas`, `nama_kelas`, `kom_keahlian`) VALUES
    ('1', '07TPLE016', 'Teknik Informatika'),
    ('2', 'XII RPL 1', 'Rekayasa Perangkat Lunak'),
    ('3', 'XII TKJ 1', 'Teknik Komputer dan Jaringan');

    REPLACE INTO `tabel_spp` (`id_spp`, `tahun`, `nominal`) VALUES
    ('1', 2024, '250000'),
    ('2', 2025, '300000');

    REPLACE INTO `tabel_siswa` (`nisn`, `nis`, `nama`, `id_kelas`, `nama_kelas`, `alamat`, `no_telepon`, `id_spp`) VALUES
    ('0051234567', '1001', 'Hamzah Arifianto', '1', '07TPLE016', 'Jl. Merdeka No. 10', '081234567890', '1'),
    ('0057654321', '1002', 'Suhendra Mandra', '1', '07TPLE016', 'Jl. Mawar No. 5', '081987654321', '1');