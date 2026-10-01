<?php
require_once __DIR__ . '/../config/Database.php';
    /**
     * Class Petugas
     *
     * Mengelola entitas pengguna (petugas & admin), autentikasi login,
     * enkripsi kata sandi, dan CRUD akun sistem.
     */
    class Petugas {
        private PDO $db;
        /**
         * Inisialisasi dependensi koneksi database PDO
         */
        public function __construct() {
            $this->db = (new Database())->getConnection();
        }
        /**
         * Memverifikasi kredensial login akun petugas/admin
         *
         * Mendukung verifikasi menggunakan password_verify() (hash aman)
         * maupun plain text untuk kompatibilitas data awal.
         *
         * @param string $username Nama pengguna yang diinput
         * @param string $password Kata sandi yang diinput
         * @return array|false Mengembalikan array data pengguna jika valid, atau false jika salah
         */
        public function login(string $username, string $password) {
            $stmt = $this->db->prepare("SELECT * FROM tabel_petugas WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user) {
                if (password_verify($password, $user['password']) || $password === $user['password']) {
                    return $user;
                }
            }
            return false;
        }
        /**
         * Mengambil Seluruh daftar data petugas dari tabel_petugas
         * 
         * @return array Sekumpulan data record petugas terurut berdasarkan Nama
         */
        public function getAll(): array {
            return $this->db->query("SELECT * FROM tabel_petugas ORDER BY nama_petugas ASC")->fetchAll();
        }
        /**
         * Manambahkan akun petugas/admin baru ke basis data
         * 
         * @param array $data array asosiatif berisi [id_petugas, username, pasword, nama petugas, level]
         * @return bool true jika berhasil disimpan, false jika gagal
         */
        public function create(array $data): array {
            try {
                $sql = "INSERT INTO tabel_petugas (id_petugas, username, password, nama_petugas, level) VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$data['id_petugas'], $data['username'], $data['password'], $data['nama_petugas'], $data['level']]);
                return ['status' => true, 'pesan' => 'Petugas berhasil ditambahkan!'];
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    if (strpos($e->getMessage(), 'PRIMARY') !== false) {
                        return ['status' => false, 'pesan' => 'Gagal: ID Petugas (' . htmlspecialchars($data['id_petugas']) . ') sudah digunakan!'];
                    } elseif (strpos($e->getMessage(), 'username') !== false) {
                        return ['status' => false, 'pesan' => 'Gagal: Username (' . htmlspecialchars($data['username']) . ') sudah dipakai akun lain!'];
                    }
                    return ['status' => false, 'pesan' => 'Gagal: Terjadi duplikasi data akun petugas!'];
                }
                return ['status' => false, 'pesan' => 'Terjadi kesalahan sistem: ' . $e->getMessage()];
            }
        }
        /**
         * Menghapus record akun petugas berdasarkan PK (id_petugas)
         * @param string $id ID Petugas yang akan dihapus
         * @return bool True jika berhasil dihapus, false jika gagal
         */
        public function delete(string $id): bool {
            $stmt = $this->db->prepare("DELETE FROM tabel_petugas WHERE id_petugas = ?");
            return $stmt->execute([$id]);
        }
    }