<?php
require_once __DIR__ . '/../config/Database.php';

class Kelas {
    private PDO $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function getAll(): array {
        return $this->db->query("SELECT * FROM tabel_kelas ORDER BY id_kelas ASC")->fetchAll();
    }

    public function create(string $id, string $nama, string $keahlian): array {
        try {
            $stmt = $this->db->prepare("INSERT INTO tabel_kelas (id_kelas, nama_kelas, kom_keahlian) VALUES (?, ?, ?)");
            $stmt->execute([$id, $nama, $keahlian]);
            return ['status' => true, 'pesan' => 'Kelas berhasil ditambahkan!'];
        } catch (PDOException $e) {
            // Tangani error duplikasi ID Kelas atau Nama Kelas (SQLSTATE 23000 / Error 1062)
            if ($e->getCode() == 23000) {
                if (strpos($e->getMessage(), 'PRIMARY') !== false) {
                    return ['status' => false, 'pesan' => 'Gagal: ID Kelas (' . htmlspecialchars($id) . ') sudah digunakan! Silakan gunakan ID lain.'];
                } elseif (strpos($e->getMessage(), 'nama_kelas') !== false) {
                    return ['status' => false, 'pesan' => 'Gagal: Nama Kelas (' . htmlspecialchars($nama) . ') sudah terdaftar!'];
                }
                return ['status' => false, 'pesan' => 'Gagal: Data kelas yang dimasukkan sudah ada!'];
            }
            return ['status' => false, 'pesan' => 'Terjadi kesalahan sistem: ' . $e->getMessage()];
        }
    }

    public function delete(string $id): bool {
        $stmt = $this->db->prepare("DELETE FROM tabel_kelas WHERE id_kelas = ?");
        return $stmt->execute([$id]);
    }
}