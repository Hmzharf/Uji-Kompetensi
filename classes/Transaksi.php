<?php
/**
 * Class Transaksi
 * Menangani operasi database untuk sistem transaksi kasir warung.
 */
class Transaksi {
    /**
     * @var PDO
     */
    private PDO $db;

    /**
     * Constructor Transaksi
     * 
     * @param PDO $db Koneksi PDO database
     */
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Simpan 1 transaksi lengkap sekaligus (header + semua detail)
     * Menggunakan konsep Database Transaction
     * 
     * @param array $data Data header transaksi (tanggal, id_user, total, dll)
     * @param array $items array of ['id_menu', 'qty', 'harga_satuan', 'subtotal']
     * @return bool True jika berhasil semua, False jika gagal (dan otomatis di-rollback)
     */
    public function simpanTransaksi(array $data, array $items): bool {
        try {
            // Mulai transaksi database
            $this->db->beginTransaction();

            // 1. Simpan Header Transaksi
            $queryHeader = "INSERT INTO transaksi (tanggal_transaksi, id_user, total_bayar, uang_bayar, uang_kembali, catatan) 
                            VALUES (?, ?, ?, ?, ?, ?)";
            $stmtHeader = $this->db->prepare($queryHeader);
            $stmtHeader->execute([
                $data['tanggal_transaksi'],
                $data['id_user'],
                $data['total_bayar'],
                $data['uang_bayar'],
                $data['uang_kembali'],
                $data['catatan'] ?? ''
            ]);

            // Dapatkan ID transaksi yang baru saja disisipkan
            $idTransaksi = $this->db->lastInsertId();

            // 2. Simpan Detail Transaksi dan Kurangi Stok Menu
            $queryDetail = "INSERT INTO detail_transaksi (id_transaksi, id_menu, qty, harga_satuan, subtotal) 
                            VALUES (?, ?, ?, ?, ?)";
            $stmtDetail = $this->db->prepare($queryDetail);

            $queryKurangiStok = "UPDATE menu SET stok = stok - ? WHERE id_menu = ?";
            $stmtKurangiStok = $this->db->prepare($queryKurangiStok);

            foreach ($items as $item) {
                // Insert ke tabel detail
                $stmtDetail->execute([
                    $idTransaksi,
                    $item['id_menu'],
                    $item['qty'],
                    $item['harga_satuan'],
                    $item['subtotal']
                ]);

                // Update stok di tabel menu
                $stmtKurangiStok->execute([
                    $item['qty'],
                    $item['id_menu']
                ]);
            }

            // Jika semua langkah di atas berhasil, commit/simpan permanen ke database
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            // Jika ada satu saja yang gagal, batalkan semua (rollback)
            $this->db->rollBack();
            error_log("Error simpanTransaksi: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ambil semua riwayat transaksi dengan JOIN ke users
     * 
     * @return array
     */
    public function tampilRiwayat(): array {
        $query = "SELECT t.*, u.nama_lengkap AS nama_kasir 
                  FROM transaksi t 
                  LEFT JOIN users u ON t.id_user = u.id_user 
                  ORDER BY t.tanggal_transaksi DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil detail 1 transaksi dengan JOIN ke menu
     * 
     * @param int $idTransaksi
     * @return array
     */
    public function ambilDetail(int $idTransaksi): array {
        $query = "SELECT dt.*, m.nama_menu, m.kode_menu 
                  FROM detail_transaksi dt 
                  JOIN menu m ON dt.id_menu = m.id_menu 
                  WHERE dt.id_transaksi = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$idTransaksi]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Hitung total pendapatan hari ini
     * 
     * @return int
     */
    public function totalHariIni(): int {
        $query = "SELECT SUM(total_bayar) as total FROM transaksi WHERE DATE(tanggal_transaksi) = CURDATE()";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ? (int) $result['total'] : 0;
    }

    /**
     * Hitung jumlah transaksi hari ini
     * 
     * @return int
     */
    public function jumlahTransaksiHariIni(): int {
        $query = "SELECT COUNT(*) as jumlah FROM transaksi WHERE DATE(tanggal_transaksi) = CURDATE()";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['jumlah'] ? (int) $result['jumlah'] : 0;
    }
}
