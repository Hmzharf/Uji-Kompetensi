<?php
require_once __DIR__ . '/../config/Database.php';

class Spp {
    private PDO $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function getAll(): array {
        return $this->db->query("SELECT * FROM tabel_spp ORDER BY tahun DESC")->fetchAll();
    }

    public function create(string $id, int $tahun, string $nominal): array {
        try {
            $stmt = $this->db->prepare("INSERT INTO tabel_spp (id_spp, tahun, nominal) VALUES (?, ?, ?)");
            $stmt->execute([$id, $tahun, $nominal]);
            return ['status' => true, 'pesan' => 'Tarif SPP berhasil ditambahkan!'];
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                if (strpos($e->getMessage(), 'PRIMARY') !== false) {
                    return ['status' => false, 'pesan' => 'Gagal: ID SPP (' . htmlspecialchars($id) . ') sudah digunakan!'];
                }
                return ['status' => false, 'pesan' => 'Gagal: Data SPP sudah terdaftar!'];
            }
            return ['status' => false, 'pesan' => 'Terjadi kesalahan sistem: ' . $e->getMessage()];
        }
    }

    public function delete(string $id): bool {
        $stmt = $this->db->prepare("DELETE FROM tabel_spp WHERE id_spp = ?");
        return $stmt->execute([$id]);
    }
}