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

        public function create(string $id, int $tahun, string $nominal): bool {
            $stmt = $this->db->prepare("INSERT INTO tabel_spp (id_spp, tahun, nominal) VALUES (?, ?, ?)");
            return $stmt->execute([$id, $tahun, $nominal]);
        }

        public function delete(string $id): bool {
            $stmt = $this->db->prepare("DELETE FROM tabel_spp WHERE id_spp = ?");
            return $stmt->execute([$id]);
        }
    }