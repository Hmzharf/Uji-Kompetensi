    <?php
    require_once __DIR__ . '/../config/Database.php';

    class Petugas {
        private PDO $db;

        public function __construct() {
            $this->db = (new Database())->getConnection();
        }

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

        public function getAll(): array {
            return $this->db->query("SELECT * FROM tabel_petugas ORDER BY nama_petugas ASC")->fetchAll();
        }

        public function create(array $data): bool {
            $sql = "INSERT INTO tabel_petugas (id_petugas, username, password, nama_petugas, level) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$data['id_petugas'], $data['username'], $data['password'], $data['nama_petugas'], $data['level']]);
        }

        public function delete(string $id): bool {
            $stmt = $this->db->prepare("DELETE FROM tabel_petugas WHERE id_petugas = ?");
            return $stmt->execute([$id]);
        }
    }