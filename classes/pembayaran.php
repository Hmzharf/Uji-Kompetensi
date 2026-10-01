    <?php
    require_once __DIR__ . '/../config/Database.php';

    class Pembayaran {
        private PDO $db;

        public function __construct() {
            $this->db = (new Database())->getConnection();
        }

        public function getAll(): array {
            $sql = "SELECT p.*, s.nama, s.nama_kelas FROM tabel_pembayaran p
                    JOIN tabel_siswa s ON p.nisn = s.nisn
                    ORDER BY p.tanggal_bayar DESC";
            return $this->db->query($sql)->fetchAll();
        }

        public function getByNisn(string $nisn): array {
            $stmt = $this->db->prepare("SELECT * FROM tabel_cek_pembayaran WHERE nisn = ? ORDER BY tanggal_bayar DESC");
            $stmt->execute([$nisn]);
            return $stmt->fetchAll();
        }

        public function bayar(array $data): bool {
            $tanggal_bayar      = date('Y-m-d H:i:s');
            $tgl_terakhir_bayar = date('Y-m-d H:i:s');
            $batas_pembayaran   = date('Y-m-10 23:59:59');
            $nominal_bayar      = (float)$data['nominal_bayar'];
            $jumlah_bayar       = (float)$data['jumlah_bayar'];
            $kembalian          = $jumlah_bayar - $nominal_bayar;
            $status             = ($jumlah_bayar >= $nominal_bayar) ? 'Lunas' : 'Belum Lunas';

            $sql1 = "INSERT INTO tabel_pembayaran
                     (id_pembayaran, status, nisn, tanggal_bayar, tgl_terakhir_bayar, batas_pembayaran, jumlah_bulan, id_spp, nominal_bayar, jumlah_bayar, kembalian)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt1 = $this->db->prepare($sql1);
            $ok = $stmt1->execute([
                $data['id_pembayaran'], $status, $data['nisn'], $tanggal_bayar,
                $tgl_terakhir_bayar, $batas_pembayaran, $data['jumlah_bulan'],
                $data['id_spp'], $nominal_bayar, $jumlah_bayar, $kembalian
            ]);

            $qS = $this->db->prepare("SELECT nama, no_telepon FROM tabel_siswa WHERE nisn = ?");
            $qS->execute([$data['nisn']]);
            $siswa = $qS->fetch();

            $sql2 = "INSERT INTO tabel_cek_pembayaran
                     (id_cek_pembayaran, nisn, status, tanggal_bayar, tgl_terakhir_bayar, batas_pembayaran, jumlah_bulan, nama, no_telepon)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute([
                'CK' . time(), $data['nisn'], $status, $tanggal_bayar,
                $tgl_terakhir_bayar, $batas_pembayaran, $data['jumlah_bulan'],
                $siswa['nama'], $siswa['no_telepon']
            ]);

            return $ok;
        }

        public function getTotalKas(): float {
            $q = $this->db->query("SELECT SUM(CAST(jumlah_bayar AS DECIMAL)) as total FROM tabel_pembayaran")->fetch();
            return $q['total'] ? (float)$q['total'] : 0.0;
        }
    }