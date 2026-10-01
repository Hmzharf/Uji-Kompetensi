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

        public function create(string $id, string $nama, string $keahlian): bool {
            $stmt = $this->db->prepare("INSERT INTO tabel_kelas (id_kelas, nama_kelas, kom_keahlian) VALUES (?, ?, ?)");
            return $stmt->execute([$id, $nama, $keahlian]);
        }

        public function delete(string $id): bool {
            $stmt = $this->db->prepare("DELETE FROM tabel_kelas WHERE id_kelas = ?");
            return $stmt->execute([$id]);
        }
    }