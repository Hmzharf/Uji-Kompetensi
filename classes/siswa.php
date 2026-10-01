<?php
require_once __DIR__ . '/../config/Database.php';
    /**
     * Class Siswa
     *
     * Model pengelola data induk siswa, relasi kelas, dan keterikatan tarif SPP
     */
    class Siswa {
        private PDO $db;

        public function __construct() {
            $this->db = (new Database())->getConnection();
        }
        /**
         * Mengambil semua siswa dengan JOIN tarif nominal SPP angkatan
         *
         * @return array Daftar siswa lengkap dengan nominal SPP
         */
        public function getAll(): array {
            $sql = "SELECT s.*, sp.nominal, sp.tahun FROM tabel_siswa s
                    LEFT JOIN tabel_spp sp ON s.id_spp = sp.id_spp
                    ORDER BY s.nama ASC";
            return $this->db->query($sql)->fetchAll();
        }
        /**
         * Mengambil data siswa beserta status pembayaran terakhirnya (Lunas / Belum Lunas)
         * Digunakan secara spesifik oleh halaman Dashboard (index.php)
         *
         * Menggunakan subquery COALESCE untuk memastikan siswa yang belum
         * memiliki transaksi otomatis berstatus 'Belum Lunas'.
         *
         * @return array Daftar siswa beserta kolom komputasi 'status_bayar' dan 'tgl_terakhir_bayar'
         */
        public function getSiswaDenganStatus(): array {
            $sql = "SELECT s.*, sp.nominal, sp.tahun,
                    COALESCE(
                        (SELECT p.status FROM tabel_pembayaran p WHERE p.nisn = s.nisn ORDER BY p.tanggal_bayar DESC LIMIT 1),
                        'Belum Lunas'
                    ) AS status_bayar,
                    (SELECT p.tanggal_bayar FROM tabel_pembayaran p WHERE p.nisn = s.nisn ORDER BY p.tanggal_bayar DESC LIMIT 1) AS tgl_terakhir_bayar
                    FROM tabel_siswa s
                    LEFT JOIN tabel_spp sp ON s.id_spp = sp.id_spp
                    ORDER BY s.nama ASC";
            return $this->db->query($sql)->fetchAll();
        }
        /**
         * Mengambil data siswa dengan NISN
         *
         * @param string $nisn
         * @return array|null mencari perkulumpulan nisn, ada dan tidak ada dimana
         */
        public function getByNisn(string $nisn) {
            $stmt = $this->db->prepare("SELECT * FROM tabel_siswa WHERE nisn = ?");
            $stmt->execute([$nisn]);
            return $stmt->fetch();
        }
        /**
         * Mendaftarkan data siswa baru dan mengaitkannya ke nama kelas serta tarif SPP
         *
         * @param array $data Array berisi [nisn, nis, nama, id_kelas, alamat, no_telepon, id_spp]
         * @return bool True jika berhasil diinput, False jika gagal
         */
        public function create(array $data): array {
            try {
                $qK = $this->db->prepare("SELECT nama_kelas FROM tabel_kelas WHERE id_kelas = ?");
                $qK->execute([$data['id_kelas']]);
                $nama_kelas = $qK->fetchColumn();

                $sql = "INSERT INTO tabel_siswa (nisn, nis, nama, id_kelas, nama_kelas, alamat, no_telepon, id_spp)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $data['nisn'], $data['nis'], $data['nama'], $data['id_kelas'],
                    $nama_kelas, $data['alamat'], $data['no_telepon'], $data['id_spp']
                ]);
                return ['status' => true, 'pesan' => 'Siswa berhasil ditambahkan!'];
            } catch (PDOException $e) {
                // Tangani error duplikasi data (SQLSTATE 23000 / Error 1062)
                if ($e->getCode() == 23000) {
                    if (strpos($e->getMessage(), 'PRIMARY') !== false) {
                        return ['status' => false, 'pesan' => 'Gagal: NISN (' . htmlspecialchars($data['nisn']) . ') sudah terdaftar di sistem!'];
                    } elseif (strpos($e->getMessage(), 'nis') !== false) {
                        return ['status' => false, 'pesan' => 'Gagal: NIS (' . htmlspecialchars($data['nis']) . ') sudah digunakan siswa lain!'];
                    } elseif (strpos($e->getMessage(), 'no_telepon') !== false) {
                        return ['status' => false, 'pesan' => 'Gagal: Nomor Telepon (' . htmlspecialchars($data['no_telepon']) . ') sudah terdaftar!'];
                    }
                    return ['status' => false, 'pesan' => 'Gagal: Terdapat data duplikat yang melanggar aturan database!'];
                }
                return ['status' => false, 'pesan' => 'Terjadi kesalahan sistem: ' . $e->getMessage()];
            }
        }
        /**
         * Menghapus data siswa berdasarkan NISN (Otomatis CASCADE ke transaksi anak)
         *
         * @param string $nisn Nomor Induk Siswa Nasional
         * @return bool Status keberhasilan query
         */
        public function delete(string $nisn): bool {
            $stmt = $this->db->prepare("DELETE FROM tabel_siswa WHERE nisn = ?");
            return $stmt->execute([$nisn]);
        }
    }